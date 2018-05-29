<?php
/*
 * 根据环境读取不同的配置文件 
 * 需要在系统php-fpm.conf里设置PHP_ENV_MODE的值
 * dev 开发环境
 * test 测试环境
 * sanbox 沙盒环境
 * prod 生产环境
 *  */
$ENV_MODE=empty($_ENV["PHP_ENV_MODE"])?$_SERVER["PHP_ENV_MODE"]:$_ENV["PHP_ENV_MODE"];
if(empty($ENV_MODE)){//未设置环境变量这取线上配置
    $ENV_MODE="prod";
}
include "$ENV_MODE/common.conf.php";
include "$ENV_MODE/mycommon.conf.php";
include "$ENV_MODE/redis.conf.php";//redis相关配置
//include "$ENV_MODE/redisQuese.conf.php";//redis队列相关配置
//include "$ENV_MODE/mysqlshard.conf.php"; // 分表分库配置
include "$ENV_MODE/email.conf.php"; // 邮件配置
include "$ENV_MODE/db.conf.php";
include "$ENV_MODE/CDNcommon.conf.php";
include "$ENV_MODE/rtb.conf.php";//rtb配置
include "$ENV_MODE/push.conf.php";//push配置
include "$ENV_MODE/inapp.conf.php";//植入式广告配置

