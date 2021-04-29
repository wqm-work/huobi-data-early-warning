<?php


namespace app\controller;


use core\config;
use core\lib\model;
use MillionMile\GetEnv\Env;
use service\DataHandle;

class loginController
{
    /**
     * @var model
     */
    private static $mysql;

    function __construct()
    {
        self::$mysql = model::getInstance();
    }

    function index(){
        $sign = $_POST['sign'];
        $email = $_POST['email'];
        if($sign != md5('binging'.$email)){
            return ['code'=>'101','msg'=>'签名验证失败'];
        }
        $user = self::$mysql->get("users",'*',['email'=>$email]);
        $user_id = $user?$user['id']:0;
        $token = md5($email.'huobi'.time());
        if(!$user){
            self::$mysql->insert('users',['email'=>$email,'token'=>$token]);
            $user_id = self::$mysql->pdo->lastInsertId();
            self::$mysql->update('users',['alias'=>"BING{$user_id}"],['id'=>$user_id]);
        }else{
            self::$mysql->update('users',['token'=>$token],['id'=>$user_id]);
        }
        return ['code'=>200,'data'=>['user_id'=>$user_id,'email'=>$_POST['email'],'token'=>$token]];
    }
}
