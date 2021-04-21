<?php

namespace app\controller;
use core\config;
use core\lib\model;

class indexController
{
    static $mysql ;
    function __construct()
    {
        self::$mysql = new model();
    }

    public function follow(){
        $name = $_POST['name'];
        $user_id = $_POST['user_id'];

        self::$mysql->insert('own_list',[
            "name"=>$name,
            "user_id"=>$user_id,
            "create_time"=>date("Y-m-d H:i:s"),
        ]);
        return ['code'=>200];
    }

    function getFollow(){
        $user_id = $_GET['user_id'];
        $data = self::$mysql->select("own_list",'*',"user_id = '{$user_id}'");
        return ['code'=>200,'data'=>$data];
    }

    function setRemind(){
        $user_id = $_POST['user_id'];
        $type = $_POST['type'];
        $name = $_POST['name'];
        $num = $_POST['num'];
        $status = $_POST['status'];
        self::$mysql->insert('remind',[
            "name"=>$name,
            "user_id"=>$user_id,
            "type"=>$type,
            "num"=>$num,
            "status"=>$status,
            "create_time"=>date("Y-m-d H:i:s"),
        ]);
        return ['code'=>200];
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
//    function updateSymbols(){
//        $symbols = json_decode(file_get_contents("https://api.huobi.pro/v1/common/symbols"),true);
//        foreach ($symbols['data'] as $symbol){
//            $data = [];
//            foreach ($symbol as $key => $val){
//                $key = str_replace('-','_',$key);
//                $data[$key] = $val;
//            }
//            self::$mysql->insert('symbols',$data);
//        }
//    }
}
