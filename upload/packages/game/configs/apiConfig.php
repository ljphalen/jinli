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
							'bbslist' => array(
									'title'=>'论坛',
									'source'=>'bbslist',
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
						'bbslist_sub' => array(
								'title'=>'论坛',
								'viewType'=>'ForumView',
								'source'=>'bbslist',
						),
						'pcgame'=> array(
								'title'=>'单机游戏',
								'viewType'=>'SingleGameView',
								'source'=>'pcg',
								'url'=>'http://game.gionee.com/Api/Local_Single/singleList/?page=',
						),
				),
	
         //列表 下面key要唯一
		'ListView' => array(
				'crackgame'=> array(
						'title'=>'破解列表',
						'viewType'=>'ListView',
						'source'=>'crackgame',
						'url'=>'http://game.gionee.com/Api/Local_Crackgame/CrackGameList/?page=',
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
//中国地区
$config['regions']= array(
		01 => '北京',
		02 => '天津',
		03 => '河北',
		04 => '山西',
		05 => '内蒙古',
		06 => '辽宁',
		07 => '吉林',
		08 => '黑龙江',
		09 => '上海',
		10 => '江苏',
		11 => '浙江',
		12 => '安徽',
		13 => '福建',
		14 => '江西',
		15 => '山东',
		16 => '河南',
		17 => '湖北',
		18 => '湖南',
		19 => '广东',
		20 => '广西',
		21 => '海南',
		22 => '四川',
		23 => '贵州',
		24 => '云南',
		25 => '西藏',
		26 => '陕西',
		27 => '甘肃',
		28 => '青海',
		29 => '宁夏',
		30 => '新疆',
		31 => '重庆',
);

//联通省份内部对应编码
$config['cuprov'] = array(
		'11'=>'01',
		'13'=>'02',
		'18'=>'03',
		'19'=>'04',
		'10'=>'05',
		'91'=>'06',
		'90'=>'07',
		'97'=>'08',
		'31'=>'09',
		'34'=>'10',
		'36'=>'11',
		'30'=>'12',
		'38'=>'13',
		'75'=>'14',
		'17'=>'15',
		'76'=>'16',
		'71'=>'17',
		'74'=>'18',
		'51'=>'19',
		'59'=>'20',
		'50'=>'21',
		'81'=>'22',
		'85'=>'23',
		'86'=>'24',
		'79'=>'25',
		'84'=>'26',
		'87'=>'27',
		'70'=>'28',
		'88'=>'29',
		'89'=>'30',
		'83'=>'31'
);

//电信省份内部对应编码
$config['ctcprov'] = array(
		'010'=>'01',
		'022'=>'02',
		'31'=>'03',
		'33'=>'03',
		'34'=>'04',
		'35'=>'04',
		'47'=>'05',
		'48'=>'05',
		'024'=>'06',
		'41'=>'06',
		'42'=>'06',
		'43'=>'07',
		'44'=>'07',
		'45'=>'08',
		'46'=>'08',
		'021'=>'09',
		'025'=>'10',
		'51'=>'10',
		'52'=>'10',
		'57'=>'11',
		'58'=>'11',
		'55'=>'12',
		'56'=>'12',
		'59'=>'13',
		'79'=>'14',
		'70'=>'14',
		'53'=>'15',
		'63'=>'15',
		'37'=>'16',
		'39'=>'16',
		'027'=>'17',
		'71'=>'17',
		'72'=>'17',
		'73'=>'18',
		'74'=>'18',
		'020'=>'19',
		'75'=>'19',
		'76'=>'19',
		'66'=>'19',
		'77'=>'20',
		'890'=>'21',
		'898'=>'21',
		'899'=>'21',
		'028'=>'22',
		'81'=>'22',
		'82'=>'22',
		'83'=>'22',
		'84'=>'22',
		'85'=>'23',
		'87'=>'24',
		'88'=>'24',
		'69'=>'24',
		'891'=>'25',
		'892'=>'25',
		'893'=>'25',
		'029'=>'26',
		'91'=>'26',
		'93'=>'27',
		'94'=>'27',
		'97'=>'28',
		'95'=>'29',
		'90'=>'30',
		'99'=>'30',
		'023'=>'31'
);

//电信区号代码
$config['regcode']= array(
		'北京' => array(
				    0 =>'010'
		    ),	
		'天津' => array(
				0 =>'022'
		),
		'河北' => array(
				0 =>'310',
				1 =>'311',
				2 =>'312',
				3 =>'313',
				4 =>'314',
				5 =>'315',
				6 =>'316',
				7 =>'317',
				8 =>'318',
				9 =>'319',
				10 =>'335',
		),
		'山西' => array(
				0 =>'349',
				1 =>'350',
				2 =>'351',
				3 =>'352',
				4 =>'353',
				5 =>'354',
				6 =>'355',
				7 =>'356',
				8 =>'357',
				9 =>'358',
				10 =>'359',
		),
		'内蒙古' => array(
				0 =>'470',
				1 =>'471',
				2 =>'472',
				3 =>'473',
				4 =>'474',
				5 =>'475',
				6 =>'476',
				7 =>'477',
				8 =>'478',
				9 =>'479',
				10 =>'482',
				11 =>'483',
		),
		'辽宁' => array(
				0 =>'024',
				1 =>'410',
				2 =>'411',
				3 =>'412',
				4 =>'413',
				5 =>'414',
				6 =>'415',
				7 =>'416',
				8 =>'417',
				9 =>'418',
				10 =>'419',
				11 =>'421',
				11 =>'427',
				11 =>'429',
		),
		'吉林' => array(
				0 =>'431',
				1 =>'432',
				2 =>'433',
				3 =>'434',
				4 =>'435',
				5 =>'436',
				6 =>'437',
				7 =>'438',
				8 =>'439',
				9 =>'440',
				10 =>'448',
		),
		'黑龙江' => array(
				0 =>'450',
				1 =>'451',
				2 =>'452',
				3 =>'453',
				4 =>'454',
				5 =>'455',
				6 =>'456',
				7 =>'457',
				8 =>'458',
				9 =>'459',
				10 =>'464',
				11 =>'468',
				12 =>'469',
		),
		'上海' => array(
				0 =>'021',
				
		),
		'江苏' => array(
				0 =>'025',
				1 =>'510',
				2 =>'511',
				3 =>'512',
				4 =>'513',
				5 =>'514',
				6 =>'515',
				7 =>'516',
				8 =>'517',
				9 =>'518',
				10 =>'519',
				11 =>'520',
				12 =>'523',
				13 =>'527',
		),
		'浙江' => array(
				0 =>'570',
				1 =>'571',
				2 =>'572',
				3 =>'573',
				4 =>'574',
				5 =>'575',
				6 =>'576',
				7 =>'577',
				8 =>'578',
				9 =>'579',
				10 =>'580',
		),
		'安徽' => array(
				0 =>'550',
				1 =>'551',
				2 =>'552',
				3 =>'553',
				4 =>'554',
				5 =>'555',
				6 =>'556',
				7 =>'557',
				8 =>'558',
				9 =>'559',
				10 =>'561',
				11 =>'562',
				12 =>'563',
				13 =>'564',
				14 =>'565',
				15 =>'566',
		),
		'福建' => array(
				0 =>'591',
				1 =>'592',
				2 =>'593',
				3 =>'594',
				4 =>'595',
				5 =>'596',
				6 =>'597',
				7 =>'598',
				8 =>'599',
		),
		'江西' => array(
				0 =>'790',
				1 =>'791',
				2 =>'792',
				3 =>'793',
				4 =>'794',
				5 =>'795',
				6 =>'796',
				7 =>'797',
				8 =>'798',
				9 =>'799',
				10 =>'701',
		),
		'山东' => array(
				0 =>'530',
				1 =>'531',
				2 =>'532',
				3 =>'533',
				4 =>'534',
				5 =>'535',
				6 =>'536',
				7 =>'537',
				8 =>'538',
				9 =>'539',
				11 =>'631',
				12 =>'632',
				13 =>'633',
				14 =>'634',
				15 =>'635',
		),
		'河南' => array(
				0 =>'370',
				1 =>'371',
				2 =>'372',
				3 =>'373',
				4 =>'374',
				5 =>'375',
				6 =>'376',
				7 =>'377',
				8 =>'378',
				9 =>'379',
				11 =>'391',
				12 =>'392',
				13 =>'393',
				14 =>'394',
				15 =>'395',
				16 =>'396',
				17 =>'397',
				18 =>'398',
		),
		'湖北' => array(
				0 =>'027',
				1 =>'710',
				2 =>'711',
				3 =>'712',
				4 =>'713',
				5 =>'714',
				6 =>'715',
				7 =>'716',
				8 =>'717',
				9 =>'718',
				11 =>'719',
				12 =>'722',
				13 =>'724',
				14 =>'728',
		),
		'湖南' => array(
				0 =>'730',
				1 =>'731',
				2 =>'732',
				3 =>'733',
				4 =>'734',
				5 =>'735',
				6 =>'736',
				7 =>'737',
				8 =>'738',
				9 =>'739',
				11 =>'743',
				12 =>'744',
				13 =>'745',
				14 =>'746',
		),
		'广东' => array(
				0 =>'020',
				1 =>'751',
				2 =>'752',
				3 =>'753',
				4 =>'754',
				5 =>'755',
				6 =>'756',
				7 =>'757',
				8 =>'758',
				9 =>'759',
				11 =>'760',
				12 =>'762',
				13 =>'763',
				14 =>'765',
				15 =>'766',
				16 =>'768',
				17 =>'769',
				18 =>'660',
				19 =>'661',
				20 =>'662',
				21 =>'663',
		),
		'广西' => array(
				0 =>'770',
				1 =>'771',
				2 =>'772',
				3 =>'773',
				4 =>'774',
				5 =>'775',
				6 =>'776',
				7 =>'777',
				8 =>'778',
				9 =>'779',
		),
		'海南' => array(
				0 =>'890',
				8 =>'898',
				9 =>'899',
		),
		'四川' => array(
				0 =>'028',
				1 =>'810',
				2 =>'811',
				3 =>'812',
				4 =>'813',
				5 =>'814',
				6 =>'816',
				7 =>'817',
				8 =>'818',
				9 =>'819',
				11 =>'825',
				12 =>'826',
				13 =>'827',
				14 =>'830',
				15 =>'831',
				16 =>'832',
				17 =>'833',
				18 =>'834',
				19 =>'835',
				20 =>'836',
				21 =>'837',
				22 =>'838',
				23 =>'839',
				24 =>'840',
		),
		'贵州' => array(
				0 =>'851',
				1 =>'852',
				2 =>'853',
				3 =>'854',
				4 =>'855',
				5 =>'856',
				6 =>'857',
				7 =>'858',
				8 =>'859',
		),
		'云南' => array(
				0 =>'870',
				1 =>'871',
				2 =>'872',
				3 =>'873',
				4 =>'874',
				5 =>'875',
				6 =>'876',
				7 =>'877',
				8 =>'878',
				9 =>'879',
				11 =>'691',
				12 =>'692',
				13 =>'881',
				14 =>'883',
				15 =>'886',
				16 =>'887',
				17 =>'888',
		),
		'西藏' => array(
				0 =>'891',
				1 =>'892',
				2 =>'893',
		),
		'陕西' => array(
				0 =>'029',
				1 =>'910',
				2 =>'911',
				3 =>'912',
				4 =>'913',
				5 =>'914',
				6 =>'915',
				7 =>'916',
				8 =>'917',
				9 =>'918',
				10 =>'919',
		),
		'甘肃' => array(
				0 =>'930',
				1 =>'931',
				2 =>'932',
				3 =>'933',
				4 =>'934',
				5 =>'935',
				6 =>'936',
				7 =>'937',
				8 =>'938',
				9 =>'941',
				10 =>'943',
		),
		'青海' => array(
				0 =>'971',
				1 =>'972',
				2 =>'973',
				3 =>'974',
				4 =>'975',
				5 =>'976',
				6 =>'977',

		),
		'宁夏' => array(
				0 =>'951',
				1 =>'952',
				2 =>'953',
				3 =>'954',
		
		),
		'新疆' => array(
				0 =>'990',
				1 =>'991',
				2 =>'992',
				3 =>'993',
				4 =>'994',
				5 =>'995',
				6 =>'996',
				7 =>'997',
				8 =>'998',
				9 =>'999',
				10 =>'901',
				11 =>'902',
				12 =>'903',
				13 =>'906',
				14 =>'908',
		),
		'重庆' => array(
				0 =>'023',
		),
);

return $config;