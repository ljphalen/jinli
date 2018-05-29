<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

return array(
    'version' => '20121228',
	'secretKey' => '92fe5927095eaac53cd1aa3408da8135',
    'mainMenu' => 'configs/mainMenu.php',
	'attachPath' => BASE_PATH . '../attachs/lock/attachs/',
	'dataPath' => BASE_PATH . 'data/',
	'uxPath' => '/data/www/lockfile',
	'tmpPath' => '/tmp/file',

	'rsaPemFile'=>BASE_PATH . 'configs/rsa_private_key.pem',
	'rsaPubFile'=>BASE_PATH . 'configs/rsa_public_key.pem',
);
