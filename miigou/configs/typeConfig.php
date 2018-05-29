<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
		'goods_type' => array(
				1=>'成人用品',
				2=>'男用情趣',
				3=>'女用情趣',
				4=>'润滑延时',
				5=>'安全套',
				6=>'情趣内衣',
				7=>'双人情趣',
				8=>'交流吧商品推荐',
		),
		'forum_type' => array(
				1=>'官方公告',
				2=>'两性攻略',
				3=>'交流求助',
				4=>'内涵漫画'
		)
);
return $config;
