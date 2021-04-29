<?php

namespace app\models;
class symbolDataHandle
{
    static function symbol(){
        $symbols = 1;
        if($symbols){
            foreach (json_decode(file_get_contents("https://api.huobi.pro/v1/common/symbols"),true)['data'] as $symbol){
                yield $symbol;
            }
        }
    }
}
