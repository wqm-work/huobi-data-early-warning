<?php
namespace core\lib;
use core\config;

class log
{
    static $class ;
    static public function init(){
        $drive = config::get('DRIVE','log');
        $class = 'core\lib\drive\log\\'.$drive;
        self::$class = new $class;
    }
    static function log($name){
        self::$class->log($name);
    }
}

