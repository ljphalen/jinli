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

$config['Admin_Device'] = array(
	'name' => '设备管理',
	'items'=>array(
			array(
					'name' => '设备管理',
					'items' => array(
						'Admin_Wifi',
					)
				),
	)
);

$config['Admin_Ptner'] = array(
		'name' => '商户管理',
		'items'=>array(
				array(
						'name' => '商户管理',
						'items' => array(
								'Admin_Ptner',
						)
				),
		)
);

/* $config['Admin_Ad'] = array(
		'name' => '广告管理',
		'items'=>array(
				array(
						'name' => '广告管理',
						'items' => array(
								'Admin_Ad',
						)
				),
		)
); */


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/admin/user/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/admin/group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/admin/user/passwd'),
	'Admin_Wifi'=>array('设备列表',$entry . '/admin/wifi/index'),
	'Admin_Ad'=>array('广告列表',$entry . '/admin/ad/index'),
	'Admin_Ptner'=>array('商户列表', $entry . '/admin/ptner/index')
);

return array($config, $view);
