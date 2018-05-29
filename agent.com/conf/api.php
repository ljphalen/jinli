<?php
define('API_ERRLOG', 0);
//各接口参数
$ini_api_params = array(
		//取子渠道日统计
		'daycount' => array('ac','reporttype','gameid','clientid','subclientid','ymdstart','ymdend','format')
);

//渠道商鉴权配置(如设IP会进行IP检查)
$ini_api_check = array(
		8168=>array('key'=>'27c41bc54829a8ff14727b40f88303b5','ip'=>'')
);


$ini_api_errcode = array(
	 	 0 => '成功'
		,1 => '系统故障'
		,1001 => '接口访问错误'
		,1002 => 'apikey错误'
		,1003 => '校验失败'
		,1004 => '缺少必要参数'
		,1005 => '参数非法'
		,1006 => '非法访问'
		,1007 => '参数超过限制'
);
?>