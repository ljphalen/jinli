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

$config['Admin_user'] = array(
    'name' => '会员',
    'items'=>array(
        'Admin_Olauser',
    )
);


$config['Admin_Yunying'] = array(
    'name' => '运营',
    'items'=>array(
        array(
            'name' => '配置',
            'items' => array(
                'Admin_Config',
            ),
        ),
        'Admin_Ad',
        'Admin_Area',
        'Admin_Category',
        'Admin_Job',
        'Admin_Signup',
        'Admin_Favorite',
        'Admin_Report'
    )
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Goods'=>array('商品管理',$entry . '/Admin/Goods/index'),
    'Admin_Config' => array('系统配置',$entry . '/Admin/Config/index'),
    'Admin_Ad'=> array('广告管理',$entry . '/Admin/Ad/index'),
    'Admin_Olauser'=> array('会员管理',$entry . '/Admin/Olauser/index'),
    'Admin_Area'=> array('区域管理',$entry . '/Admin/Area/index'),
    'Admin_Category'=> array('职位分类',$entry . '/Admin/Category/index'),
    'Admin_Job'=> array('职位管理',$entry . '/Admin/Job/index'),
    'Admin_Signup'=> array('报名管理',$entry . '/Admin/Signup/index'),
    'Admin_Favorite'=> array('收藏管理',$entry . '/Admin/Favorite/index'),
    'Admin_Report'=> array('举报管理',$entry . '/Admin/Report/index'),
);

return array($config, $view);
