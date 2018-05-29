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
         'Admin_Goods'
    )
);

$config['Admin_Fjuser'] = array(
    'name' => '会员',
    'items'=>array(
        'Admin_Fjuser',
    )
);

$config['Admin_Order'] = array(
    'name' => '订单',
    'items'=>array(
        'Admin_Address',
        'Admin_Order',
    )
);


$config['Admin_Yunying'] = array(
    'name' => '运营',
    'items'=>array(
        array(
            'name' => '配置',
            'items' => array(
                'Admin_Config'
            ),
        ),
        'Admin_Ad',
    )
);

$config['Admin_Craw'] = array(
    'name' => '爬虫',
    'items'=>array(
        'Admin_Craw_Goods',
        'Admin_Craw_Category',
        'Admin_Craw_Urls'
    ),
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Goods'=>array('商品管理',$entry . '/Admin/Goods/index'),
    'Admin_Config' => array('系统配置',$entry . '/Admin/Config/index'),
    'Admin_Ad'=> array('广告管理',$entry . '/Admin/Ad/index'),
    'Admin_Fjuser'=> array('会员管理',$entry . '/Admin/Fjuser/index'),
    'Admin_Order'=> array('订单管理',$entry . '/Admin/Order/index'),
    'Admin_Address'=> array('提货地址管理',$entry . '/Admin/Address/index'),

    'Admin_Craw_Goods'=> array('商品管理', $entry . '/Admin/Craw_Goods/index'),
    'Admin_Craw_Category'=> array('分类管理', $entry . '/Admin/Craw_Category/index'),
    'Admin_Craw_Urls'=> array('链接管理', $entry . '/Admin/Craw_Urls/index'),
);

return array($config, $view);
