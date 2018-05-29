<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//微信配置
$config = array(
	'menu' => array(
		'button' => array(
			array(
				'name'       => '产品指南',
				'sub_button' => array(
					array('type' => 'click', 'name' => Lang::_('USER_CENTER'), 'key' => 'product_1'),
					array('type' => 'click', 'name' => '免费电话', 'key' => 'product_2')
				)
			),
			array(
				'name'       => '活动专区',
				'sub_button' => array(
					array('type' => 'click', 'name' => '关注领金币', 'key' => 'activity_1'),
					array('type' => 'click', 'name' => '最新活动', 'key' => 'activity_2')
				)
			),
			array(
				'name'       => '拍砖技巧',
				'sub_button' => array(
					array('type' => 'click', 'name' => '问题反馈', 'key' => 'interact_1'),
					array('type' => 'click', 'name' => '功能建议', 'key' => 'interact_2'),
					array('type' => 'click', 'name' => '小编陪你玩', 'key' => 'interact_3'),
				)
			),
		)
	),
	'conf' => array(
		'token'   => 'gioneebrowser',
		'appid'   => 'wx079ca669c7d62642',
		'secret'  => '0a59548572fd19ee105d0448cc263766',
		'api_url' => 'https://api.weixin.qq.com/cgi-bin/',
		'aes_key' => 'YdGinLRq8Esn5fj1KYxt576lZePAGIPNjqXKMQap0d9',
	),
);

return $config;