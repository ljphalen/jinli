<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$config = array (
		'test' => array (
					'host' => '127.0.0.1',
					'port' => '6779',
		),
		'product' => array (
					'host' => '10.168.79.215',
					'port' => '6779',
		),
		'develop' => array (
					'host' => '127.0.0.1',
					'port' => '6379',
		) 
);
return defined('ENV') ? $config[ENV] : $config['product'];
