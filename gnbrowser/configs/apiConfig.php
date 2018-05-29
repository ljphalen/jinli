<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//新闻配置
$config['news'] = array(
		'1'=>array(
				'name' => '凤凰要闻',
				'url' => 'http://i.ifeng.com/api/hiapn.api?hzssid=SYLB10&hzkey=ifeng_jinli&ch=zd_jl_llq'
		),
		'2'=>array(
				'name' => '凤凰军事',
				'url' => 'http://i.ifeng.com/api/hiapn.api?hzssid=JS83&hzkey=ifeng_jinli&ch=zd_jl_llq'
		),
		'3'=>array(
				'name' => '凤凰社会',
				'url' => 'http://i.ifeng.com/api/hiapn.api?hzssid=SH133&hzkey=ifeng_jinli&ch=zd_jl_llq'
		),
		'4'=>array(
				'name' => '凤凰娱乐',
				'url' => 'http://i.ifeng.com/api/hiapn.api?hzssid=YL53&hzkey=ifeng_jinli&ch=zd_jl_llq'
		)
);

//图片接口配置
$config['picture'] = array(
		'1'=>array(
				'name' => '明星',
				'url' => ''
		),
		'2'=>array(
				'name' => '美女',
				'url' => ''
		),
		'3'=>array(
				'name' => '军事',
				'url' => ''
		),
		'4'=>array(
				'name' => '光影故事',
				'url' => ''
		),
		'5'=>array(
				'name' => '汽车',
				'url' => ''
		)
);

return $config;