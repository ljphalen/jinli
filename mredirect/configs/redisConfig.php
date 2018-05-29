<?php
$config = array (
		'test' => array (
				'host' => '127.0.0.1',
				'port' => '6379' 
		),
		'product' => array (
				'host' => '127.0.0.1',
				'port' => '6379' 
		),
		'develop' => array (
				'host' => '127.0.0.1',
				'port' => '6379'
		) 
);
return defined('ENV') ? $config[ENV] : $config['product'];