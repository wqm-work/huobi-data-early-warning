<?php


namespace service;


use core\config;
use core\lib\log;
use JPush\Client;
use MillionMile\GetEnv\Env;

class pushMessage
{

    static function pushIos($alias,$alert){
        log::setLog('开始推送......');
        log::setLog("推送消息为：",$alert);
        $appKey = Env::get('appKey');
        $masterSecret = Env::get('masterSecret');
        $jpush = new Client($appKey,$masterSecret);
        $res = $jpush->push()
            ->setPlatform(['ios', 'android'])
            ->addAlias($alias)
            ->iosNotification($alert['alert'],$alert['set'])
            ->androidNotification($alert['alert'],$alert['set'])
            ->options(
                [
                    'apns_production'=>true
                ]
            )
            ->send();
        return $res;
    }
}
