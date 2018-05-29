<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//微信配置
$config = array(
	'conf' => array(
		'token'   => 'cx654123',
		'appid'   => 'wxfb30cace72e2d613',
		'secret'  => '7c1e60aba6c1b9313415ff77e812229d',
		'api_url' => 'https://api.weixin.qq.com/cgi-bin',
		'aes_key' => 'CiCG1QeetTWSdY1kTlEAZ6FyxAOhwwAdy2cMAdR2ngF',
	),
);

return $config;