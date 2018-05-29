<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//聚合阅读新闻配置
$config['jh_ifeng'] = array(
	'1' => array(
		'name'    => '头条',
		'url'     => 'http://i.ifeng.com/commendrss?id=jinlihezuo&ch=zd_jl_llq_tt&vt=5',
		'moreUrl' => ''
	),
	'2' => array(
		'name'    => '要闻',
		'url'     => 'http://i.ifeng.com/commendrss?id=dbyw_1&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/news/newsi?ch=zd_jl_dh&vt=5&dh=touch&mid=1elgqx'
	),
	'3' => array(
		'name'    => '社会',
		'url'     => 'http://i.ifeng.com/commendrss?id=sci_tt01&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/news/society/shi?ch=zd_jl_dh&vt=5&mid=1elgqx'
	),
	'4' => array(
		'name'    => '评论',
		'url'     => 'http://i.ifeng.com/commendrss?id=tt01&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/news/djch/youbao/dir?ch=zd_jl_dh&vt=5&cid=17901&mid=21RQuO'
	),
	'5' => array(
		'name'    => '军事',
		'url'     => 'http://i.ifeng.com/commendrss?id=mil_head&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/mil/mili?ch=zd_jl_dh&vt=5&dh=touch&mid=9yCLji'
	),
	'6' => array(
		'name'    => '财富',
		'url'     => 'http://i.ifeng.com/commendrss?id=caifuyaowen&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/finance/financei?ch=zd_jl_dh&vt=5&mid=1elgqx'
	),
	'7' => array(
		'name'    => '探索',
		'url'     => 'http://i.ifeng.com/commendrss?id=IndexDisc&ch=zd_jl_llq&vt=5',
		'moreUrl' => 'http://i.ifeng.com/tech/discovery/disci?ch=zd_jl_dh&vt=5&mid=1elgqx'
	)
);

$config['jh_sohu'] = array(
	'8'  => array(
		'name'    => '娱乐',
		'url'     => 'http://api.m.sohu.com/rss/fragment/?ids=1142&count=6&_trans_=000011_jl_browser',
		'moreUrl' => 'http://m.sohu.com/c/4/?_once_=000025_top_yule_v3'
	),
	'9'  => array(
		'name'    => '体育',
		'url'     => 'http://api.m.sohu.com/rss/fragment/?ids=1144&count=6&_trans_=000011_jl_browser',
		'moreUrl' => 'http://m.sohu.com/c/3/?_once_=000025_top_tiyu_v3'
	),
	'10' => array(
		'name'    => '星座',
		'url'     => 'http://api.m.sohu.com/rss/fragment/?ids=1148&count=3&_trans_=000011_jl_browser',
		'moreUrl' => 'http://m.sohu.com/c/9/?_once_=000025_top_xingzuo_v3'
	)
);

//金立内部接口
$config['jh_gionee'] = array(
	'11' => array(
		'name'    => '购物',
		'url'     => 'http://gou.gionee.com/api/news/navNews',
		'moreUrl' => 'http://gou.gionee.com/index/redirect?url_id=476'
	),
	'12' => array(
		'name'    => '数码',
		'url'     => 'http://www.wanjiquan.com/interface.php?mod=portal&do=article',
		'moreUrl' => 'http://m.wanjiquan.com/portal.php?mod=listall'
	),
);


//账户
if (stristr(ENV, 'product')) {
	$config['oauth'] = array(
		//帐号
		'gionee_user_appid'  => 'E6474C8858E645B39777695F84D619D8',
		'gionee_user_appkey' => '33D0897A9BE04BE6816A202559AA8830',
		//金立帐号
		'gionee_user_url'    => 'http://id.gionee.com',
	);

	$config['ofpay'] = array(
		/*欧飞手机充值接口*/
		'ofpay_userid'  => 'A985109',
		'ofpay_userpws' => 'wangji0829',
		'ofpay_url'     => 'http://api2.ofpay.com/',
		'ofpay_keyStr'  => 'OFCARD',
	);
} else {
	$config['oauth'] = array(
		//帐号
		'gionee_user_appid'  => '04486C5285F24A2293502110706F01D2',
		'gionee_user_appkey' => 'EA093343C9974BAD9F145BE70462C76B',
		//金立帐号
		'gionee_user_url'    => 'http://t-id.gionee.com',
	);

	$config['ofpay'] = array(
		/*欧飞手机充值接口*/
		'ofpay_userid'  => 'A985109',
		'ofpay_userpws' => 'wangji0829',
		'ofpay_url'     => 'http://api2.ofpay.com/',
		'ofpay_keyStr'  => 'OFCARD',
	);
}

return $config;