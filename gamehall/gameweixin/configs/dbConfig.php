<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'testpushdb01.mysql.aliyun.com',
				'username'=>'testgamewxdbw',
				'password'=>'yluKYf75',
				'dbname'=>'testgamewxdb',
				'displayError'=>1
			)
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'prodecdb01.mysql.aliyun.com',
				'username'=>'prodacdbw',
				'password'=>'wsadea234',
				'dbname'=>'prodacdb',
				'displayError'=>0
			)
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'192.168.115.159',
				'username'=>'root',
				'password'=>'jjljjk',
				'dbname'=>'gameweixindb',
				'displayError'=>1
			)
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];