<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
				'belong'=>array( //应用类别
						'1'=>'预安装',
						'2'=>'装机必备'
				),
				'system'  => '1', //装机必备分类id
	),
	'product' => array(
				'belong'=>array( //应用类别
					'1'=>'预安装',
					'2'=>'装机必备'
				),
				'system'  => '5', //装机必备分类id
	),
			
	'develop' => array(
				'belong'=>array( //应用类别
					'1'=>'预安装',
					'2'=>'装机必备'
				),
				'system'  => '1', //装机必备分类id
	)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];