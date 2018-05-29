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
			array(
					'name' => '搜索',
					'items' => array(
							'Admin_Keywords',
							'Admin_Keywordslog'
					)
			),
			'Admin_Goods',
	),
);

$config['Admin_Forum'] = array(
		'name' => '交流吧',
		'items' => array(
				'Admin_Forum',
				'Admin_Forumreply',
		),
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat',
				'Admin_Stat_uv',
		)
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
	'Admin_Stat'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Stat_uv'=>array('UV统计',$entry . '/Admin/Stat/uv'),
	'Admin_Config' => array('配置管理',$entry . '/Admin/Config/index'),
	'Admin_Keywords' => array('关键字',$entry . '/Admin/Keywords/index'),
	'Admin_Keywordslog' => array('搜索排行',$entry . '/Admin/Keywordslog/index'),
	'Admin_Goods' => array('商品管理',$entry . '/Admin/Goods/index'),
	'Admin_Forum' => array('帖子管理',$entry . '/Admin/Forum/index'),
	'Admin_Forumreply' => array('回帖管理',$entry . '/Admin/Forumreply/index'),
);

return array($config, $view);
