<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'testpushdb01.mysql.aliyun.com',
				'username'=>'testgamedbw',
				'password'=>'hih3409-12',
				'dbname'=>'testgamedb',
				'displayError'=>1
			),
			'glog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'testpushdb01.mysql.aliyun.com',
					'username'=>'testgamelogdbw',
					'password'=>'yt634_yc',
					'dbname'=>'testgamelog',
					'displayError'=>1
			),
			'acclog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'testidpaydb01.mysql.rds.aliyuncs.com',
					'username'=>'devreader',
					'password'=>'devdbr0808',
					'dbname'=>'bizlogdb',
					'displayError'=>1
			),
			'bi'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'218.16.100.213',
					'username'=>'h5user',
					'password'=>'gge9e2-1kef5',
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
			'glog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'127.0.0.1',
					'username'=>'root',
					'password'=>'root',
					'dbname'=>'gamelog',
					'displayError'=>1
			),
			'acclog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'127.0.0.1',
					'username'=>'root',
					'password'=>'root',
					'dbname'=>'bizlogdb',
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
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'42.121.237.23',
// 				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'game',
// 				'dbname'=>'game_online2',
				'displayError'=>1
			),
			'glog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'127.0.0.1',
					'username'=>'root',
					'password'=>'root',
					'dbname'=>'gamelog',
					'displayError'=>1
			),
			'acclog'=>array(
					'adapter' => 'PDO_MYSQL',
					'host'=>'testidpaydb01.mysql.rds.aliyuncs.com',
					'username'=>'devreader',
					'password'=>'devdbr0808',
					'dbname'=>'bizlogdb',
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
