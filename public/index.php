<?php
/**
 * 入口文件
 */
define('ROOT',realpath('../'));
define('CORE',ROOT.'/core');
define('APP',ROOT.'/app');
define('CONTROLLER','\app');
define('DEBUG',true);
include "../vendor/autoload.php";
if(DEBUG){
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
    ini_set('display_errors','On');
}else{
    ini_set('display_errors','Off');
}
include CORE.'/common/function.php';

include CORE.'/Service.php';

spl_autoload_register('\core\Service::load');

\core\Service::run();

