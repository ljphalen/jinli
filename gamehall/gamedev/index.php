<?php
//定义项目名称和路径
define('APP_NAME',				'Source');
define('APP_PATH',				'./Source/');

//全局变量
define('SITE_PATH',             dirname(__FILE__));
define('DATA_HOME',             SITE_PATH.'/Data/');
define('CONF_PATH',             DATA_HOME.'Config/');
define('ATTACH_PATH',           DATA_HOME.'Attachments/');
define('SESSION_PATH',          DATA_HOME.'Session/');
define('EVENT_LOG_PATH',        DATA_HOME.'Log/');

//获取当前运行的域名
define('APP_HOST_NAME',			strtolower(substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ":") ? strpos($_SERVER['HTTP_HOST'], ":") : strlen($_SERVER['HTTP_HOST']) ) ));
define('APP_HOST_FIX',			substr(APP_HOST_NAME, strpos(APP_HOST_NAME, '.')) );

//获取集群中的php服务器编号，生成不同的编译目录
define('SERVERID',				(isset($_SERVER['SERVERID']) && !empty($_SERVER['SERVERID'])) ? $_SERVER['SERVERID'] : '' );
define('RUNTIME_PATH',          DATA_HOME.'Runtime/'.SERVERID.'/'.APP_HOST_NAME.'/');

//定义调试模式
define('APP_DEBUG',				1);
define('THINK_PATH',            realpath('./Core').'/');
require(THINK_PATH."/ThinkPHP.php");


if (isset($_GET['session'])) {
    echo "<pre>========== SESSION ==========\n";
    print_r($_SESSION);
}
if (isset($_GET['config'])) {
    echo "<pre>========== CONFIG ==========\n";
    print_r(C());
}
if (isset($_GET['define'])) {
    echo "<pre>========== CONSTANTS ==========\n";
    $definedConstants = get_defined_constants(true);
    print_r($definedConstants['user']);
}

