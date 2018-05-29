<?php
$isDebug   = preg_match("/debug/i", $_SERVER['QUERY_STRING']) >= 1;
$isLocal   = preg_match("/localhost|dev|[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$isLocalIp = preg_match("/[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$ucweb     = @preg_match("/UC/i", $_SERVER['HTTP_USER_AGENT']) >= 1;
$source    = $isDebug || $isLocal ? '.source' : '';
$ucClass   = $ucweb? 'uc-hack' : '';
$webroot   = $isLocal ? $isLocalIp ? "http://".$_SERVER['HTTP_HOST'].":8899": "http://dev.assets.gionee.com" : 'http://assets.3gtest.gionee.com';
$sysRef    = "sys";

switch($moduleName){
	case "dpl": $appRef = "apps/dpl"; $mainCss = "gn.dpl"; $mainJs = "gn_dpl"; break;
	default:
		die("请在页面中检查是否设置变量moduleName所属应用模块[dpl]");
}

$appPic    = $webroot.'/'.$appRef."/pic";
?>