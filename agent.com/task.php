<?php
/*
 * 用户操作接口入口文件
 * @since 1.0.0 (2012-08-14)
 * @version 1.0.0 (2012-08-14)
 * @author jun <huanghaijun@mykj.com>
 */
header("content-type:text/html;charset=utf-8");
//加载公共文件
require 'comm.php';


//获取GET参数
$ac = get('ac');

if($ac == ''){
	$ac = get('method');
}

//允许的方法
$acs = array('update');

//判断方法是否有效
if (empty($ac) || !in_array($ac, $acs)) {
	exit();
}


//加载逻辑处理程序]
include SROOT.'/source/task_'.$ac.'.php';
?>