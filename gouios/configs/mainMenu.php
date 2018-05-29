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

$config['Admin_Yunying'] = array(
	'name' => '运营',
	'items' => array(
		'Admin_Config',
		'Admin_Ad',
		'Admin_Url',
		'Admin_Keywords',
		'Admin_Keywordslog',
		'Admin_Msg',
		'Admin_Start',
	)
);

$config['Admin_Eb'] = array(
		'name' => '电商渠道',
		'items' => array(
				'Admin_Channel',
		)
);

$config['Admin_Cod'] = array(
		'name' => '货到付款',
		'items' => array(
				'Admin_Cod_Type',
				array(
						'name' => '导购管理',
						'items' => array(
								'Admin_Cod_Guide',
								'Admin_Cod_Guide_txt'
						)
				),
		)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat',
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Ad'=>array('广告管理',$entry . '/Admin/Ad/index'),
	'Admin_Stat'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Supplier' => array('供应商管理',$entry . '/Admin/Supplier/index'),
	'Admin_Cod_Type' => array('分类管理',$entry . '/Admin/Cod_Type/Index'),
	'Admin_Channel' => array('渠道管理',$entry . '/Admin/Channel/index'),
	'Admin_Taoke' => array('淘客商品',$entry . '/Admin/Taoke/index'),
	'Admin_Config' => array('配置管理',$entry . '/Admin/Config/index'),
	'Admin_Guide' => array('导购管理',$entry . '/Admin/Guide/index'),
	'Admin_Guidetype' => array('导购分类',$entry . '/Admin/Guidetype/index'),
// 	'Admin_Cod_Guide' => array('导购管理',$entry . '/Admin/Cod_Guide/index'),
	'Admin_Cod_Guide' => array('图片',$entry . '/Admin/Cod_Guide/pic'),
	'Admin_Cod_Guide_txt' => array('文字链',$entry . '/Admin/Cod_Guide/txt'),
	'Admin_Url'=> array('分成渠道',$entry . '/Admin/Url/index'),
	'Admin_Msg'=>array('push消息管理',$entry . '/Admin/Msg/index'),		
	'Admin_Keywords' => array('关键字',$entry . '/Admin/Keywords/index'),
	'Admin_Keywordslog' => array('搜索排行',$entry . '/Admin/Keywordslog/index'),
	'Admin_Start' => array('闪屏管理',$entry . '/Admin/Start/index'),
);

return array($config, $view);
