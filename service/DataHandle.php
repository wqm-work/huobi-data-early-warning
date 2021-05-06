<?php


namespace service;


use core\lib\log;
use core\lib\model;
use Psr\Log\LoggerInterface;

class DataHandle
{
    private static $alias = '';
    private static $data;
    /**
     * @var array
     */
    private static $alert;

    function __construct($data)
    {
        self::$data = $data;
        $this->handle();
    }

    static function handle(){
        if(isset(self::$data['tick'])){
            $ch_arr = explode('.',self::$data['ch']);
            $symbol = $ch_arr[1];
            $remind = model::getInstance()->get('symbols',['alias'],['symbol'=>$symbol]);
            self::$alias = $remind['alias'];
            $new_price = self::$data['tick']['close'];//最新价格
            /**
             * 获取设置涨幅提醒的人,每五分钟提醒一次
             */
            $time = time();
            //type = 1 涨，2： 跌
            self::pushMessage(self::getUsers($symbol,$new_price,1,$time),$time,$symbol,$new_price,1);
            self::pushMessage(self::getUsers($symbol,$new_price,2,$time),$time,$symbol,$new_price,2);
        }
    }
    static function pushMessage($user,$time,$symbol,$new_price,$type){
        if($user){
            log::setLog('已经获取到了需要推送的用户.....');
            $user_ids = array_column($user,'id');
            $alias = array_column($user,'alias');
            model::getInstance()->update('remind',
                [
                    'notice_time'=>$time
                ],
                [
                    'id'=>$user_ids
                ]
            );
            self::setAlert($time,$symbol,$new_price,$type);
            log::setLog('准备推送信息.....');
            pushMessage::pushIos($alias,self::$alert);
        }
    }
    static function setAlert($time,$symbol,$new_price,$type = 1){
        $h_s = date('H:i',$time);
        if ($type == 1){
            $txt = '涨';
        }else{
            $txt = '跌';
        }

        self::$alert = [
            'alert'=>$h_s.' '.self::$alias.'火币全球站最新成交价'.$txt.'到'.$new_price.'USDT',
            "set"=>[
                'sound'=>'shake.caf'
            ]
        ];
    }
    static function getUsers($symbol,$new_price,$type,$time){
        $time = time();
        if($type == 1){
            $where =  [
                'name'=>$symbol,
                "status"=>1,
                "type"=>1,
                "num[<=]"=>$new_price,
                'notice_time[<=]'=> $time - 180
            ];
        }else{
            $where =  [
                'name'=>$symbol,
                "status"=>1,
                "type"=>2,
                "num[>=]"=>$new_price,
                'notice_time[<=]'=> $time - 180
            ];
        }

        $user = model::getInstance()->select(
            'remind',
            [
                "[>]users"=>['user_id'=>'id']
            ],
            [
                'num',
                "users.alias",
                'remind.id'
            ],
            $where);
        return $user;
    }
}
