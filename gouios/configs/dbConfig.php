<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'gou_ios',
				'displayError'=>1
			)
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'localhost',
				'username'=>'progouiosuser',
				'password'=>'8jfewijf9',
				'dbname'=>'progouiosdb',
				'displayError'=>0
			)
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'gou_ios',
				'displayError'=>1
			)
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];