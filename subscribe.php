<?php
use core\lib\model;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
require_once __DIR__ . '/vendor/autoload.php';
define('ROOT',__DIR__);
define('CORE',ROOT.'/core');
define('APP',ROOT.'/app');
define('CONTROLLER','\app');
define('DEBUG',true);
ini_set('date.timezone','Asia/Shanghai');
/*
*订阅数据函数
$sub_str type: string e.g market.btcusdt.kline.1min 具体请查看api
$callback type: function 回调函数，当获得数据时会调用
*/
function subscribe($callback, $sub_str=[]) {
    $GLOBALS['sub_str'] = $sub_str;
    $GLOBALS['callback'] = $callback;
    $worker = new Worker();
    $GLOBALS['reminds'] = [];
    $worker->onWorkerStart = function($worker) {
        // ssl需要访问443端口
        $con = new AsyncTcpConnection('ws://api.huobi.pro:443/ws');
        // 设置以ssl加密方式访问，使之成为wss
        $con->transport = 'ssl';
        $reminds = model::getInstance()->select("remind",['name'],['status'=>1,'GROUP'=>'name']);
        $con->onConnect = function($con) use($reminds) {
            $all_symbol = array_column($reminds,'name');
            $GLOBALS['reminds'] = $all_symbol;
            foreach ($all_symbol as $symbol){
                $data = json_encode([
                    'sub' => "market.".$symbol.".detail",
                    'id' => 'depth' . time()
                ]);
                $con->send($data);
            }
            //设置定时器，定时增加新的关注
            \Workerman\Lib\Timer::add(10,function () use($con){
                $new_symbol = model::getInstance()->select("remind",['name'],['status'=>1,'GROUP'=>'name']);
                $old_symbol = $GLOBALS['reminds'];
                if($new_symbol){
                    $new_symbol = array_column($new_symbol,'name');
                    /**
                     * 这里比的数组差值，返回的数组为要新订阅的symbol
                     */
                    $add_diff = array_diff($new_symbol,$old_symbol);
                    /**
                     * 这里比的数组差值，返回的数组为要取消订阅的symbol
                     */
                    $remove_diff = array_diff($old_symbol,$new_symbol);
                    if($add_diff){
                        foreach ($add_diff as $symbol){
                            $GLOBALS['reminds'][] = $symbol;
                            $data = json_encode([
                                'sub' => "market.".$symbol.".detail",
                                'id' => 'depth' . time()
                            ]);
                            $con->send($data);
                        }
                    }
                    if($remove_diff){
                        foreach ($remove_diff as $symbol){
                            $GLOBALS['reminds'] = array_diff($GLOBALS['reminds'],[$symbol]);//这里的array_diff做为删除使用
                            $data = json_encode([
                                'unsub' => "market.".$symbol.".detail",
                                'id' => 'depth' . time()
                            ]);
                            $con->send($data);
                        }
                    }
                }
            });
        };

        $con->onMessage = function($con, $data) {
            $data = gzdecode($data);
            $data = json_decode($data, true);
            if(isset($data['ping'])) {
                $con->send(json_encode([
                    "pong" => $data['ping']
                ]));
            }else{

                call_user_func_array($GLOBALS['callback'], array($data));
            }
        };

        $con->connect();
    };

    Worker::runAll();
}

subscribe(function($data){
    new \service\DataHandle($data);
});
