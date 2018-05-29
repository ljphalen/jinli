<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
			'hosts' =>
				array(
					'127.0.0.1:27017',
				),
			'replicaSet'=> '',
			'username'	=>'gouUserAdmin',
			'password'	=>'password',
			'db' 		=>'gou',
            'isauth'    =>true 
	),
	'product' => array(
			'hosts' =>
				array(
					'10.162.88.159:27017',
				),
			'replicaSet'=> '',
			'username'	=>'gouUserAdmin',
			'password'	=>'Ger2_eF*u',
			'db' 		=>'gou',
            'isauth'    => true
	),
	'develop' => array(
			'hosts' =>
				array(
					'127.0.0.1:27017',
				),
			'replicaSet'=> '',
			'username'	=>'',
			'password'	=>'',
			'db' 		=>'gou',
            'isauth'    => false
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
