<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		/*push接口*/
		'appid' => '8a6f4ea5e647490ba459620f04af7001',
		'password' => 'pushthemeaps123456',
		'url' => 'http://push.gionee.com'
	),
	'product' => array(
		/*push接口*/
		'appid' => '8a6f4ea5e647490ba459620f04af7001',
		'password' => 'pushthemeaps123456',
		'url' => 'http://push.gionee.com'
	),
	'develop' => array(
		/*push接口*/
		'appid' => '8a6f4ea5e647490ba459620f04af7001',
		'password' => 'pushthemeaps123456',
		'url' => 'http://push.gionee.com'
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
