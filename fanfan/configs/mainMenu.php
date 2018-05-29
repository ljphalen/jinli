<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_System'] = array(
	'name'  => '系统',
	'items' => array(
		array(
			'name'  => '用户',
			'items' => array(
				'Admin_User',
				'Admin_Group',
				'Admin_User_passwd'
			),
		),
		'Admin_Widget_webp',
	)
);

$config['Admin_Widgets'] = array(
	'name'  => '数据管理',
	'items' => array(
		array(
			'name'  => '配置管理',
			'items' => array(
				'Admin_Widgetconn',
				'Admin_W3_Config',
			),
		),
		array(
			'name'  => '订阅管理',
			'items' => array(
				'Admin_Widget',
				'Admin_Widget_Column',
				'Admin_W3_Column',
				'Admin_Widget_ColumnInit',
			),
		),
		array(
			'name'  => '数据源管理',
			'items' => array(
				'Admin_Widget_Source',
				'Admin_Widget_Cp',
				'Admin_Widget_Freq',
				'Admin_W3_Topic',
			),
		),
		array(
			'name'  => '机型管理',
			'items' => array(
				'Admin_Widget_Spec',
				'Admin_Widget_SpecInit',
			),
		),
		array(
			'name'  => 'CP參數管理',
			'items' => array(
				'Admin_Widget_CpMange',
				'Admin_Widget_CpClient',
				'Admin_W3_Cp_setting',
				'Admin_Widget_Down',
			),
		),

		array(
			'name'  => 'PUSH',
			'items' => array(
				'Admin_Widget_Push_config',
				'Admin_Widget_Push',
			),
		),
	)
);


$config['Admin_Stat'] = array(
	'name'  => '统计',
	'items' => array(
		array(
			'name'  => '公共数据',
			'items' => array(
				'Admin_Stat',
				'Admin_Stat_logcall',
				'Admin_Stat_lognews',
				'Admin_Stat_logres',
				'Admin_Stat_logdown',
			),
		),
		array(
			'name'  => 'v2.0',
			'items' => array(
				'Admin_Widget_Stats',
				'Admin_Widget_User',
			),
		),

		array(
			'name'  => 'v3.0',
			'items' => array(
				'Admin_W3_Stats',
				'Admin_W3_User',
			),
		),
		'Admin_Stat_monkeynum',
		'Admin_Stat_logexport'
	)
);


$entry = Yaf_Registry::get('config')->adminroot;
$view  = array(
	'Admin_User'               => array('用户管理', $entry . '/Admin/User/index'),
	'Admin_Group'              => array('用户组管理', $entry . '/Admin/Group/index'),
	'Admin_User_passwd'        => array('修改密码', $entry . '/Admin/User/passwd'),

	'Admin_Widget'             => array('列表(v1.0)', $entry . '/Admin/Widget/editSource'),
	'Admin_Widget_Column'      => array('列表(v2.0)', $entry . '/Admin/Widget_Column/spec'),
	'Admin_Widget_ColumnInit'  => array('预置', $entry . '/Admin/Widget_Column/init'),

	'Admin_Widget_Freq'        => array('推送干预管理', $entry . '/Admin/Widget_Freq/index'),

	'Admin_Widgetconn'         => array('配置(v2.0)', $entry . '/Admin/Widget/conn'),
	'Admin_Widget_Source'      => array('数据仓库', $entry . '/Admin/Widget_Source/index'),
	'Admin_Widget_Cp'          => array('来源管理', $entry . '/Admin/Widget_Cp/index'),

	'Admin_Stat'               => array('PV统计', $entry . '/Admin/Stat/pv'),
	'Admin_Stat_logcall'       => array('接口统计', $entry . '/Admin/Stat/logcall'),
	'Admin_Stat_lognews'       => array('新闻统计', $entry . '/Admin/Stat/lognews'),
	'Admin_Stat_logres'        => array('资源统计', $entry . '/Admin/Stat/logres'),
	'Admin_Stat_logdown'       => array('下载统计', $entry . '/Admin/Stat/logdown'),
	'Admin_Widget_Stats'       => array('统计数据', $entry . '/Admin/Widget_Stats/index'),
	'Admin_Widget_User'        => array('用户列表', $entry . '/Admin/Widget_User/index'),

	'Admin_Widget_Spec'        => array('系列管理', $entry . '/Admin/Widget_Spec/index'),
	'Admin_Widget_SpecInit'    => array('预置管理', $entry . '/Admin/Widget_Spec/init'),

	'Admin_Widget_CpMange'     => array('版本(v1.0-v2.0.5)', $entry . '/Admin/Widget_Cp/manage'),
	'Admin_Widget_CpClient'    => array('版本(v2.0.6)', $entry . '/Admin/Widget_Cp/clientList?cp_id=1'),

	'Admin_Widget_Down'        => array('下载资源管理', $entry . '/Admin/Widget_Down/index'),

	'Admin_Widget_Push_config' => array('配置', $entry . '/Admin/Widget_Push/config'),
	'Admin_Widget_Push'        => array('推送', $entry . '/Admin/Widget_Push/index'),

	'Admin_W3_Config'          => array('配置(v3.0)', $entry . '/Admin/W3_Config/edit'),
	'Admin_W3_Cp'              => array('CP', $entry . '/Admin/W3_Cp/index'),
	'Admin_W3_Cp_upload'       => array('CP', $entry . '/Admin/W3_Cp/index'),
	'Admin_W3_Cp_setting'      => array('详情页(v3.0)', $entry . '/Admin/W3_Cp/setting?cp_id=105'),
	//'Admin_W3_Cp_res'    => array('CP资源信息', $entry . '/Admin/W3_Cp/res?cp_id=105'),
	'Admin_W3_Column'          => array('列表(v3.0)', $entry . '/Admin/W3_Column/list'),
	'Admin_W3_User'            => array('用户列表', $entry . '/Admin/W3_User/index'),
	'Admin_W3_Stats'           => array('统计数据', $entry . '/Admin/W3_Stats/index'),
	'Admin_Widget_webp'        => array('webp图片转换', $entry . '/Admin/Widget/webp'),
	'Admin_Stat_monkeynum'     => array('接口性能', $entry . '/Admin/Stat/monkeynum'),
	'Admin_Stat_logexport'     => array('日志导出', $entry . '/Admin/Stat/logexport'),
	'Admin_W3_Topic'           => array('专题列表', $entry . '/Admin/W3_Topic/list'),

);

return array($config, $view);
