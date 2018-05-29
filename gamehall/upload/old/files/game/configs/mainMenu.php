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
		)			
	)
);

$config['Admin_Content'] = array(
		'name'=>'广告运营',
		'items'=>array(
				array(
					'name'=>'H5',
					'items'=>array(
						'Admin_Ad_Recommendlist',
					        'Admin_Ad_Indexad',
					        'Admin_Client_Ad_Turn'
						)
					),
				array(
					'name'=>'客户端',
					'items'=>array(
							'Admin_Client_Ad_Recommendlist',
							'Admin_Client_Ad_Recommendold',
					        'Admin_Client_Ad_Magneticon',
							'Admin_Client_Ad_Besttj',
					        'Admin_Client_Ad_Start',
					        'Admin_Client_Ad_Channel',
							'Admin_Client_Column',
							'Admin_Client_Columnnew',
							'Admin_Client_Ad_Importantgame',
					        'Admin_Freedl_Hd',
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
		                'name'=>'内容运营',
		                'items'=>array(
		                        'Admin_Client_Subject',
		                        'Admin_Resource_Keyword',
		                        'Admin_Client_Gift',
		                        'Admin_Client_Rank',
		                        'Admin_Client_Installe',
		                        'Admin_Client_Taste',
		                        'Admin_Game_Webgame',
		                        'Admin_Game_Singlegame',
		                        'Admin_Bbs_Manage',
		                        'Admin_Client_Config',
		                    
		                )
		        ),
		        array(
		                'name'=>'用户管理',
		                'items'=>array(
		                        'Admin_Account_User',
		                        'Admin_Account_Bgimg',
		                        'Admin_Client_Feedback',
		                        'Admin_Client_Comment',
		                        'Admin_Resource_Score',
		                        'Admin_Msg_index',
		                        'Admin_Client_Push',
		                )
		        ),
		        array(
		                'name'=>'活动管理',
		                'items'=>array(
                                'Admin_Client_Hd',
                                'Admin_Client_Giftactivity',
		                        'Admin_Client_Wealtaskconfig',
		                        'Admin_Mall_Goods',
		                		'Admin_Client_Acoupon',
		                		'Admin_Festival_Index',
		                		'Admin_Festival_Summer',
                                'Admin_Festival_Touchgame'
		                        //'Admin_Point_Prize',
		                        // 'Admin_Client_Activity',
		                )
		        ),
		        //array(
		                //'name'=>'节日活动',
		               // 'items'=>array(
		                        //'Admin_Festival_Props',
		                        //'Admin_Festival_Autumn',
		                        //'Admin_Festival_Guoqing'
		                //)
		        //),
		        array(
		                'name'=>'资讯管理',
		                'items'=>array(
		                        'Admin_Client_News',
				                'Admin_Client_Evaluation',
				                'Admin_Client_Strategy',
		                )
		        ),
				array(
						'name'=>'配置管理',
						'items'=>array(
								'Admin_Config',
								'Admin_Web_Config',
								'Admin_Client_Set',
						)
				)
		)
);

$config['Admin_Store'] = array(
		'name'=>'内容库',
		'items' => array(
				array(
						'name'=>'属性管理',
						'items' => array(
								'Admin_Resource_Category',
								'Admin_Resource_Attribute',
								'Admin_Resource_Type',
								'Admin_Resource_Pgroup',
								'Admin_Resource_Label',
								'Admin_Resource_Property',						        
						)
				),
				array(
						'name'=>'游戏仓库',
						'items' => array(
								'Admin_Resource_Games_index',
								'Admin_Resource_Games_entering',
								'Admin_Freedl_Cugd',
								'Admin_Resource_Sync_index',
						)
				)
		)
);
$config['Admin_Sdk'] = array(
		'name'=>'Sdk运营',
		'items'=>array(
				'Admin_Sdk_Ad_Activity'
					
		)
);
$config['Admin_Stat'] = array(
		'name' => '统计',
		'items' => array(
				'Admin_Stat_monkeynum',
		        	'Admin_Resource_Asyntask'
		)
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
		//系统-用户
		'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
        'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
        'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),
        
		//广告运营-H5
        'Admin_Ad_Recommendlist'=>array('首页配置',$entry . '/Admin/Ad_Recommendlist/index'),
        'Admin_Ad_Indexad'=>array('悬浮广告配置',$entry . '/Admin/Ad_Indexad/index'),
        'Admin_Client_Ad_Turn'=>array('单机网游轮播图',$entry . '/Admin/Client_Ad_Turn/index'),
		//广告运营-客户端
        'Admin_Client_Ad_Recommendlist'=>array('首页推荐',$entry . '/Admin/Client_Ad_Recommendlist/index'),
        'Admin_Client_Ad_Recommendold'=>array('旧版本推荐',$entry . '/Admin/Client_Ad_Recommendold/index'),
        'Admin_Client_Ad_Magneticon' => array('磁铁图标', $entry . '/Admin/Client_Ad_Magneticon/index'),
        'Admin_Client_Ad_Besttj'=>array('闪屏推荐',$entry . '/Admin/Client_Ad_Besttj/index'),
        'Admin_Client_Ad_Start'=>array('启动图片',$entry . '/Admin/Client_Ad_Start/index'),
        'Admin_Client_Ad_Channel' => array('频道管理', $entry.'/Admin/Client_Ad_Channel/index'),
        'Admin_Client_Column' => array('栏目管理', $entry.'/Admin/Client_Column/index'),
        'Admin_Client_Columnnew' => array('栏目管理(新)', $entry.'/Admin/Client_Columnnew/index'),
        'Admin_Client_Ad_Importantgame'=>array('重点游戏',$entry . '/Admin/Client_Ad_Importantgame/index'),
        'Admin_Freedl_Hd'=>array('免流量管理',$entry . '/Admin/Freedl_Hd/index'),
        
		//广告运营-web版
		'Admin_Web_Turn'=>array('轮播广告',$entry . '/Admin/Web_Turn/index'),
		'Admin_Web_Playgame'=>array('大家玩列表',$entry . '/Admin/Web_Playgame/index'),
        
        //内容运营-内容运营
        'Admin_Client_Subject'=>array('专题管理',$entry . '/Admin/Client_Subject/index'),
        'Admin_Resource_Keyword'=>array('搜索管理', $entry. '/Admin/Resource_Keyword/index'),
        'Admin_Client_Gift' => array('礼包管理', $entry.'/Admin/Client_Gift/gamelist'),
        'Admin_Client_Rank'=>array('排行管理',$entry . '/Admin/Client_Rank/new'),
        'Admin_Client_Installe'=>array('装机必备',$entry . '/Admin/Client_Installe/index'),
        'Admin_Client_Taste' => array('新游尝鲜', $entry.'/Admin/Client_Taste/index'),
        'Admin_Client_Web' => array('网游频道', $entry.'/Admin/Client_Web/index'),
        'Admin_Client_Single' => array('单机频道', $entry.'/Admin/Client_Single/index'),
        'Admin_Bbs_Manage'=>array('论坛管理',$entry . '/Admin/Bbs_Manage/index'),
        'Admin_Client_Config'=>array('猜你喜欢',$entry . '/Admin/Client_Config/index'),
        'Admin_Game_Webgame' => array('网游频道', $entry.'/Admin/Game_Webgame/index'),
        'Admin_Game_Singlegame' => array('单机频道', $entry.'/Admin/Game_Singlegame/index'),
    
        
        //内容运营-用户管理
        'Admin_Msg_index' => array('消息管理', $entry . '/Admin/Msg/index'),
        'Admin_Account_User' => array('账号信息', $entry.'/Admin/Account_User/index'),
        'Admin_Account_Bgimg' => array('用户中心背景图', $entry.'/Admin/Account_Bgimg/index'),
        'Admin_Client_Feedback'=>array('意见反馈',$entry . '/Admin/Client_Feedback/index'),
        'Admin_Client_Comment' => array('评论管理', $entry.'/Admin/Client_Comment/index'),
        'Admin_Resource_Score' => array('评分管理', $entry.'/Admin/Resource_Score/index'),
        'Admin_Client_Push'=>array('Push推送',$entry . '/Admin/Client_Push/index'),
        
        //内容运营-活动管理
        'Admin_Client_Hd' => array('活动管理', $entry.'/Admin/Client_Hd/index'),
        'Admin_Client_Giftactivity' => array('送礼包活动', $entry . '/Admin/Client_Giftactivity/index'),
        'Admin_Client_Wealtaskconfig'=>array('任务管理',$entry . '/Admin/Client_Wealtaskconfig/index'),
        'Admin_Client_Acoupon'=> array('A券管理 ',$entry . '/Admin/Client_Acoupon/index'),
        'Admin_Mall_Goods'=> array('积分管理',$entry . '/Admin/Mall_Goods/index'),
        'Admin_Point_Prize'=> array('积分抽奖 ',$entry . '/Admin/Point_Prize/index'),
        'Admin_Client_Activity' => array('每日抽奖', $entry.'/Admin/Client_Activity/log'),
		'Admin_Festival_Index'=>array('节日活动', $entry.'/Admin/Festival_Props/index'),
		'Admin_Festival_Props'=>array('道具兑换', $entry.'/Admin/Festival_Props/index'),
		'Admin_Festival_Summer'=>array('暑假活动', $entry.'/Admin/Festival_Summer/index'),
        'Admin_Festival_Touchgame'=>array('划屏游戏', $entry.'/Admin/Festival_Touchgame/index'),

    //内容运营-节日活动
        //'Admin_Festival_Autumn'=>array('中秋活动', $entry.'/Admin/Festival_Autumn/index'),
        //'Admin_Festival_Guoqing'=>array('国庆活动', $entry.'/Admin/Festival_Guoqing/index'),
        
		//内容运营-资讯管理
		'Admin_Client_News'=>array('新闻管理',$entry . '/Admin/Client_News/index'),
        'Admin_Client_Evaluation'=>array('评测管理',$entry . '/Admin/Client_Evaluation/index'),
        'Admin_Client_Strategy'=>array('攻略管理',$entry . '/Admin/Client_Strategy/index'),
        
    	//内容运营-配置管理
		'Admin_Config'=>array('服务端配置项',$entry . '/Admin/Config/index'),
		'Admin_Web_Config' => array('艾米WEB版', $entry.'/Admin/Web_Config/add'),
		'Admin_Client_Set' => array('客户端配置项', $entry.'/Admin/Client_Set/index'),
		
		//内容库-属性管理
		'Admin_Resource_Category'=>array('分类管理',$entry . '/Admin/Resource_Category/index'),
		'Admin_Resource_Attribute'=>array('属性管理',$entry . '/Admin/Resource_Attribute/index'),
		'Admin_Resource_Type'=>array('机型管理',$entry . '/Admin/Resource_Type/index'),
		'Admin_Resource_Pgroup'=>array('机组管理',$entry . '/Admin/Resource_Pgroup/index'),
		'Admin_Resource_Label' => array('标签管理', $entry.'/Admin/Resource_Label/index'),
		'Admin_Resource_Property'=>array('添加属性',$entry . '/Admin/Resource_Attribute/add'),
		'Admin_Resource_Upgrade'=>array('差分峰值',$entry . '/Admin/Resource_Upgrade/index'),
        
		//内容库-游戏仓库
		'Admin_Resource_Games_index'=>array('游戏内容库',$entry . '/Admin/Resource_Games/index?type=1'),
		'Admin_Resource_Games_entering'=>array('运营管理',$entry . '/Admin/Resource_Games/entering?type=2'),
		'Admin_Freedl_Cugd'=>array('联通免流量',$entry . '/Admin/Freedl_Cugd/index'),
		'Admin_Resource_Sync_index' => array('同步日志',$entry . '/Admin/Resource_Sync/index'),
		'Admin_Sdk_Ad_Activity' => array('公告活动管理', $entry.'/Admin/Sdk_Ad_Activity/index'),
		'Admin_Sdk_Game_Feedback'=> array('反馈数据', $entry.'/Admin/Sdk_Game_Feedback/index'),
		//内容库-性能统计
		'Admin_Stat_pv'=>array('PV统计',$entry . '/Admin/Stat/pv'),
		'Admin_Stat_uv'=>array('UV统计',$entry . '/Admin/Stat/uv'),
		'Admin_Stat_monkeynum' => array('接口性能', $entry . '/Admin/Stat/monkeynum'),
		'Admin_Resource_Asyntask' => array('异步任务', $entry . '/Admin/Resource_Asyntask/taskList'),
);

$extends = array(
    'Admin_Client_Ad_Recommendlist' => array(
        'Admin_Client_Ad_Recommendnew',
        'Admin_Client_Ad_Recommendday',
        'Admin_Client_Ad_Recommendbanner',
        'Admin_Client_Ad_Recommendtext',
    ),
    'Admin_Client_Ad_Recommendold' => array(
        'Admin_Client_Ad_Recommend',
        'Admin_Client_Ad_Turn',
        'Admin_Client_Ad_Picture',
        'Admin_Client_Ad_Recpic',
        'Admin_Client_Ad_Subject',
    ),
    'Admin_Ad_Recommendlist' => array(
        'Admin_Ad_Recommendnew',
        'Admin_Ad_Recommendday',
        'Admin_Ad_Recommendbanner',
        'Admin_Ad_Recommendtext',
    ),
    'Admin_Game_Webgame' => array(
        'Admin_Game_Gameopen',
        'Admin_Client_Web',
        'Admin_Client_Navigation'
    ),
    'Admin_Game_Singlegame' => array(
        'Admin_Client_Single',
    ),
	'Admin_Mall_Goods' => array(
    	'Admin_Point_Prize',
    ),
	'Admin_Festival_Index' => array(
		'Admin_Festival_Props'
	)
);

$noVerify = array(
    'Admin_Common'
);

return array($config, $view, $extends, $noVerify);
