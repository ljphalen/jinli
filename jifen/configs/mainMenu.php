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
	),	
);

$config['Admin_App'] = array(
	'name' => '应用',
	'items'=>array(
			'Admin_App',
	)
);

$config['Admin_User'] = array(
		'name' => '用户',
		'items'=>array(
				'Admin_Uuser',
				'Admin_Coin'
		)
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
	'Admin_App'=>array('应用管理',$entry . '/Admin/App/index'),
	'Admin_Uuser'=>array('用户管理',$entry . '/Admin/Uuser/index'),
	'Admin_Coin'=>array('流通日志',$entry . '/Admin/Coin/log'),
);

return array($config, $view);
