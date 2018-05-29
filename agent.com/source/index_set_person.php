<?php
include 'source/inc/check.php';//登陆检查

if (isset($_POST['smt'])){
	$nickname = post('nickname');
	//连接数据库
	$db = db::factory(get_db_config());
	$user = new user($db);
	$op = new op();
	
	$ret = $op->editPersonal($nickname, $user);
	alertMsg($config['error'][$ret]);

	
}


$personNickname = $_SESSION['userinfo']['nickname'];
$personName = $_SESSION['userinfo']['username'];
$personRole = $config['level'][$_SESSION['userinfo']['level']];

// smarty
require 'plugin/smarty.php';
// 显示模板的内容
$smarty->assign('personName',$personName);
$smarty->assign('personRole',$personRole);
$smarty->assign('personNickname',$personNickname);
$smarty->assign('actionText','个人设置');
$smarty->display('index_'.$ac.'.html');