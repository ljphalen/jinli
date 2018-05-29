<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		'appid' => '33729FD0C3D842CC9774A1B4D1D93931',
		'appkey' => 'BFB578DD9ED243A2BAA83B715A363A78',
	),
	'product' => array(
		'appid' => 'DCAD37901F2A45468AD06684523AA2F8',
		'appkey' => '88EAC6309CF247AB81D4533F57E1FC08',
	),
	'develop' => array(
		'appid' => '33729FD0C3D842CC9774A1B4D1D93931',
		'appkey' => 'BFB578DD9ED243A2BAA83B715A363A78',
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];