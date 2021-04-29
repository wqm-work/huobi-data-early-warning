<?php

namespace app\controller;
use app\models\symbolDataHandle;
use core\config;
use core\lib\model;
use service\pushMessage;

class indexController
{
    static $mysql ;
    function __construct()
    {
        self::$mysql = model::getInstance();
    }

    public function follow(){
        $name = $_POST['name'];
        $user_id = $_POST['user_id'];
        $alias = $_POST['alias'];

        self::$mysql->insert('own_list',[
            "name"=>$name,
            "alias"=>$alias,
            "user_id"=>$user_id,
            "create_time"=>date("Y-m-d H:i:s"),
        ]);
        $id = self::$mysql->pdo->lastInsertId();
        return ['code'=>200,'follow_id'=>$id];
    }
    function unFollow(){
        $follow_id = $_POST['follow_id'];
        self::$mysql->delete('own_list',['id'=>$follow_id]);
        return ['code'=>200];
    }
    function getFollow(){
        $user_id = $_GET['user_id'];
        $data = self::$mysql->select("own_list",'*',["user_id" => $user_id]);
        return ['code'=>200,'data'=>$data];
    }

    function setRemind(){
        $user_id = $_POST['user_id'];
        $type = $_POST['type'];
        $name = $_POST['name'];
        $num = $_POST['num'];
        $status = $_POST['status'];
        if(isset($_POST['id']) && $_POST['id']){
            self::$mysql->update('remind',[
                "name"=>$name,
                "user_id"=>$user_id,
                "type"=>$type,
                "num"=>$num,
                "status"=>$status,
                "create_time"=>date("Y-m-d H:i:s"),
            ],['id'=>$_POST['id']]);
        }else{
            self::$mysql->insert('remind',[
                "name"=>$name,
                "user_id"=>$user_id,
                "type"=>$type,
                "num"=>$num,
                "status"=>$status,
                "create_time"=>date("Y-m-d H:i:s"),
            ]);
        }

        return ['code'=>200];
    }
    function unsetRemind(){
        $id = $_POST['id'];
        self::$mysql->delete('remind',['id'=>$id]);
        return ['code'=>200];
    }
    function getRemind(){
        $user_id = $_GET['user_id'];
        $name = $_GET['name'];
        $res = self::$mysql->select("remind",'*',['user_id'=>$user_id,'name'=>$name]);
        return ['code'=>200,'data'=>$res];
    }
    function getTickerBySymbol(){
        $symbol = $_GET['symbol'];
        try{
            $ticker = json_decode(file_get_contents("https://api.huobi.pro/market/detail/merged?symbol={$symbol}"),true);
        }catch (\Exception $e){
            $ticker = json_decode(file_get_contents("https://api.huobi.pro/market/detail/merged?symbol={$symbol}"),true);
        }
        return $ticker;
    }
    function getTickers(){
        try{
            $ticker = json_decode(file_get_contents("https://api.huobi.pro/market/tickers"),true);
        }catch (\Exception $e){
            $ticker = json_decode(file_get_contents("https://api.huobi.pro/market/tickers"),true);
        }
        return $ticker;
    }
    function getSymbol(){
        $symbol = strtoupper($_GET['symbol']);
        $user_id = isset($_GET['user_id'])?$_GET['user_id']:0;
        $symbols = self::$mysql->query("SELECT
	`symbol`,
	`symbols`.`alias`,
	`own_list`.`id` as follow_id 
FROM
	`symbols`
	LEFT JOIN `own_list` ON `symbols`.`alias` = `own_list`.`alias` 
	AND `user_id` = {$user_id}
WHERE
	 symbols.`alias` LIKE '%{$symbol}%' 
order by symbols.id asc
")->fetchAll(2);
        return ['code'=>200,'data'=>$symbols];
    }
    /**
     * 获取火币的tickers
     */
//    function updateTickers(){
//        $tickers = json_decode(file_get_contents("https://api.huobi.pro/market/tickers"),true);
//        foreach ($tickers['data'] as $ticker){
//            self::$mysql->insert('tickers',$ticker);
//        }
//
//    }
    /**
     * 获取火币所有symbols
     */
    function updateSymbols(){
        model::getInstance()->action(function (){

            foreach (symbolDataHandle::symbol() as $symbol){
                $data = [];
                foreach ($symbol as $key => $val){
                    $key = str_replace('-','_',$key);
                    $data[$key] = $val;
                }
                $preg = preg_match("(\d(l|s))",$data['symbol']);

                if($preg){
                    $str = substr($data['base_currency'],-2);
                    $name = substr($data['base_currency'],0,-2);
                    if(strpos($str,'l')){
                        $as = strtoupper($name)."*".substr($str,0,1).'/'.strtoupper($data['quote_currency']);
                    }else{
                        $as = strtoupper($name)."*(-".substr($str,0,1).')/'.strtoupper($data['quote_currency']);
                    }
                }else{
                    $as = strtoupper($data['base_currency']).'/'.strtoupper($data['quote_currency']);
                }
                $data['alias'] = $as;
                self::$mysql->insert('symbols',$data);
            }
        });

    }
    function test(){
        foreach (symbolDataHandle::symbol() as $test){
            print_r($test);
        }
    }
}
