<?php
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/3/18
 * Time: 05:17
 */
//define("__APP_DIR__", dirname(__DIR__));
//include __DIR__."/../vendor/autoload.php";
//
//$config_file = __DIR__."/../config/rabbitmq.php";
//$config      = new \Seals\Library\Config([
//    "host" => "127.0.0.1",
//    "port" => 123,
//    "user" => "admin",
//    "password" => "admin",
//    "vhost" => "/"
//]);
//
//$config->write($config_file);
//echo realpath(__DIR__."/../123.php");
function a($a,$b){
    var_dump($a,$b);
}

call_user_func_array("a",[1,[2,3,4]]);

