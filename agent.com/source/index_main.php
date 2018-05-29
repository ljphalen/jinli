<?php
// smarty
require 'plugin/smarty.php';

//连接数据库
//$db = new Dbmodel('db1');

//print_r($sess_userinfo);
// 模板赋值

$sess_userinfo['dateline'] = date('Y-m-d H:i',$sess_userinfo['dateline']);
$sess_userinfo['rolename'] = $config['level'][$sess_userinfo['level']];
$smarty->assign('userinfo',$sess_userinfo);
// 显示模板的内容
$smarty->display('index_'.$ac.'.html');