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
	'Admin_Config',
	)
);

$config['Admin_Product'] = array(
    'name'=>'运营',
	'items' => array(
	'Admin_Ad',
	array(
			'name'=>'产品管理',
			'items' => array(
					'Admin_Series',
					'Admin_Models',
					'Admin_Product',
			)
	),
	array(
			'name'=>'专题管理',
			'items' => array(
					'Admin_Subject',
					'Admin_Gousubject',
					'Admin_Gamesubject',
					'Admin_Imcsubject',
			)
	),
	array(
			'name'=>'资源管理',
			'items' => array(
					'Admin_Resource',
					'Admin_Resourceassign',
			)
	),
	array(
			'name'=>'配件管理',
			'items' => array(
					'Admin_Parts',
					'Admin_Partsassign',
			)
	),
	'Admin_News',	
	array(
			'name'=>'签到管理',
			'items' => array(
					'Admin_Signinimg',
					'Admin_Prize',
			)
	),
	array(
			'name'=>'会员管理',
			'items' => array(
					'Admin_Gioneeuser',
					'Admin_Lotterylog',
			)
	),
	'Admin_Area',
	'Admin_Address',
	'Admin_Questions',
	'Admin_Picture',
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
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
    'Admin_Product'=>array('产品列表',$entry . '/Admin/Product/index'),	
	'Admin_Series'=>array('系列管理',$entry . '/Admin/Series/index'),
    'Admin_Ad'=>array('广告管理',$entry . '/Admin/Ad/index'),
	'Admin_Config'=>array('系统设置',$entry . '/Admin/Config/index'),
    'Admin_News'=>array('新闻管理',$entry . '/Admin/News/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Crcategory'=>array('点击量统计',$entry . '/Admin/Crcategory/index'),
	'Admin_Series'=>array('系列管理',$entry . '/Admin/Series/index'),
	'Admin_Models'=>array('机型管理',$entry . '/Admin/Models/index'),
	'Admin_Gioneeuser'=>array('会员管理',$entry . '/Admin/Gioneeuser/index'),
	'Admin_Signinimg'=>array('签到图片管理',$entry . '/Admin/Signinimg/index'),
	'Admin_Prize'=>array('奖品管理',$entry . '/Admin/Prize/index'),
	'Admin_Lotterylog'=>array('抽奖日志管理',$entry . '/Admin/Lotterylog/index'),
	'Admin_Resource'=>array('资源管理',$entry . '/Admin/Resource/index'),
	'Admin_Parts'=>array('配件管理',$entry . '/Admin/Parts/index'),
	'Admin_Resourceassign'=>array('分配资源管理',$entry . '/Admin/Resourceassign/index'),
	'Admin_Partsassign'=>array('分配配件管理',$entry . '/Admin/Partsassign/index'),
	'Admin_Area'=>array('省市管理',$entry . '/Admin/Area/index'),
	'Admin_Address'=>array('网点管理',$entry . '/Admin/Address/index'),
	'Admin_Questions'=>array('常见问题管理',$entry . '/Admin/Questions/index'),
	'Admin_Picture'=>array('图片管理',$entry . '/Admin/Picture/index'),
	'Admin_Subject'=>array('专题管理',$entry . '/Admin/Subject/index'),
	'Admin_Gousubject'=>array('金立购专题',$entry . '/Admin/Gousubject/index'),
	'Admin_Gamesubject'=>array('游戏专题',$entry . '/Admin/Gamesubject/index'),
	'Admin_Imcsubject'=>array('IMC专题',$entry . '/Admin/Imcsubject/index'),
	'Admin_Lottery'=>array('彩票管理',$entry . '/Admin/Lottery/index'),
);

return array($config, $view);
