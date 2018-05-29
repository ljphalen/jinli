<?php
/*
 * 订单表汇总操作接口入口文件
* @since 1.0.0 (2013-05-13)
* @version 1.0.0 (2013-05-13)
* @author ljp <ljphalen@163.com>
*/
header("content-type:text/html;charset=utf-8");
//加载公共文件
require 'comm.php';


//获取GET参数
$ac = get('ac');

if($ac == ''){
	$ac= 'update';
}

//允许的方法
$acs = array('update');

//判断方法是否有效
if (empty($ac) || !in_array($ac, $acs)) {
	exit();
}


//加载逻辑处理程序]
include SROOT.'/source/plan_'.$ac.'.php';
?>