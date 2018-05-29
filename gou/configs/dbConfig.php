<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			/* 'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'gou',
				'displayError'=>1
			),
            'bi'=>array(
                'adapter' => 'PDO_MYSQL',
                'host'=>'127.0.0.1',
                'username'=>'root',
                'password'=>'root',
                'dbname'=>'gou_bi',
                'displayError'=>1
            ), */
	
        	'default'=>array(
            	'adapter' => 'PDO_MYSQL',
            	'host'=>'testpushdb01.mysql.aliyun.com',
            	'username'=>'gouw',
            	'password'=>'ljge822_234fg',
            	'dbname'=>'gou',
            	'displayError'=>1
        	),
            	'bi'=>array(
            	'adapter' => 'PDO_MYSQL',
            	'host'=>'testpushdb01.mysql.aliyun.com',
            	'username'=>'gou_biw',
            	'password'=>'iewon2802_lje2',
            	'dbname'=>'gou_bi',
            	'displayError'=>1
        	),
	),
	'product' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'prodecdb01.mysql.aliyun.com',
				'username'=>'prodecdbw',
				'password'=>'gecsqw2345',
				'dbname'=>'prodecdb',
				'displayError'=>0
			),
            'bi'=>array(
                'adapter' => 'PDO_MYSQL',
                'host'=>'prodecdb01.mysql.aliyun.com',
                'username'=>'prodgoubidbw',
                'password'=>'WDsd_839',
                'dbname'=>'prodgoubidb',
                'displayError'=>0
            ),
	),
	'develop' => array(
			'default'=>array(
				'adapter' => 'PDO_MYSQL',
				'host'=>'127.0.0.1',
				'username'=>'root',
				'password'=>'root',
				'dbname'=>'gou',
				'displayError'=>1
			),
            'bi'=>array(
                'adapter' => 'PDO_MYSQL',
                'host'=>'127.0.0.1',
                'username'=>'root',
                'password'=>'root',
                'dbname'=>'gou_bi',
                'displayError'=>1
            ),
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
