<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$config = array(
		'test' => array(
				'host'   => '218.244.145.199',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'gameftpfile', //帐号
				'pass' => 'gameftp##file', //密码
		),
		'product' => array(
				'host'   => '218.244.145.199',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'gameftpfile', //帐号
				'pass' => 'gameftp##file', //密码
		),
			
		'develop' => array(
				'host'   => '218.244.145.199',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'gameftpfile', //帐号
				'pass' => 'gameftp##file', //密码
		)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];