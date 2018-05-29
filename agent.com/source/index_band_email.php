<?php
include 'source/inc/check.php';//登陆检查

//var_dump($_SESSION);
//var_dump($_POST);exit;
$personMail = $_SESSION['userinfo']['email'];

if (isset($_POST['smt'])){
	$pwd = post('userpass');
	$emial = post('email');
	$pwd = md5($pwd.ENCRYPTKEY);
	if ($pwd != $_SESSION['userinfo']['passwd']){
		alertMsg('密码错误，请重新输入！');
	}else {
		
		$op = new op();
                $db = db::factory(get_db_config());
                $user = new user($db);
		$ret = $op->bindMail($_SESSION['userinfo']['userid'], $emial,$user);
		alertMsg('邮件已发送，请查收');
		
	}

}

// smarty
require 'plugin/smarty.php';
// 显示模板的内容
$smarty->assign('personMail',$personMail);
$smarty->display('index_'.$ac.'.html');