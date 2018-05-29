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

$config['Admin_Goods'] = array(
    'name' => '商品',
    'items'=>array(
        'Admin_Goods',
        'Admin_Goodsmall'
    )
);


$config['Admin_Yunying'] = array(
    'name' => '运营',
    'items'=>array(
        'Admin_Config',
        'Admin_Ad',
        'Admin_Country',
        'Admin_Category',
        'Admin_Brand',
        'Admin_Tag',
        'Admin_Mall',
        'Admin_Footer',
        'Admin_Info',
        array(
            'name' => '搜索管理',
            'items' => array(
                'Admin_Search',
            ),
        ),
    )
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User' => array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group' => array('用户组管理', $entry . '/Admin/Group/index'),
    'Admin_User_passwd' => array('修改密码', $entry . '/Admin/User/passwd'),
    'Admin_Ad' => array('广告管理', $entry . '/Admin/Ad/index'),
    'Admin_Config' => array('配置管理', $entry . '/Admin/Config/index'),
    'Admin_Country' => array('国家管理', $entry . '/Admin/Country/index'),
    'Admin_Category' => array('分类管理', $entry . '/Admin/Category/index'),
    'Admin_Goods' => array('商品管理', $entry . '/Admin/Goods/index'),
    'Admin_Goodsmall' => array('商品商家', $entry . '/Admin/Goodsmall/index'),
    'Admin_Brand' => array('品牌管理', $entry . '/Admin/Brand/index'),
    'Admin_Tag' => array('标签管理', $entry . '/Admin/Tag/index'),
    'Admin_Mall' => array('商家管理', $entry . '/Admin/Mall/index'),
    'Admin_Info' => array('资讯管理', $entry . '/Admin/Info/index'),
    'Admin_Footer' => array('底部管理', $entry . '/Admin/Footer/index'),
    'Admin_Search' => array('搜索排名', $entry . '/Admin/Search/index'),
);

return array($config, $view);
