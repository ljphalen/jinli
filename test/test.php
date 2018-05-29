<?php

//http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&218.192.3.1
$str = base_convert(255,10,2) ;

$str = str_pad($str,8,'0',STR_PAD_LEFT);

//echo $str;

echo getClientIP();


/**
 * 获取客户段访问IP地址,成功返回客户段IP,失败返回空
 */
function getClientIP() {

	if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])){
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
	}
	if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])){
		return $_SERVER['HTTP_PROXY_USER'];
	}
	if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])){
		return $_SERVER['REMOTE_ADDR'];
	} else {
		return "0.0.0.0";
	}
}