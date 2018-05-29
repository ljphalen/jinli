<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//新闻配置
$config['news'] = array(
		'1'=>array(
				'name' => '头条',
				'url' => 'http://i.ifeng.com/commendrss?id=jinlihezuo&ch=zd_jl_llq_tt&vt=5'
		),
		'2'=>array(
				'name' => '新闻',
				'url' => 'http://i.ifeng.com/commendrss?id=dbyw_1&ch=zd_jl_llq_xw&vt=5'
		),
		'3'=>array(
				'name' => '体育',
				'url' => 'http://i.ifeng.com/commendrss?id=sports_yw&ch=zd_jl_llq_ty&vt=5'
		),
		'4'=>array(
				'name' => '军事',
				'url' => 'http://i.ifeng.com/commendrss?id=mil_head&ch=zd_jl_llq_js&vt=5'
		),
		'5'=>array(
				'name' => '娱乐',
				'url' => 'http://i.ifeng.com/commendrss?id=yl_lb01&ch=zd_jl_llq_yl&vt=5'
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