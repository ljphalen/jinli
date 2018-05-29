<?php
include './protected/config/env.conf.php';
include './protected/config/permission.conf.php';
include './protected/config/routes.conf.php';

#Just include this for production mode
//include $config['BASE_PATH'].'deployment/deploy.php';
include $config['BASE_PATH'].'Doo.php';
include $config['BASE_PATH'].'app/DooConfig.php';
error_reporting(E_ERROR | E_PARSE);
# Uncomment for auto loading the framework classes.
//spl_autoload_register('Doo::autoload');

//加载db的config文件
Doo::conf()->set($config);
//加载自定义配置项
Doo::conf()->set($myconfig);
//加载redis的config文件
Doo::conf()->set($rdconfig);
//加载redis队列的config文件
Doo::conf()->set($redisQconfig);
// 加载MysqlShard分表分库的Config文件
Doo::conf()->set($mysqlShard);
//加载email配置文件
Doo::conf()->set($EMAIL);
//定义权限配制
Doo::conf()->set($PERMISS_CONFIG);
//加载rtb配置项
Doo::conf()->set($rtbconfig);
//加载push配置项
Doo::conf()->set($pushconfig);
//加载植入式广告配置项
Doo::conf()->set($inappconfig);
# remove this if you wish to see the normal PHP error view.
//include $config['BASE_PATH'].'diagnostic/debug.php';

# database usage
//Doo::useDbReplicate();	#for db replication master-slave usage
//Doo::db()->setMap($dbmap);
Doo::db()->setDb($dbconfig, $config['APP_MODE']);
Doo::db()->sql_tracking = $config['DEBUG_ENABLED'];	#for debugging/profiling purpose

//session
Doo::session()->start();


Doo::app()->route = $route;
# Uncomment for DB profiling
//Doo::logger()->beginDbProfile('doowebsite');
Doo::app()->run();
//Doo::loadController("AppDooController");
//Doo::logger()->endDbProfile('doowebsite');
//Doo::logger()->rotateFile(20);
//Doo::logger()->writeDbProfiles();
?>
