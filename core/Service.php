<?php
namespace core;

use core\lib\log;

class Service{

    public static $classMap = array();
    static function run(){

        $route = new \core\lib\route();
        $ctrlClass = $route->ctrl;
        $action = $route->action;
        $ctrlFile = APP.'/controller/'.$ctrlClass.'Controller.php';
        $cltrlClass = CONTROLLER.'\controller\\'.$ctrlClass.'Controller';

        if(!$route->checkToken($ctrlClass.'/'.$action)){
            echo json_encode(['code'=>104,'msg'=>'请先登录用户']);
            return true;
        }
        if(is_file($ctrlFile)){
            include $ctrlFile;
            $ctrl = new $cltrlClass();
            $res = $ctrl->$action();
            if(is_array($res)){
                echo json_encode($res);
            }else{
                echo $res;
            }
        }else{
            throw new \Exception('找不到控制器'.$cltrlClass.'.php');
        }
    }

    /**
     * 自动加载类库
     * @param $class
     * @return bool
     */
    static function load($class){
        if(isset(self::$classMap[$class])){
            return true;
        }else{
            $class = str_replace('\\','/',$class);
            $file = ROOT.'/'.$class.'.php';


            if(is_file($file)){
                include $file;
                self::$classMap[$class] = $class;
            }else{
                return false;
            }
        }

    }
}
