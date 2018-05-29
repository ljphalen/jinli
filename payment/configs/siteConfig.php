<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
return array(
    'version' => '20141121',
    'mainMenu' => 'configs/mainMenu.php',
	'rsaPemFile'=>BASE_PATH . 'configs/rsa_private_key.pem',
	'rsaPubFile'=>BASE_PATH . 'configs/rsa_public_key.pem',
	'attachPath' => BASE_PATH . '../attachs/game/attachs/',
	'dataPath' => BASE_PATH . 'data/',
	'logPath' => BASE_PATH . 'data/logs/'
);
