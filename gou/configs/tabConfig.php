<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
客户端tab栏配置
 */
$config = array(
	'test' => array(
		//客户端tab栏
		'apk'=>array(
			0=>array('tabName'=>'首页','url'=>'http://apk.gou.3gtest.gionee.com'),
			1=>array('tabName'=>'货到付款','url'=>'http://apk.gou.3gtest.gionee.com/cod'),
			2=>array('tabName'=>'天天特价','url'=>'http://apk.gou.3gtest.gionee.com/shop'),
		),
		'market'=>array(
			0=>array('tabName'=>'推荐','url'=>'http://market.gou.3gtest.gionee.com'),
			1=>array('tabName'=>'货到付款','url'=>'http://market.gou.3gtest.gionee.com/cod'),
			2=>array('tabName'=>'淘宝好店','url'=>'http://market.gou.3gtest.gionee.com/shop'),
		),
	),
	'product' => array(
		//客户端tab栏
		'apk'=>array(
			0=>array('tabName'=>'首页','url'=>'http://apk.gou.gionee.com'),
			1=>array('tabName'=>'货到付款','url'=>'http://apk.gou.gionee.com/cod'),
			2=>array('tabName'=>'天天特价','url'=>'http://apk.gou.gionee.com/shop'),
		),
		'market'=>array(
				0=>array('tabName'=>'推荐','url'=>'http://market.gou.gionee.com'),
				1=>array('tabName'=>'货到付款','url'=>'http://market.gou.gionee.com/cod'),
				2=>array('tabName'=>'淘宝好店','url'=>'http://market.gou.gionee.com/shop'),
		),
	),
	'develop' => array(
		//客户端tab栏
		'apk'=>array(
			0=>array('tabName'=>'首页','url'=>'http://apk.gou.3gtest.gionee.com'),
			1=>array('tabName'=>'货到付款','url'=>'http://apk.gou.3gtest.gionee.com/cod'),
			2=>array('tabName'=>'天天特价','url'=>'http://apk.gou.3gtest.gionee.com/shop'),
		),
		'market'=>array(
				0=>array('tabName'=>'推荐','url'=>'http://market.gou.gionee.com'),
				1=>array('tabName'=>'货到付款','url'=>'http://market.gou.gionee.com/cod'),
				2=>array('tabName'=>'淘宝好店','url'=>'http://market.gou.gionee.com/shop'),
		),
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
