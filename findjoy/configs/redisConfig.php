<?php
$config = array (
		'test' => array (
				'host' => '127.0.0.1',
				'port' => '6379',
				'key-prefix'=>'fj-'
		),
		'product' => array (
				'host' => '127.0.0.1',
				'port' => '6379',
				'key-prefix'=>'fj-'
		),
		'develop' => array (
				'host' => '127.0.0.1',
				'port' => '6379',
				'key-prefix'=>'fj-'
		) 
);

return defined('ENV') ? $config[ENV] : $config['product'];