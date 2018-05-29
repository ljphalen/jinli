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

$config['Admin_Product'] = array(
    'name'=>'运营',
	'items' => array(
		'Admin_Product',
		array(
				'name'=>'网址导航',
				'items' => array(
					'Admin_Navtype',
					'Admin_Nav',
				)
		),
		'Admin_Ad',
		'Admin_News',
		'Admin_Redirect',
		'Admin_Game',
		array(
				'name' => '频道管理',
				'items' => array(
						'Admin_Indexchannel',
						'Admin_Channelcontent',
				)
		),
		array(
				'name' => '书签管理',
				'items' => array(
						'Admin_Models',
						'Admin_Recmark',
						'Admin_Recsitetype',
						'Admin_Recsite',
						'Admin_Recurl',
				)
		),
	)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_pv',
				'Admin_Stat_ip',
				'Admin_Crcategory',
				'Admin_Clicktype',
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Product'=>array('产品列表',$entry . '/Admin/Product/index'),
    'Admin_Navtype'=>array('导航分类',$entry . '/Admin/Navtype/index'),
    'Admin_Nav'=>array('导航列表',$entry . '/Admin/Nav/index'),
    'Admin_Ad'=>array('广告管理',$entry . '/Admin/Ad/index'),
    'Admin_News'=>array('新闻管理',$entry . '/Admin/News/index'),
	'Admin_Redirect'=>array('跳转管理',$entry . '/Admin/Redirect/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Stat_ip'=>array('IP统计',$entry . '/Admin/Stat/ip'),
	'Admin_Crcategory'=>array('点击量统计',$entry . '/Admin/Crcategory/index'),
	'Admin_Game'=>array('游戏管理',$entry . '/Admin/Game/index'),
	'Admin_Indexchannel'=>array('首页频道管理',$entry . '/Admin/Indexchannel/index'),
	'Admin_Channelcontent'=>array('频道内容管理',$entry . '/Admin/Channelcontent/index'),
	'Admin_Clicktype'=>array('首页统计',$entry . '/Admin/Clicktype/index'),
	'Admin_Recmark'=>array('书签管理',$entry . '/Admin/Recmark/index'),
	'Admin_Models'=>array('机型管理',$entry . '/Admin/Models/index'),
	'Admin_Recsitetype'=>array('站点分类管理',$entry . '/Admin/Recsitetype/index'),
	'Admin_Recsite'=>array('推荐站点管理',$entry . '/Admin/Recsite/index'),
	'Admin_Recurl'=>array('推荐网址管理',$entry . '/Admin/Recurl/index'),
);

return array($config, $view);
