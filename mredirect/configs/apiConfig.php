<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		
	),
	'product' => array(
		
	),
	'develop' => array(
		
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
