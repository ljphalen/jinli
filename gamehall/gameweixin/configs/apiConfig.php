<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
		'test' => array(
		                'gameApiRoot' => 'http://game.3gtest.gionee.com',
		                'forgeturl' => 'http://t-id.gionee.com/gsp/reset/start',
		),
		'develop' => array(
		                'gameApiRoot' => 'http://game.3gtest.gionee.com',
		                'forgeturl' => 'http://t-id.gionee.com/gsp/reset/start'
		),
		'product' => array(
		                'gameApiRoot' => 'http://game.gionee.com',
		                'forgeturl' => 'http://id.gionee.com/gsp/reset/start'
		),					
);
return defined('ENV') ? $config[ENV] : $config['product'];