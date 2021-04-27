<?php


namespace app\controller;


use JPush\Client;

class pushController
{
    function pushIos(){
        $jpush = new Client('b9375eb97c6eaec1eb7fb770','692b27d16461741a74dac1b0');
        $res = $jpush->push()
            ->setPlatform(['ios', 'android'])
            ->addAlias('yuanyuanzhenshuai')
            ->setNotificationAlert("Fuck!")
            ->send();
        print_r($res);
    }
}
