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
$config['Admin_Operation'] = array(
		'name'=>'应用运营',
		'items' => array(
			 		 array(
		 				'name'=>'运营管理',
		 				'items' => array(
		 						'Admin_Operation_Category',
		 						'Admin_Operation_Pgroup',
		 				)
			)
		)
);
$config['Admin_Store'] = array(
		'name'=>'应用仓库',
		'items' => array(
				array(
						'name'=>'属性管理',
						'items' => array(
								'Admin_Resource_Attribute',
								'Admin_Resource_Models',
								'Admin_Resource_Pgroup',
						)
				),
				array(
						'name'=>'应用管理',
						'items' => array(
								'Admin_Resource_Apps_index',
								'Admin_Resource_Apps_add',
						)
				)
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    	'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    	'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    	'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
		'Admin_Operation_Category'=>array('分类应用',$entry . '/Admin/Operation_Category/index'),
		'Admin_Operation_Pgroup'=>array('机组应用',$entry . '/Admin/Operation_Pgroup/index'),
		
		'Admin_Resource_Attribute'=>array('属性管理',$entry . '/Admin/Resource_Attribute/index'),
		'Admin_Resource_Models'=>array('机型管理',$entry . '/Admin/Resource_Models/index'),
		'Admin_Resource_Pgroup'=>array('机组管理',$entry . '/Admin/Resource_Pgroup/index'),
		'Admin_Resource_Apps_index'=>array('应用管理',$entry . '/Admin/Resource_Apps/index'),
		'Admin_Resource_Apps_add'=>array('添加应用',$entry . '/Admin/Resource_Apps/add'),
	
	
);

return array($config, $view);
