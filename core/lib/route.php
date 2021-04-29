<?php
namespace core\lib;

use core\config;

class route{
    public $ctrl ;
    public $action ;
    public function __construct()
    {

        if($_SERVER['REQUEST_URI'] && $_SERVER['REQUEST_URI'] != '/'){
            $path = $_SERVER['REQUEST_URI'];
            if(strpos($path,'?')){
                $get_param = explode('?',$path);
                $path = $get_param[0];
            }
            $patharr = explode('/',trim($path,'/'));
            if(isset($patharr[0])){
                $this->ctrl = $patharr[0];
            }
            unset($patharr[0]);
            if (isset($patharr[1])){
                $this->action = $patharr[1];
                unset($patharr[1]);
            }else{
                $this->action = config::get('ACTION','route');
            }
            //url的多余部分转换成GET参数
            $count  = count($patharr)+2;
            $i = 2;
            while ($i < $count){
                if(isset($patharr[$i+1])){
                    $_GET[$patharr[$i]] = $patharr[$i+1];
                }
                $i += 2;
            }
        }else{
            $this->ctrl = config::get('CTRL','route');
            $this->action = config::get('ACTION','route');
        }
    }
    function checkToken($action){
        try {
            if (isset($_SERVER['HTTP_TOKEN']) && $_SERVER['HTTP_TOKEN']) {
                $user = model::getInstance()->get('users', '*', ['token' => $_GET['token']]);
                if ($user) {
                    return true;
                } else {
                    return false;
                }
            } elseif (in_array($action, config::all('whitelist'))) {
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
