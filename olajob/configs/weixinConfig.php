<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//微信配置如项目中的字符搜索，快速文件查找，快速定位函数等等
$config = array(
	'conf' => array(
		'token'   => 'cx654123',
		'appid'   => 'wx30cb0b0515f450c8',
		'secret'  => '3dc83ddecf3c6b8ee9f4c4ea84c55d77',
		'api_url' => 'https://api.weixin.qq.com/cgi-bin',
		'aes_key' => 'lpCGyrckgTBmAgdA0MkOySbQAvejes7lCF9yeVoTzM7',
	),
);

return $config;