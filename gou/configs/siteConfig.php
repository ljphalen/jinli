<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

return array(
    'version'       => '20150420',
    'secretKey'     => '92fe5927095eaac53cd1aa3408da8135',
    'mainMenu'      => 'configs/mainMenu.php',
	'attachPath'    => BASE_PATH . '../attachs/gou/attachs/',
	'dataPath'      => BASE_PATH . 'data/',
	'logPath'       => BASE_PATH . 'data/logs/',
	'statisticPath' => BASE_PATH . 'data/statistic/',
	'rsaPemFile'    => BASE_PATH . 'configs/rsa_private_key.pem',
	'rsaPubFile'    => BASE_PATH . 'configs/rsa_public_key.pem',
    'alipayRsaPriFile'  => BASE_PATH . 'configs/alipay_rsa_private_key.pem',
    'alipayRsaPubFile'  => BASE_PATH . 'configs/alipay_rsa_public_key.pem',
    'cacertFile'    => BASE_PATH . 'configs/cacert.pem',
	'mobiles'       => '13809886150',
    'pdoLog'        => 'gou_pdo.log',   //位置: data/logs/gou_pdo.log
    'pdoLogSwitch'  => true,            //注意: pdo日志, 仅在开发模式中开启.!!!!!!!
);
