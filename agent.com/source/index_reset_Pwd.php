<?php
include 'source/inc/check.php';//登陆检查



if (isset($_POST['smt'])){
	$oldpwd = post('oldpwd');
	$newpwd = post('newpwd');
	$repwd = post('repwd');
	
	if ($newpwd != $repwd){
		alertMsg('两次输入的新密码不一致！');
	}else{
		//连接数据库
		$db = db::factory(get_db_config());
		$user = new user($db);
		$op = new op();
		$ret = $op->editPasswd($_SESSION['userinfo']['userid'], $oldpwd, $newpwd, $user);
		alertMsg($config['error'][$ret]);
	}
	
	
}

// smarty
require 'plugin/smarty.php';
$smarty->assign('actionText','修改密码');
// 显示模板的内容
$smarty->display('index_'.$ac.'.html');