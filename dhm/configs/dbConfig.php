<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'testpushdb01.mysql.aliyun.com',
				'username'=>'testdhmdbw',
				'password'=>'p5zpUM8h',
				'dbname'=>'testdhmdb',
				'displayError'=>1
			),
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'localhost',
				'username'=>'dhmuser',
				'password'=>'I34H5tIvJ9k',
				'dbname'=>'dhmdb',
				'displayError'=>0
			),
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'dhm',
				'displayError'=>1
			),
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
