<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
return array(
	'secretKey' => '92fe5927095eaac53cd1aa3408da8135',
	'attachPath' => BASE_PATH . '../attachs/gameweixin/attachs/',
	'dataPath' => BASE_PATH . 'data/',
    'tmpFilePath' => BASE_PATH.'data/tmp/',
	'logPath' => BASE_PATH . '../logs/gameweixin/',
	'gameSecreKey' => 'f84c04c1611366ce43d3cd07b8c65217',//请求game服务器key
    'giftSecreKey' => '8cd5426ce8a336ac78a3fc88d90762d7',//加密抢礼包链接
);
