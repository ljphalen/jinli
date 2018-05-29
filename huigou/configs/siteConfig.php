<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

return array(
    'version' => '20120708',
    'secretKey' => '92fe5927095eaac53cd1aa3408da8135',
    'mainMenu' => 'configs/mainMenu.php',
	'attachPath' => BASE_PATH . 'data/attachs/',
	'dataPath' => BASE_PATH . 'data/',
	'logPath' => BASE_PATH . 'data/logs/',
	'rsaPemFile'=>BASE_PATH . 'configs/rsa_private_key.pem',
	'mobiles'=> '13809886150'
);
