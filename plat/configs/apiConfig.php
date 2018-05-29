<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		//sms短信接口
		'sms_url' => 'http://gou.3gtest.gionee.com/api/wifi/sms',
			
	),
	'product' => array(
		//sms短信接口
		'sms_url' => 'http://gou.gionee.com/api/wifi/sms',
			
	),
	'develop' => array(
		//sms短信接口
		'sms_url' => 'http://gou.3gtest.gionee.com/api/wifi/sms',
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];