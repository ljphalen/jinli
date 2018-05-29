<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test'             => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'testpushdb01.mysql.aliyun.com',
			'username'     => 'test3gw',
			'password'     => 'ljgsee52_1ljf_fg',
			'dbname'       => 'test3g',
			'displayError' => 1
		),
	),
	'product'          => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'gioneedb.mysql.aliyun.com',
			'username'     => 'prod3gadmindbw',
			'password'     => 'sdyhj123',
			'dbname'       => 'prod3gadmindb',
			'displayError' => 0
		),
	),
	'develop'          => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => '127.0.0.1',
			'username'     => 'root',
			'password'     => '123456',
			'dbname'       => 'test3g',
			'displayError' => 1
		),
	),
	'overseas_product' => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'gioneedb.mysql.aliyun.com',
			'username'     => 'prod3gadmindbw',
			'password'     => 'sdyhj123',
			'dbname'       => 'prod3gadmindb',
			'displayError' => 0
		),
	),
	'overseas_test'    => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'testpushdb01.mysql.aliyun.com',
			'username'     => 'test3gw',
			'password'     => 'ljgsee52_1ljf_fg',
			'dbname'       => 'test3goverseadb',
			'displayError' => 0
		),
	),
	'sige_product' => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'gioneedb.mysql.aliyun.com',
			'username'     => 'prod3gadmindbw',
			'password'     => 'sdyhj123',
			'dbname'       => 'prod3gadmindb',
			'displayError' => 0
		),
	),
	'sige_test' => array(
		'default' => array(
			'adapter'      => 'PDO_MYSQL',
			'host'         => 'testpushdb01.mysql.aliyun.com',
			'username'     => 'test3gw',
			'password'     => 'ljgsee52_1ljf_fg',
			'dbname'       => 'testsigedb',
			'displayError' => 0
		),
	),

);
return defined('ENV') ? $config[ENV] : $config['product'];