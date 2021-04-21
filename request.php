<?php

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
require_once __DIR__ . '/workerman/Autoloader.php';


/*
*请求数据函数
$sub_str type: string e.g market.btcusdt.kline.1min 具体请查看api
$callback type: function 回调函数，当获得数据时会调用
*/
function request($callback, $req_str="market.btcusdt.kline.1min") {
    $type = 'mysql'; //数据库类型
    $db_name = 'test'; //数据库名
    $host = '127.0.0.1';
    $username = 'root';
    $password = 'mysql';

    $dsn = "$type:host=$host;dbname=$db_name";
    global $pdo;
    try {
//建立持久化的PDO连接
        $pdo = new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => true));
    } catch (Exception $e) {
        die('连接数据库失败!');
    }

    $GLOBALS['req_str'] = $req_str;
    $GLOBALS['callback'] = $callback;
    $worker = new Worker();
    $worker->onWorkerStart = function($worker) {
        // ssl需要访问443端口
        $con = new AsyncTcpConnection('ws://api.huobi.pro:443/ws');

        // 设置以ssl加密方式访问，使之成为wss
        $con->transport = 'ssl';
        \Workerman\Lib\Timer::add(5,function () use($con){
            $data = json_encode([
                'req' => $GLOBALS['req_str'],
                'id' => 'id' . time()
            ]);
            $con->send($data);
        });
//        $con->onConnect = function($con) {
//            $data = json_encode([
//                'req' => $GLOBALS['req_str'],
//                'id' => 'id' . time()
//            ]);
//            $con->send($data);
//        };

        $con->onMessage = function($con, $data) {
            global $pdo;
            $data = gzdecode($data);
            $data = json_decode($data, true);
            if(isset($data['ping'])) {
                $con->send(json_encode([
                    "pong" => $data['ping']
                ]));
            }else{
                $tick = json_encode($data);
                $pdo->query("insert into huobi(ts,ch,tick) values('1','1','{$tick}')");
                call_user_func_array($GLOBALS['callback'], array($data));
            }
        };

        $con->connect();
    };

    Worker::runAll();
}
request(function($data){
    print_r($data);
},'market.link3lusdt.detail');
