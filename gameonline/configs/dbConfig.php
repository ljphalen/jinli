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
			'bi'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'218.16.100.213',
					'username'=>'gameuser',
					'password'=>'ff_@af591',
					'dbname'=>'bidb_dlv',
					'displayError'=>1
			)
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'prodgamesdb01.mysql.rds.aliyuncs.com',
				'username'=>'prodgamewebdbw',
				'password'=>'wsadea234',
				'dbname'=>'prodgamewebdb',
				'displayError'=>0
			),
			'bi'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'218.16.100.213',
					'username'=>'gameuser',
					'password'=>'ff_@af591',
					'dbname'=>'bidb_dlv',
					'displayError'=>1
			)
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'game',
				'displayError'=>1
			),
			'bi'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'127.0.0.1',
					'username'=>'root',
					'password'=>'root',
					'dbname'=>'game',
					'displayError'=>1
			)
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
