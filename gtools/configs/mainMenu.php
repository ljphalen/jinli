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

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    	'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    	'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    	'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
);

return array($config, $view);
