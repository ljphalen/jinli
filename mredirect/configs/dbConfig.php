<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'mredirect',
				'displayError'=>1
			)
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'localhost',
				'username'=>'mredirectuser',
				'password'=>'a54uikuysad',
				'dbname'=>'mredirectdb',
				'displayError'=>0
			)
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'mredirect',
				'displayError'=>1
			)
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];