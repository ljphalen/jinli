<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
		'test' => array(
		                'appID' => 'wx56e1481ba72ca1d3',
		                'token' => 'testToken',
		                'appSecret' => 'ac684e82633dc61e1f9eacb994aa2a43'
		),
		'develop' => array(
		                'appID' => 'wx56e1481ba72ca1d3',
		                'token' => 'testToken',
		                'appSecret' => 'ac684e82633dc61e1f9eacb994aa2a43'
		),
		'product' => array(
// 		                'appID' => 'wx88d465425fc51892',
// 		                'token' => 'testToken',
// 		                'appSecret' => 'b43ce374d2245d75fe4a46badb0babe6'
		                'appID' => 'wxfdabb600b600b9b1',
		                'token' => '3e6e0ddf622c82b62fc0ec4b1d27d6c3',
		                'appSecret' => '818bb05e681318bf936675588dfbfcd8'
		),					
);
return defined('ENV') ? $config[ENV] : $config['product'];