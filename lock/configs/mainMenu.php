<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_System'] = array(
	'name' => '系统',
	'items'=>array(
			'Admin_Config',
			array(
				'name' => '用户',
				'items' => array(
				'Admin_User',
				'Admin_Group',
				'Admin_Passwd'
			),
			
		)
	)	
);

$config['Admin_File'] = array(
    'name'=>'金立锁屏',
	'items' => array(
		'Admin_File',
		'Admin_Filetype',
		'Admin_Size',
	)
);

$config['Admin_QiiFile'] = array(
		'name'=>'精灵锁屏',
		'items' => array(
				'Admin_Qiilabel',
				'Admin_Qiifile',
		)
);

$config['Admin_Lock'] = array(
		'name'=>'锁屏管理',
		'items' => array(
				'Admin_Lock',
				'Admin_Subject'
		)
);

$config['Admin_Log'] = array(
		'name'=>'日志',
		'items' => array(
				'Admin_Adminlog',
		)
);

$config['Admin_Push'] = array(
		'name'=>'Push管理',
		'items' => array(
				'Admin_Rid',
				'Admin_Msg',
				'Admin_Pushlog',
		)
);

$config['Admin_Message'] = array(
		'name'=>'消息',
		'items' => array(
				'Admin_Message',
		)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_pv',
				'Admin_Crcategory',
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_Passwd'=>array('修改密码',$entry . '/Admin/Passwd/index'),
	'Admin_Config'=>array('系统设置',$entry . '/Admin/Config/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Crcategory'=>array('点击量统计',$entry . '/Admin/Crcategory/index'),
	'Admin_Clicktype'=>array('首页统计',$entry . '/Admin/Clicktype/index'),
	'Admin_File'=>array('文件管理',$entry . '/Admin/File/index'),
	'Admin_Filetype'=>array('文件分类管理',$entry . '/Admin/Filetype/index'),
	'Admin_Size'=>array('分辨率管理',$entry . '/Admin/Size/index'),
	'Admin_Adminlog'=>array('日志管理',$entry . '/Admin/Adminlog/index'),
	'Admin_Message'=>array('消息管理',$entry . '/Admin/Message/index'),
	'Admin_Subject'=>array('专题管理',$entry . '/Admin/Subject/index'),
	'Admin_Qiilabel'=>array('场景分类',$entry . '/Admin/Qiilabel/index'),
	'Admin_Qiifile'=>array('场景管理',$entry . '/Admin/Qiifile/index'),
	'Admin_Lock'=>array('锁屏管理',$entry . '/Admin/Lock/index'),
	'Admin_Config'=>array('系统设置',$entry . '/Admin/Config/index'),
	'Admin_Rid'=>array('Rid管理',$entry . '/Admin/Rid/index'),
	'Admin_Msg'=>array('Push消息管理',$entry . '/Admin/Msg/index'),
	'Admin_Pushlog'=>array('Push日志管理',$entry . '/Admin/Pushlog/index'),
);

return array($config, $view);
