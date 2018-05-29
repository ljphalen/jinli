<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
return array(
    'version' => '20150119',
	'secretKey' => '92fe5927095eaac53cd1aa3408da8135',
	'rsaPemFile'=>BASE_PATH . 'configs/rsa_private_key.pem',
	'rsaPubFile'=>BASE_PATH . 'configs/rsa_public_key.pem',
    'mainMenu' => 'configs/mainMenu.php',
	'attachPath' => BASE_PATH . '../attachs/search/attachs',
	'appsPath' => BASE_PATH . '../attachs/ac/apps',
	'dataPath' => BASE_PATH . 'data/',
	'logPath' => BASE_PATH . 'data/logs/',
);
