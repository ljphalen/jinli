<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
				'sign' => 'gionee201503',
			),
	'product' => array(
				'sign' => 'gionee201503',
			),
			
	'develop' => array(
				'sign' => 'gionee201503',
			)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];