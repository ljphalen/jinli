<?php 
session_start();
if(in_array($ac, $config['priv_page']['nocheck'])){
    
}elseif (isset($_SESSION['userinfo']['level'])){
	if ($_SESSION['userinfo']['level']<200){
		if(!isset($config['priv_page'][$ac]) || !in_array($_SESSION['userinfo']['level'], $config['priv_page'][$ac])){
		
			jump_url('您没有访问该页面权限！',WLECOME);
			exit;
		}
	}
}else{
	jump_url('您没有访问该页面权限！',WLECOME,'js','top');
	exit;
}

$sess_userinfo = $_SESSION['userinfo'];
?>