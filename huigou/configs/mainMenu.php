<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_System'] = array(
	'name' => '系统',
	'items'=>array(
			array(
				'name' => '用户',
				'items' => array(
				'Admin_User',
				'Admin_Group',
				'Admin_User_passwd'
			),
		),
	)
);

$config['Admin_Ad'] = array(
	'name' => '运营',
	'items' => array(
		'Admin_Ad',
		'Admin_Links',
		array(
				'name' => '导购管理',
				'items' => array(
						'Admin_Guidetype',
						'Admin_Guide',
				)
		),
		'Admin_React',
		'Admin_Gouuser',
		'Admin_Config',
		'Admin_Channel',
		'Admin_Sms',
		'Admin_Report'
	)
);


$config['Admin_Goods'] = array(
		'name' => '商品',
		'items' => array(
				'Admin_Goodslabel',
				'Admin_Subject',
				'Admin_Supplier',
				'Admin_Category',
				'Admin_Localgoods',
				'Admin_Taokegoods',
				'Admin_Taoke',
		)
);

$config['Admin_Order'] = array(
		'name' => '订单',
		'items' => array(
				'Admin_Order'
		)
);

$config['Admin_Activity'] = array(
		'name' => '活动',
		'items' => array(
				array(
					'name' => '免单活动',
					'items' => array(
						'Admin_Wantlog',
						'Admin_Orderfree',
						'Admin_Orderfreelog'
					)		
				),
		)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_pv',
				'Admin_Stat_ip',
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Ad'=>array('广告管理',$entry . '/Admin/Ad/index'),
	'Admin_Orderfree' => array('免单抽奖',$entry . '/Admin/Wantlog/orderfree'),
	'Admin_Orderfreelog' => array('免单日志',$entry . '/Admin/Orderfreelog/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Stat_ip'=>array('IP统计',$entry . '/Admin/Stat/ip'),
	'Admin_Links'=>array('链接管理',$entry . '/Admin/Links/index'),
	'Admin_React'=>array('用户反馈',$entry . '/Admin/React/index'),
	'Admin_Subject' => array('专题管理',$entry . '/Admin/Subject/index'),
	'Admin_Supplier' => array('供应商管理',$entry . '/Admin/Supplier/index'),
	'Admin_Category' => array('商品分类',$entry . '/Admin/Category/index'),
	'Admin_Channel' => array('渠道管理',$entry . '/Admin/Channel/index'),
	'Admin_Localgoods' => array('本地商品',$entry . '/Admin/LocalGoods/index'),
	'Admin_Taokegoods' => array('淘客商品',$entry . '/Admin/TaokeGoods/index'),
	'Admin_Wantlog' => array('我想要日志',$entry . '/Admin/Wantlog/index'),
	'Admin_Gouuser' => array('会员管理',$entry . '/Admin/Gouuser/index'),
	'Admin_Taoke' => array('淘客商品库',$entry . '/Admin/Taoke/index'),
	'Admin_Config' => array('配置管理',$entry . '/Admin/Config/index'),
	'Admin_Sms'=>array('短信平台', $entry . '/Admin/Sms/index'),
	'Admin_Order'=>array('订单管理', $entry . '/Admin/Order/index'),
	'Admin_Goodslabel'=>array('商品标签', $entry . '/Admin/Goodslabel/index'),
	'Admin_Guide' => array('导购管理',$entry . '/Admin/Guide/index'),
	'Admin_Guidetype' => array('导购分类',$entry . '/Admin/Guidetype/index'),
	'Admin_Report' => array('淘客报表',$entry . '/Admin/Report/index'),
);

return array($config, $view);
