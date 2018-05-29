<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'testpushdb01.mysql.aliyun.com',
				'username'=>'gamesw',
				'password'=>'ag_92i2gjirm8',
				'dbname'=>'games',
				'displayError'=>1
			)
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'prodgamesdb01.mysql.rds.aliyuncs.com',
				'username'=>'prodgamesdbw',
				'password'=>'wsedz234',
				'dbname'=>'prodgamesdb',
				'displayError'=>0
			)
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'games',
				'displayError'=>1
			)
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
