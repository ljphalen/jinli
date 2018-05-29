<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
			
	),
	'product' => array(
		//sms短信接口
		'sms_url' => 'http://id.gionee.com:10808/sms/mt.do',
			
	),
	'develop' => array(
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];