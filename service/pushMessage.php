<?php


namespace service;


use core\config;
use JPush\Client;
use MillionMile\GetEnv\Env;

class pushMessage
{

    static function pushIos($alias,$alert){
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
