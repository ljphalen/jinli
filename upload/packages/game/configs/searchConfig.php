<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
				'searchUrl'  => 'http://search.game.3gtest.gionee.com/',
				'sign' => 'gionee201503',
			),
	'product' => array(
				'searchUrl'  => 'http://search.game.gionee.com/',
				'sign' => 'gionee201503',
			),
			
	'develop' => array(
				'searchUrl'  => 'http://search.game.localhost.gionee.com/',
				'sign' => 'gionee201503',
			)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];