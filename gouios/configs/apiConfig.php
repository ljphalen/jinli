<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		/*push接口*/
		'ssl_url' => 'ssl://gateway.sandbox.push.apple.com:2195',
		'pass' => 'gouwudating',
	),
	'product' => array(
			/*push接口*/
			'ssl_url' => 'ssl://gateway.push.apple.com:2195',
			'pass' => 'taobao',
	),
	'develop' => array(
			/*push接口*/
			'ssl_url' => 'ssl://gateway.sandbox.push.apple.com:2195',
			'pass' => 'gouwudating',
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
