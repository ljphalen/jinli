<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'game',
				'displayError'=>1
			),
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'localhost',
				'username'=>'dahongmaouser',
				'password'=>'E8jM9v8Qefb',
				'dbname'=>'dahongmaodb',
				'displayError'=>0
			),
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'findjoy',
				'displayError'=>1
			),
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];