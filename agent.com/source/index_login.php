<?php
session_start();
// smarty
require 'plugin/smarty.php';

if(post('submitBtn')){
	//$_SESSION['level']=222;
	$nickname = post('username');
	$passwd   = post('password');
	$captchStr = post('captchStr');
	//连接数据库
	$db = db::factory(get_db_config());
	$user = new user($db);
	$op = new op(); 
	if(!isset($_SESSION['loginerrtimes'])||$_SESSION['loginerrtimes']<3){
		//不检查验证码
		$captchStr = -1;
	}
	
	$ret = $op->Login($nickname, $passwd, $captchStr,$user);
	if ($ret != 0){
		if(isset($_SESSION['loginerrtimes'])){
			$_SESSION['loginerrtimes'] +=1;
		}else{
			$_SESSION['loginerrtimes'] = 1;
		}
		alertMsg($config['error'][$ret],'/index.php?ac=login');
		exit;
	}
	$_SESSION['loginerrtimes'] = 0;
	
	jump_url('','index.php?ac=main');
	
}


// 模板赋值
$smarty->assign('loginerrtimes',$_SESSION['loginerrtimes']);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');