<?php

header("content-type:text/html;chart-set=utf-8");
//加载公共文件
require 'comm.php';
include "source/inc/check.php";
//$_SESSION['level'] = 10;

//echo FILENAME;
//echo $_SERVER['SCRIPT_FILENAME'];
//getmenu();
//echo json_encode($config['menu']);

//var_dump(check_prvi(FILENAME));

//writeLog("You messed up!", 3, "/data/log/xxx.log");
//登陆模块
/* $db = db::factory('db1');
$op = new op($db);
$user = new user($db);

$_SESSION['captcha'] = '12345';
$username = 'admin';
$passwd = '123456';
$captcha = '12345';
//$ret = $op->Login($username, $passwd, $captcha,$user);
//$ret = $op->check_prvi(FILENAME);

//修改密码模块
var_dump($_SESSION);exit;
$username = 'admin';
$oldpasswd = '123456';
$newpasswd = '123456'; */
var_dump($_SESSION);
$db = db::factory('db1');
$op = new op($db);
$user = new user($db);
//$_SESSION['captcha'] = '12345';
//$ret = $op->Login('admin', '456', '12345',$user);
//$ret = $op->editPasswd($_SESSION['userinfo']['userid'], 456, 123456, $user);
$op->editPersonal('陈冠希', $user);

var_dump($ret);














?>