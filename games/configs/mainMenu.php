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

$config['Admin_Games'] = array(
    'name'=>'游戏大厅',
	'items' => array(
		'Admin_Ad',
		'Admin_Subject',
		'Admin_Type',
		'Admin_Games',
		'Admin_Recommend',
		'Admin_Package'
	)
);

$config['Admin_Game'] = array(
		'name'=>'游戏网站',
		'items' => array(
				'Admin_Game_Ad',
				'Admin_Game_Game',
		)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_pv',
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Ad'=>array('广告管理',$entry . '/Admin/Ad/index'),
	'Admin_Game_Ad'=>array('广告管理',$entry . '/Admin/Game_Ad/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Games'=>array('游戏管理',$entry . '/Admin/Games/index'),
	'Admin_Game_Game'=>array('游戏管理',$entry . '/Admin/Game_Game/index'),
	'Admin_Type'=>array('分类管理',$entry . '/Admin/Type/index'),
	'Admin_Subject'=>array('游戏专题',$entry . '/Admin/Subject/index'),
	'Admin_Recommend'=>array('推荐管理', $entry . '/Admin/Recommend/index'),
	'Admin_Package'=>array('包名管理', $entry . '/Admin/Package/index'),
);

return array($config, $view);
