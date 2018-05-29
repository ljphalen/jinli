<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//认证相关配置信息

//欧飞接口配置
$config['ofpay'] = array(
	'ofpay_userid'  => 'A985109',
	'ofpay_userpws' => 'wangji0829',
	'ofpay_url'     => 'http://api2.ofpay.com/',
	'ofpay_keyStr'  => 'OFCARD',
);

//用户登陆配置
if (stristr(ENV, 'product')) {
	$config['oauth'] = array(
		'gionee_user_appid'  => 'E6474C8858E645B39777695F84D619D8',
		'gionee_user_appkey' => '33D0897A9BE04BE6816A202559AA8830',
		'gionee_user_url'    => 'http://id.gionee.com',
	);
} else {
	$config['oauth'] = array(
		'gionee_user_appid'  => '04486C5285F24A2293502110706F01D2',
		'gionee_user_appkey' => 'EA093343C9974BAD9F145BE70462C76B',
		'gionee_user_url'    => 'http://t-id.gionee.com',
	);
}
return $config;
		