<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
				'searchUrl'  => 'http://search.gtest.gionee.com/',
				'sign' => 'gionee201503',
			),
	'product' => array(
				'searchUrl'  => 'http://search.game.gionee.com/',
				'sign' => 'gionee201503',
			),
			
	'develop' => array(
				'searchUrl'  => 'http://search.gtest.gionee.com/',
				'sign' => 'gionee201503',
			)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];