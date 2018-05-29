<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//新闻配置
$config['news'] = array(
		'1'=>array(
				'name' => '头条',
				'url' => 'http://i.ifeng.com/commendrss?id=jinlihezuo&ch=zd_jl_llq_tt&vt=5'
		),
		'2'=>array(
				'name' => '新闻',
				'url' => 'http://i.ifeng.com/commendrss?id=dbyw_1&ch=zd_jl_llq_xw&vt=5'
		),
		'3'=>array(
				'name' => '体育',
				'url' => 'http://i.ifeng.com/commendrss?id=sports_yw&ch=zd_jl_llq_ty&vt=5'
		),
		'4'=>array(
				'name' => '军事',
				'url' => 'http://i.ifeng.com/commendrss?id=mil_head&ch=zd_jl_llq_js&vt=5'
		),
		'5'=>array(
				'name' => '娱乐',
				'url' => 'http://i.ifeng.com/commendrss?id=yl_lb01&ch=zd_jl_llq_yl&vt=5'
		)
);
//图片接口配置
$config['picture'] = array(
		'1'=>array(
				'name' => '明星',
				'url' => ''
		),
		'2'=>array(
				'name' => '美女',
				'url' => ''
		),
		'3'=>array(
				'name' => '军事',
				'url' => ''
		),
		'4'=>array(
				'name' => '光影故事',
				'url' => ''
		),
		'5'=>array(
				'name' => '汽车',
				'url' => ''
		)
);

//系统版本配置
$config['sys_version'] = array(
		1 => '1.6',
		2 => '2.0',
		3 => '2.1',
		4 => '2.2',
		5 => '2.3',
		6 => '4.0'
);
//分辨率配置
$config['resolution'] = array(
		1 => '240*320',
		2 => '320*480',
		3 => '480*800',
		4 => '540*960',
		5 => '720*1280'
);
//游戏价格配置
$config['price'] = array(
		1 => '道具收费',
		2 => '关卡收费',
		3 => '完全免费',
		4 => '内嵌广告'
);
//运营商配置
$config['operators'] = array(
		1 => '移动',
		2 => '联通',
		3 => '电信'
);
// SDK适应版本记录表对应字段
$config['sdkver'] =  array (
		3 => '1.5',
		4 => "1.6",
		5 => "2.0",
		6 => '2.0.1',
		7 => "2.1.x",
		8 => '2.2.x',
		9 => '2.3, 2.3.1, 2.3.2',
		10 => '2.3.3, 2.3.4',
		11 => "3.0.x",
		12 => "3.1.x",
		13 => "3.2",
		14 => "4.0, 4.0.1, 4.0.2",
		15 => "4.0.3, 4.0.4",
		16 => "4.1, 4.1.1, 4.1.2",
		17 => "4.2.x",
		18 => "4.3",
		19 => "4.4",
);

//开发者平台接口URL配置(线上)
$config['product_Url'] = array(
		1 => 'http://dev.game.gionee.com/api/get',
		2 => 'http://dev.game.gionee.com/api/getApk',
);
//开发者平台接口URL配置(测试)
$config['devlope_Url'] = array(
		1 => 'http://dev.game.3gtest.gionee.com/api/get',
		2 => 'http://dev.game.3gtest.gionee.com/api/getApk',
);

//标签定义
/**
 * 线上数据
 *network_type    [联网类型]
 *character       [游戏特色]
 *billing_model   [资费方式]
 *detail_category [详细分类]
 *level           [游戏评级]
 *about           [内容题材]
 *style           [画面风格]
 */
$config['label']=array(
		'test'=>array(
				'network_type' => '115',
				'character' => '111',  
				'billing_model' => '127' ,
				'detail_category' => '112' ,
				'level' => '120' ,        
				'about' => '113' ,        
				'style'=> '114' ,
		 ),
		'product'=>array(
				'network_type' => '103',
				'character' => '104',
				'billing_model' => '105' ,
				'detail_category' =>'106' ,
				'level' => '107' ,
				'about' => '108' ,
				'style'=> '109' ,
		)
);

//排行榜key定义
$config['rankKeys']= array(
			'weekRank'=>'周榜',
			'monthRank'=>'月榜',
		    'newRank'=>'新游榜',
			'upRank'=>'上升最快',
			'onlineRank'=>'网游榜',
			'pcRank'=>'单机榜'
		);

$config['clientRank'] = array(
		'weekRank'=> array(
					'title'=>'周榜',
					'viewType'=>'RankView',
					'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientWeekIndex/?',
					'source'=>'rankweek'
				),
		'monthRank'=> array(
					'title'=>'月榜',
					'viewType'=>'RankView',
					'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientMonthIndex/?',
					'source'=>'rankmonth'
				),
		'newRank'=> array(
				'title'=>'新游榜',
				'viewType'=>'RankView',
				'url'=>'http://game.gionee.com/Api/Local_Clientrank/newRankIndex/?',
				'source'=>'ranknew'
		),
		'upRank'=> array(
				'title'=>'上升最快',
				'viewType'=>'RankView',
				'url'=>'http://game.gionee.com/Api/Local_Clientrank/upRankIndex/?',
				'source'=>'rankup'
		),
		'onlineRank'=> array(
				'title'=>'网游榜',
				'viewType'=>'RankView',
				'url'=>'http://game.gionee.com/Api/Local_Clientrank/onlineRankIndex/?',
				'source'=>'rankonline'
		),
		'pcRank'=> array(
				'title'=>'单机榜',
				'viewType'=>'RankView',
				'url'=>'http://game.gionee.com/Api/Local_Clientrank/pcRankIndex/?',
				'source'=>'rankpc'
		),
);
$config['layout']= array(
		'version'=>'2014-05-16 00:00:00',
		'items'=>array(
				0 => array(
						'title'=>'精选',
						'viewType'=>'ChosenGameView',
						'source'=>'home',
						),
				1 => array(
						'title'=>'分类',
						'source'=>'category',
						'items'=> array(
								0 =>array(
										'title'=>'分类',
										'viewType'=>'CategoryListView',
										'source'=>'categorylist',
								),
								1 =>array(
										'title'=>'专题',
										'viewType'=>'TopicListView',
										'source'=>'subjectlist',
								),
						),
				),
				2 => array(
						'title'=>'排行',
						'source'=>'rank',
						'items'=> array(
								0 =>array(
										'title'=>'周榜',
										'viewType'=>'RankView',
										'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientWeekIndex/?',
										'source'=>'rankweek',
										
								),
								1 =>array(
										'title'=>'月榜',
										'viewType'=>'RankView',
										'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientMonthIndex/?',
										'source'=>'rankmonth',
								),
						),
				),
				3 => array(
						'title'=>'网游',
						'source'=>'olg',
						'items'=> array(
								0 =>array(
										'title'=>'热门',
										'viewType'=>'HotGameView',
										'source'=>'olghot',
								),
								1 =>array(
										'title'=>'礼包',
										'viewType'=>'GiftListView',
										'source'=>'giftlist',
								),
						),
				),
				4 => array(
						'title'=>'活动',
						'viewType'=>'ActivityListView',
						'source'=>'eventlist',
				),
				
		)
);
$config['layoutnew']= array(
		'column' => array('home'=>array(
				                'title'=>'首页',
								'source'=>'home',
								 ),
							'category'=>array(
									'title'=>'分类',
									'source'=>'category',
							),
							'rank' =>array(
									'title'=>'排行',
									'source'=>'rank',
							),
							'olg'      =>array(
									'title'=>'网游',
									'source'=>'olg'
							),

							'eventlist' => array(
									'title'=>'活动',
									'source'=>'eventlist',
							),
				
				     ),
		'channel' => array(
						'chosen'=> array(
								'title'=>'精选',
								'viewType'=>'ChosenGameView',
								'source'=>'home',
						),
						'categorylist'=>array(
								'title'=>'分类列表',
								'viewType'=>'CategoryListView',
								'source'=>'categorylist',
						),
						'subjectlist' =>array(
								'title'=>'专题',
								'viewType'=>'TopicListView',
								'source'=>'subjectlist',
						),
						'rankweek' =>array(
								'title'=>'周榜',
								'viewType'=>'RankView',
								'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientWeekIndex/?',
								'source'=>'rankweek',
						
						),
						'rankmonth' =>array(
								'title'=>'月榜',
								'viewType'=>'RankView',
								'url'=>'http://game.gionee.com/Api/Local_Clientrank/clientMonthIndex/?',
								'source'=>'rankmonth',
						),
						'olghot' =>array(
								'title'=>'热门',
								'viewType'=>'HotGameView',
								'source'=>'olghot',
						),
						'giftlist'=>array(
								'title'=>'礼包',
								'viewType'=>'GiftListView',
								'source'=>'giftlist',
						),
						'eventlist_sub' => array(
								'title'=>'活动',
								'viewType'=>'ActivityListView',
								'source'=>'eventlist',
						),
					    'newon'=> array(
					    		'title'=>'新游尝鲜',
					    		'viewType'=>'LatestView',
					    		'source'=>'newon',
					    		),
				        'classic'=> array(
					    		'title'=>'经典必备',
					    		'viewType'=>'ClassicView',
					    		'source'=>'classic',
					    		),
						'glike'=> array(
								'title'=>'猜你喜欢',
								'viewType'=>'GuessView',
								'source'=>'glike',
						),
				),
	
         //列表 下面key要唯一
		'ListView' => array(
					'pcgame'=> array(
							'title'=>'单机游戏',
							'viewType'=>'SingleGameView',
							'source'=>'pcg',
							'url'=>'http://game.gionee.com/Api/Local_Single/singleList/?page=',
					),
		),
		
		'WebView' => array(
					
				),
		'RankView' => array(
				'newRank'=> array(
						'title'=>'新游榜',
						'viewType'=>'RankView',
						'url'=>'http://game.gionee.com/Api/Local_Clientrank/newRankIndex/?',
						'source'=>'ranknew'
				),
				'upRank'=> array(
						'title'=>'上升最快',
						'viewType'=>'RankView',
						'url'=>'http://game.gionee.com/Api/Local_Clientrank/upRankIndex/?',
						'source'=>'rankup'
				),
				'onlineRank'=> array(
						'title'=>'网游榜',
						'viewType'=>'RankView',
						'url'=>'http://game.gionee.com/Api/Local_Clientrank/onlineRankIndex/?',
						'source'=>'rankonline'
				),
				'pcRank'=> array(
						'title'=>'单机榜',
						'viewType'=>'RankView',
						'url'=>'http://game.gionee.com/Api/Local_Clientrank/pcRankIndex/?',
						'source'=>'rankpc'
				),
				
		),
	);
//添加扩展的要展示的类型
$config['ext_type']= array(
		1 =>array('name'=>'列表',
				  'value'=> 'ListView'
				),
		2 =>array('name'=>'网页',
				'value'=> 'WebView'
		),
		3 =>array('name'=>'排行',
				'value'=> 'RankView'
		),		
);
return $config;