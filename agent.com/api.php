<?php
/*
 * 渠商信息接口
 * @since 1.0.0 (2013-05-15)
 * @version 1.0.0 (2013-05-15)
 * @author jun <huanghaijun@mykj.com>
 */
header("content-type:text/html;charset=utf-8");
//加载公共文件
require 'comm.php';

require 'conf/api.php';

//获取GET参数
$ac = get('ac');

if($ac == ''){
	$ac = get('method');
}

if($ac == ''){
	$ac = 'daycount';
}

//允许的方法
$acs = array('daycount');

//判断方法是否有效
if (empty($ac) || !in_array($ac, $acs)) {
	output_message(read_status(1001),get('format'));	//1001	接口访问错误
}

//必要参数检查
if(get('clientid')==''||get('sig')==''){
	//exit('clientid参数不正确');
	output_message(read_status(1004),get('format'));	//1004	参数不完整	缺少必要参数
}

if(!isset($ini_api_check[get('clientid')])){
	output_message(read_status(1005),get('format'));	//1005	参数不符合要求	参数非法
	//exit('非法请求');
}

// 取参数值
if(!check_sig($ac)){
	//exit('签名参数不正确');
	output_message(read_status(1003),get('format'));	//1003	sig参数校验失败	校验失败
}

//ip检查
if(!empty($ini_api_check[get('clientid')]['ip']) && $ini_api_check[get('clientid')]['ip']!=$_SERVER['REMOTE_ADDR']){
	//exit('非法IP请求');
	output_message(read_status(1006),get('format'));	//3	非法调用
}



//加载逻辑处理程序]
include SROOT.'/source/api_'.$ac.'.php';
?>