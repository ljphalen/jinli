<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$config = array(
		'test' => array(
				'host'   => '42.121.237.23',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'acftpfile', //帐号 
				'pass' => 'acftp##file', //密码 
		),
		'product' => array(
				'host'   => '42.121.237.23',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'acftpfile', //帐号 
				'pass' => 'acftpfile', //密码 
		),
			
		'develop' => array(
				'host'   => '42.121.237.23',//主机地址
				'port'   =>  8009,    //端口
				'user' => 'acftpfile', //帐号 
				'pass' => 'acftp##file', //密码 
		)
			
);
return defined('ENV') ? $config[ENV] : $config['product'];