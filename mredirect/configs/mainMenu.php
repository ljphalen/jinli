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
		'Admin_Url',
		'Admin_Urlgionee',
	    'Admin_Taobaourl',
	    'Admin_Gntaobaourl',
	)
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
	'Admin_Url'=> array('分成渠道',$entry . '/Admin/Url/index'),
	'Admin_Urlgionee'=> array('金立分成渠道',$entry . '/Admin/Urlgionee/index'),
    'Admin_Taobaourl'=> array('淘热卖地址',$entry . '/Admin/Taobaourl/index'),
    'Admin_Gntaobaourl'=> array('金立淘热卖地址',$entry . '/Admin/Gntaobaourl/index'),
);

return array($config, $view);
