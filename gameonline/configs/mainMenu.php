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

$config['Admin_Content'] = array(
	'name'=>'内容运营',
	'items'=>array(
			'Admin_Client_Category',
			'Admin_Client_Subject',
			'Admin_Client_Game',
// 			'Admin_Client_New',
			'Admin_Resource_Keyword',
		)	
);



$config['Admin_Content'] = array(
		'name'=>'广告运营',
		'items'=>array(
				array(
					'name'=>'H5',
					'items'=>array(
							'Admin_Ad_Turn',
							'Admin_Ad_Recommend',
							'Admin_Ad_Subject',
							'Admin_Ad_Public',
							'Admin_Ad_New',
							'Admin_Ad_Tuijian',
						)
					),
				array(
					'name'=>'客户端',
					'items'=>array(
							'Admin_Client_Ad_Turn',
							'Admin_Client_Ad_Subject',
							'Admin_Client_Ad_Besttj',
							'Admin_Client_Column',
							'Admin_Client_Ad_Channel',
							'Admin_Client_Ad_Start',
							'Admin_Client_Ad_Recommend',
							'Admin_Client_Ad_Activity',
							'Admin_Client_Columnnew',
							//'Admin_Client_Ad_Public',
						)
					),
				array(
						'name'=>'web',
						'items'=>array(
								'Admin_Web_Turn',
								'Admin_Web_Playgame',
						)
				),
		)
);

$config['Admin_YunYing'] = array(
		'name'=>'内容运营',
		'items' => array(
				array(
					'name'=>'账号管理',
					'items'=>array(
							'Admin_Account_User'
							)
				),
				'Admin_Client_Category',
				'Admin_Client_Subject',
				'Admin_Client_News',
				'Admin_Client_Evaluation',
				'Admin_Client_Strategy',
				'Admin_Client_Installe',
				'Admin_Client_Single',
				'Admin_Client_Web',
				array(
						'name'=>'抽奖活动',
						'items'=>array(
								'Admin_Client_Activity',
								'Admin_Festival_Autumn',
								'Admin_Festival_Guoqing'
								)
				),
				'Admin_Client_Hd',
				'Admin_Client_Gift',
				'Admin_Client_Rank',
				'Admin_Resource_Keyword',
				array(
						'name'=>'配置管理',
						'items'=>array(
								'Admin_Config',
								'Admin_Web_Config',
								'Admin_Client_Set'
						)
				),
				'Admin_Client_Config',
				'Admin_Client_Push',
				'Admin_Client_Taste',
				'Admin_Client_Comment',
				'Admin_Resource_Score',
				'Admin_React',
		)
);

$config['Admin_Store'] = array(
		'name'=>'内容库',
		'items' => array(
				array(
						'name'=>'属性管理',
						'items' => array(
								'Admin_Resource_Attribute',
								'Admin_Resource_Type',
								'Admin_Resource_Property',
								'Admin_Resource_Pgroup',
								'Admin_Resource_Label',
						)
				),
				array(
						'name'=>'游戏仓库',
						'items' => array(
								'Admin_Resource_Games_index',
								'Admin_Resource_Games_entering',
								'Admin_Resource_Sync_index',
								//'Admin_Resource_New',
						)
				)
		)
);

$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_pv',
				'Admin_Stat_uv',
				'Admin_Stat_monkeynum'
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
	'Admin_Config'=>array('游戏大厅',$entry . '/Admin/Config/index'),
    'Admin_Ad_Turn'=>array('轮播广告',$entry . '/Admin/Ad_Turn/index'),
	'Admin_Ad_Recommend'=>array('推荐游戏',$entry . '/Admin/Ad_Recommend/index'),
	'Admin_Ad_Subject'=>array('推荐专题',$entry . '/Admin/Ad_Subject/index'),
	'Admin_Ad_Public'=>array('宣传广告',$entry . '/Admin/Ad_Public/index'),
	'Admin_Ad_New'=>array('最新游戏',$entry . '/Admin/Ad_New/index'),
	'Admin_Category'=>array('游戏分类',$entry . '/Admin/Category/index'),
	'Admin_Subject'=>array('游戏专题',$entry . '/Admin/Subject/index'),
	'Admin_Label'=>array('游戏标签',$entry . '/Admin/Label/index'),
	'Admin_Price'=>array('游戏资费',$entry . '/Admin/Price/index'),
	'Admin_React'=>array('意见反馈',$entry . '/Admin/React/index'),
	'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
	'Admin_Stat_uv'=>array('UV统计',$entry . '/Admin/Stat/uv'),
	'Admin_Stat_monkeynum'      => array('接口性能', $entry . '/Admin/Stat/monkeynum'),
	'Admin_Game'=>array('游戏管理',$entry . '/Admin/Game/index'),
	'Admin_Web_Turn'=>array('轮播广告',$entry . '/Admin/Web_Turn/index'),	
	'Admin_Web_Playgame'=>array('大家玩列表',$entry . '/Admin/Web_Playgame/index'),
		
	'Admin_Resource_Games_index'=>array('内容管理',$entry . '/Admin/Resource_Games/index?type=1'),
	'Admin_Resource_Games_entering'=>array('运营管理',$entry . '/Admin/Resource_Games/entering?type=2'),
	'Admin_Resource_Sync_index' => array('同步日志',$entry . '/Admin/Resource_Sync/index'),
	'Admin_Resource_Category'=>array('游戏分类',$entry . '/Admin/Resource_Category/index'),
	'Admin_Client_Game'=>array('游戏管理',$entry . '/Admin/Client_Game/index'),
	'Admin_Client_Category'=>array('分类管理',$entry . '/Admin/Client_Category/index'),
	'Admin_Client_Subject'=>array('专题管理',$entry . '/Admin/Client_Subject/index'),
	'Admin_Client_Ad_Turn'=>array('置顶广告',$entry . '/Admin/Client_Ad_Turn/index'),
	'Admin_Client_Ad_Recommend'=>array('广告位1',$entry . '/Admin/Client_Ad_Recommend/index'),
	'Admin_Client_Ad_Public'=>array('广告位2',$entry . '/Admin/Client_Ad_Public/index'),
	'Admin_Client_Ad_Subject'=>array('首页推荐列表',$entry . '/Admin/Client_Ad_Subject/index'),
	'Admin_Resource_Attribute'=>array('属性管理',$entry . '/Admin/Resource_Attribute/index'),
	'Admin_Resource_Type'=>array('机型管理',$entry . '/Admin/Resource_Type/index'),
	'Admin_Resource_Property'=>array('添加属性',$entry . '/Admin/Resource_Attribute/add'),
	'Admin_Client_Config'=>array('猜你喜欢',$entry . '/Admin/Client_Config/index'),
	'Admin_Super_Resource'=>array('资源管理', $entry. '/Admin/Super_Resource/index'),
	'Admin_Resource_Keyword'=>array('搜索管理', $entry. '/Admin/Resource_Keyword/index'),
	'Admin_Client_Rank'=>array('排行管理',$entry . '/Admin/Client_Rank/new'),
	'Admin_Resource_New'=>array('新消息',$entry . '/Admin/Resource_Games/add_new'),
	'Admin_Client_News'=>array('新闻管理',$entry . '/Admin/Client_News/index'),
	'Admin_Client_Evaluation'=>array('评测管理',$entry . '/Admin/Client_Evaluation/index'),
	'Admin_Client_Strategy'=>array('攻略管理',$entry . '/Admin/Client_Strategy/index'),
	'Admin_Ad_Tuijian'=>array('热点评测',$entry . '/Admin/Ad_Tuijian/index'),
	'Admin_Client_Push'=>array('Push推送',$entry . '/Admin/Client_Push/index'),
	'Admin_Resource_Pgroup'=>array('机组管理',$entry . '/Admin/Resource_Pgroup/index'),
	'Admin_Client_Ad_Besttj'=>array('精品推荐',$entry . '/Admin/Client_Ad_Besttj/index'),
	'Admin_Client_Ad_Start'=>array('启动广告',$entry . '/Admin/Client_Ad_Start/index'),
	'Admin_Client_Installe'=>array('装机必备',$entry . '/Admin/Client_Installe/index'),
	'Admin_Client_Column' => array('栏目管理', $entry.'/Admin/Client_Column/index'),
	'Admin_Client_Ad_Channel' => array('频道管理', $entry.'/Admin/Client_Ad_Channel/index'),
	'Admin_Client_Single' => array('单机频道', $entry.'/Admin/Client_Single/index'),
	'Admin_Client_Web' => array('网游频道', $entry.'/Admin/Client_Web/index'),
	'Admin_Resource_Label' => array('标签管理', $entry.'/Admin/Resource_Label/index'),
	'Admin_Client_Gift' => array('礼包管理', $entry.'/Admin/Client_Gift/index'),
	'Admin_Client_Ad_Activity' => array('文字公告', $entry.'/Admin/Client_Ad_Activity/index'),
	'Admin_Client_Columnnew' => array('栏目管理(新)', $entry.'/Admin/Client_Columnnew/index'),
	'Admin_Client_Activity' => array('抽奖活动', $entry.'/Admin/Client_Activity/log'),
	'Admin_Festival_Autumn'=>array('中秋活动', $entry.'/Admin/Festival_Autumn/index'),
	'Admin_Festival_Guoqing'=>array('国庆活动', $entry.'/Admin/Festival_Guoqing/index'),
	'Admin_Account_User' => array('账号信息', $entry.'/Admin/Account_User/index'),
	//以下暂时保留	
	'Admin_Client_Hd' => array('活动管理', $entry.'/Admin/Client_Hd/index'),
	'Admin_Web_Config' => array('艾米WEB版', $entry.'/Admin/Web_Config/add'),
	'Admin_Client_Taste' => array('新游尝鲜', $entry.'/Admin/Client_Taste/index'),
	'Admin_Resource_Score' => array('评分管理', $entry.'/Admin/Resource_Score/index'),
	'Admin_Client_Comment' => array('评论管理', $entry.'/Admin/Client_Comment/index'),
	'Admin_Client_Set' => array('定值设置', $entry.'/Admin/Client_Set/index'),
);

return array($config, $view);
