<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'testpushdb01.mysql.aliyun.com',
				'username'=>'testolajobdbw',
				'password'=>'ai21YTpp',
				'dbname'=>'testolajob',
				'displayError'=>1
			),
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'localhost',
				'username'=>'olajobuser',
				'password'=>'K3fJf8Ei3va',
				'dbname'=>'olajobdb',
				'displayError'=>0
			),
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'olajob',
				'displayError'=>1
			),
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];