<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
return array(
    'version' => '201506181200',
	'secretKey' => '92fe5927095eaac53cd1aa3408da8135',
        'mainMenu' => 'configs/mainMenu.php',
	'rsaPemFile'=>BASE_PATH . 'configs/rsa_private_key.pem',
	'rsaPubFile'=>BASE_PATH . 'configs/rsa_public_key.pem',
	'attachPath' => BASE_PATH . '../attachs/game/attachs/',
	'guessPath' => BASE_PATH . '../attachs/guess',
	'dataPath' => BASE_PATH . 'data/',
	'logPath' => BASE_PATH . '../logs/game/',
	'aaptPath'=> BASE_PATH . 'data/aapt/aapt'
);
