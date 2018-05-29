<?php
//错误级别
error_reporting(E_ALL ^ E_NOTICE);


//时区
date_default_timezone_set("Asia/Shanghai");

//定义站点根目录
//define('SROOT', __DIR__);
define('SROOT', dirname(__FILE__));

//加载配置文件
require 'conf/web.php';
require 'conf/config.php';
require 'conf/const.php';

//加载框架
require 'sys/core.php';
?>