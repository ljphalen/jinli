<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_Browser'] = array(
    'name'  => '浏览器',
    'items' => array(
        array(
            'name'  => '基础配置',
            'items' => array(
                'Admin_Page',
                'Admin_Blackurl',
                'Admin_Welcome',
                'Admin_Splash',
                'Admin_Bookmark',
                'Admin_Browserurl',
                'Admin_Browserurl_2',
                'Admin_Browserurl_3',
                'Admin_Browserfavicon',
			    'Admin_Wanka',
                'Admin_Config_browserbase',
                'Admin_Config_search',
                'Admin_Browserreplacesearch',
				'Admin_Pushtools',
            )
        ),
        array(
            'name'  => '分机型控制',
            'items' => array(
                'Admin_Browser_conf',
                'Admin_Browser_brand',
                'Admin_Browser_series',
                'Admin_Browser_model',
            )
        ),
        array(
            'name'  => '推荐站点',
            'items' => array(
                'Admin_Recwebsite',
                'Admin_Recwebsite_group',
                //'Admin_Recwebsite_tourl',
            )
        ),
        array(
            'name'  => '中转链接管理',
            'items' => array(
                'Admin_Inbuilt',
                'Admin_Inbuilt_bookmark',
            )
        )
    )
);
$config['Admin_Server']  = array(
    'name'  => '服务端',
    'items' => array(
        array(
            'name'  => 'H5导航管理',
            'items' => array(
                'Admin_Ngtype',
                'Admin_Ngtype2',
                'Admin_Ng',
                'Admin_Ngimport',
                'Admin_Ng_picimport',
                'Admin_Vendor',
                'Admin_Attribute',
                'Admin_Config_Channel',
            )
        ),
        array(
            'name'  => '卡片导航管理',
            'items' => array(
                'Admin_Localnav_type',
                'Admin_Localnav_column',
                'Admin_Localnav',
                'Admin_Localnav_import',
				'Admin_Localnav_cardtitle',
                array(
                    'name'  => '二级栏目-新闻',
                    'items' => array(
                        'Admin_Navnews',
                        'Admin_Navnews_column',
                        'Admin_Navnews_source',
                        'Admin_Navad_listnews',
                    ),
                ),
                array(
                    'name'  => '二级栏目-美图',
                    'items' => array(
                        'Admin_Navpic',
                        'Admin_Navpic_column',
                        'Admin_Navpic_source',
                        'Admin_Navad_listpic',
                    ),
                ),
                array(
                    'name'  => '二级栏目-段子',
                    'items' => array(
                        'Admin_Navfun',
                        'Admin_Navfun_column',
                        'Admin_Navfun_source',
                        'Admin_Navad_listfun',
                    ),
                ),

            ),
        ),
        'Admin_Machine',
        array(
            'name'  => '老版应用',
            'items' => array(
                'Admin_Apptype',
                'Admin_App',
            )
        ),
        array(
            'name'  => '新版应用',
            'items' => array(
                'Admin_Webapptype',
                'Admin_Webthemetype',
                'Admin_Webapp',
                'Admin_Appconfig',
                'Admin_Webapp_picimport',
                'Admin_Webapp_picimport2',
            )
        ),
        array(
            'name'  => 'H5游戏',
            'items' => array(
                'Admin_H5game_list',
                'Admin_H5game_type',
                'Admin_H5game_cate',

            )
        ),
        array(
            'name'  => '专题管理',
            'items' => array(
                'Admin_Topic',
                'Admin_Feedback',
                'Admin_Createurl'
            )
        ),
        array(
            'name'  => 'Elife管理',
            'items' => array(
                'Admin_Elifeserver',
            )
        ),
        array(
            'name'  => '自定义短链接管理',
            'items' => array(
                'Admin_Shorturl',
            )
        ),
        array(
            'name'  => '导航新闻',
            'items' => array(
                'Admin_Jhtype',
                'Admin_Jhnews',
            )
        ),
        'Admin_Sohu',
        array(
            'name'  => '畅聊管理',
            'items' => array(
                'Admin_Voip',
                'Admin_Voip_phonevest',
            )
        ),
        array(
            'name'  => '微信管理',
            'items' => array(
                'Admin_Weixin',
                'Admin_Weixin_user',
                'Admin_Weixin_menu',
                'Admin_Weixin_command',
                'Admin_Weixin_feedback',
                'Admin_Weixin_msg',
                'Admin_Weixin_concern'
            )
        ),
        array(
            'name'  => '活动',
            'items' => array(
            		array(
            			'name'	=>'活动管理',
            			'items'	=>array(
            						'Admin_Event',
            						'Admin_Event_Goods',
            						'Admin_Event_Prize',
            						'Admin_Event_Log',
            					)
            			),
                array(
                    'name'  => '微信减钱活动',
                    'items' => array(
                        'Admin_Wxhelp',
                        'Admin_Wxhelp_user',
                        'Admin_Wxhelp_address',
                    )
                ),
                array(
                    'name'  => '端午活动',
                    'items' => array(
                        'Admin_Duanwu_config',
                        'Admin_Duanwu_listuser',
                        'Admin_Duanwu_listlog',
                    )
                ),
            		
           array(
                    	'name'=>'七夕连连看',
           				'items'=>array(
                    			'Admin_Link_config',
           						'Admin_Link_index',
           						'Admin_Link_log',
                    )
                    ),
            		
            		array(
            				'name'	=>'国庆活动',
            				'items'	=>array(
            						'Admin_National_Config',
            						'Admin_National_Index',
            						'Admin_National_Log',
            				)
            		),
            		
            		array(
            				'name'	=>'双十一预约',
            				'items'	=>array(
            						'Admin_Seckill_Config',
            						'Admin_Seckill_remind',
            				)
            		),


            ),
        	
        ),
        array(
            'name'  => '用户中心',
            'items' => array(
                array(
                    'name'  => '基本信息管理',
                    'items' => array(
                        'Admin_Register',
                        'Admin_Userinnermsg_listtpl',
                        'Admin_Uprivilege_base',
                        'Admin_Rule',
                    )
                ),
                array(
                    'name'  => '广告管理',
                    'items' => array(
                        'Admin_Position',
                        'Admin_Ads',
                    ),
                ),
                array(
                    'name'  => '物品管理',
                    'items' => array(
                        'Admin_Category',
                        'Admin_Commodities',
                        'Admin_Produces',
                        'Admin_Uprivilege',
                    ),
                ),
                array(
                    'name'  => '订单管理',
                    'items' => array(
                        'Admin_Order',
                        'Admin_Order_Done',
                        'Admin_Order_Cancel',
                        'Admin_Order_New',
                    )
                ),
                array(
                    'name'  => '日志管理',
                    'items' => array(
                        'Admin_Logs',
                        'Admin_Logs_InnerMsg',
                        'Admin_Logs_Api',
                        'Admin_Usummary',
                    )
                ),
                array(
                    'name'  => '专题活动',
                    'items' => array(
                        'Admin_Ulottery',
                        'Admin_Ulottery_index',
                        'Admin_Ulottery_winner',
                        'Admin_Ulottery_Coupon',
                        'Admin_Ulottery_Quiz',
                    )
                ),
                array(
                    'name'  => '充值管理',
                    'items' => array(
                        'Admin_Umoney',
                        'Admin_Umoney_Order',
                        'Admin_Umoney_Log',
                    	'Admin_Umoney_api',
                    )
                ),
                array(
                    'name'  => '账号管理',
                    'items' => array(
                        'Admin_Uaccount',
                        'Admin_Uaccount_Month',
                        'Admin_Uaccount_BlackList',
                    )
                ),
                array(
                    'name'  => '经验等级管理',
                    'items' => array(
                        'Admin_Experience_Config',
                        'Admin_Experience',
                        'Admin_Experience_Type',
                        'Admin_Experience_Log',
                    )
                ),
            ),

        ),
        array(
            'name'  => '运营内容管理',
            'items' => array(
                'Admin_Sites',
                'Admin_React',
                'Admin_Ad',
            )
        ),
        array(
            'name'  => '分机型管理',
            'items' => array(
                'Admin_Model_Attributes',
                'Admin_Model',
            )
        ),
        array(
            'name'  => '反馈管理',
            'items' => array(
                'Admin_Feedbackconf',
                'Admin_Feedbackmsg',
                'Admin_Feedbackfaq',
                'Admin_Feedbackkey',
            )
        ),
        array(
            'name'  => 'CP管理',
            'items' => array(
                'Admin_Parter',
                'Admin_Parter_Qualification',
                'Admin_Parter_Businuss',
                'Admin_Parter_Click',
                'Admin_Parter_Day',
                'Admin_Parter_Month'
            )
        ),
        array(
            'name'  => '旧新闻聚合',
            'items' => array(
                'Admin_Column',
                'Admin_Outnews'
            )
        ),
        array(
            'name'  => '内部合作',
            'items' => array(
                array(
                    'name'  => '天气',
                    'items' => array(
                        'Admin_Partner_Weathercolumn',
                        'Admin_Partner_Weathersource',
                        'Admin_Partner_Weather',
                        'Admin_Navad_listweather',
                    )
                ),
                //'Admin_Partner_Desktop',
                array(
                    'name'  => '音乐',
                    'items' => array(
                        'Admin_Partner_listsingernews',
                    )
                ),
                array(
                    'name'  => '日历',
                    'items' => array(
                        'Admin_Partner_listhistorytoday',
                        'Admin_Navad_listcalendar',
                    )
                )
            )
        ),
        array(
            'name'  => '标签管理',
            'items' => array(
                'Admin_Label',
                'Admin_Label_listimei',
            )
        )
    )
);

$config['Admin_DataSysem'] = array(
    'name'  => '数据系统',
    'items' => array(
        array(
            'name'  => 'PV/UV统计',
            'items' => array(
                array(
                    'name'  => 'PV统计',
                    'items' => array(
                        'Admin_Stat_pv2_all',
                        'Admin_Stat_pv2_3g',
                        'Admin_Stat_pv2_user',
                        'Admin_Stat_pv2_voip',
                        'Admin_Stat_accesstimes'
                    )
                ),
                array(
                    'name'  => 'UV统计',
                    'items' => array(
                        'Admin_Stat_uv2_all',
                        'Admin_Stat_uv2_3g',
                        'Admin_Stat_uv2_user',
                        'Admin_Stat_uv2_voip'
                    )
                ),
            )
        ),
        array(
            'name'  => '导航相关统计',
            'items' => array(
                'Admin_Stat_shorturl',
                'Admin_stat_pv3'
            )
        ),
        array(
            'name'  => '卡片导航统计',
            'items' => array(
                'Admin_Localstat',
                'Admin_Navad_stat',
                'Admin_Navnews_statfun',
                'Admin_Navnews_statpic',
                'Admin_Localstat_statall',
                /* 'Admin_Localstat_Channel',
                'Admin_Localstat_Detail', */
            )
        ),
        array(
            'name'  => '专题统计',
            'items' => array(
                'Admin_Stat_topiclist',
                'Admin_Stat',
            )
        ),
        array(
            'name'  => '用户中心统计',
            'items' => array(
                'Admin_Voipstat',
                'Admin_Userstat_entrance',
                'Admin_Userstat_task',
                'Admin_Userstat_exchange',
                'Admin_Userstat_signin',
                'Admin_Userstat_day',
                'Admin_Userstat_month',
                'Admin_Userstat_rank',
                'Admin_Userstat_lottery',
                'Admin_Userstat_scratch',
                'Admin_Userstat',
                'Admin_Userstat_snatch',
                'Admin_Userstat_Experience',
                'Admin_Userstat_quiz',
                'Admin_Userstat_browseronlinestat',
            ),
        ),
        array(
            'name'  => '第三方接口统计',
            'items' => array(
                'Admin_Partner_statweather',
                'Admin_Partner_statmusic',
                'Admin_Partner_statcalendar',
            ),
        ),
        array(
            'name'  => '其它统计',
            'items' => array(
                array(
                    'name'  => '微信帮忙活动',
                    'items' => array(
                        'Admin_Wxhelp_stat',
                        'Admin_Wxhelp_statuserincr',
                    ),
                ),
                'Admin_Stat_hotwords',
                'Admin_Stat_monkeynum',
                'Admin_Stat_inbuilt',
                'Admin_Stat_duanwu',
            	'Admin_Stat_qixi',
            	'Admin_Stat_national',
				'Admin_Stat_seckill',
                array(
                    'name'  => 'cp相关',
                    'items' => array(
                        'Admin_Stat_CpInit',
                        'Admin_Stat_CpDay',
                        'Admin_stat_CpMonth'
                    )
                )
            )
        ),
    ),
);
$config['Admin_System']    = array(
    'name'  => '系统',
    'items' => array(
        array(
            'name'  => '用户',
            'items' => array(
                'Admin_User',
                'Admin_Group',
                'Admin_Log',
                'Admin_User_passwd'
            ),
        ),
        'Admin_Config',
        'Admin_CleanVer',
        array(
            'name'  => '运行监控',
            'items' => array(
                'Admin_BaiduKeywords',
                'Admin_Config_errlog',
                'Admin_Config_cronlog',
                'Admin_Config_cronrun_1',
                'Admin_Config_cronrun_2',
                'Admin_Config_cronrun_4',
                'Admin_Config_cronrun_5',
                'Admin_Config_cronrun_3',
            ),
        ),
        array(
            'name'  => '第三方接口配置',
            'items' => array(
                'Admin_Config_changyan',
                'Admin_Config_geetest',
                'Admin_Voip_config',
            ),
        ),
        'Admin_CheckUrl',
        'Admin_Ollog',
        'Admin_Incomelog'
    )
);

$op    = array('add' => '添加', 'edit' => '编辑', 'del' => '删除');
$entry = Yaf_Registry::get('config')->adminroot;
$view  = array(
    'Admin_Common'                     => array('公共权限', ''),
    'Admin_NewLightApp'                => array('新版轻应用', ''),
    'Admin_LightApp'                   => array('旧版轻应用', ''),
    'Admin_HotWordCtl'                 => array('热词控制', ''),
    'Admin_SearchEngineCtl'            => array('搜索引擎控制', ''),
    'Admin_RecommendSite'              => array('推荐站点', ''),
    'Admin_Subtopic'                   => array('Subtopic', ''),
    'Admin_LNSiteUpload'               => array('站点上传', ''),
    'Admin_LNRssRead'                  => array('聚合阅读', ''),
    'Admin_LNXiaoShuo'                 => array('小说', ''),
    'Admin_LNMeitu'                    => array('美图', ''),
    'Admin_LNSearchEngine'             => array('搜索引擎', ''),
    'Admin_LNHotWord'                  => array('热词', ''),
    'Admin_LNLotteryApi'               => array('彩票接口', ''),
    'Admin_LNLotteryContent'           => array('彩票内容', ''),
    'Admin_BookMarkContent'            => array('书签内容', ''),
    'Admin_BookMarkSubtopic'           => array('Subtopic', ''),
    'Admin_StartUpContent'             => array('内容', ''),
    'Admin_StartUpRule'                => array('规则', ''),
    'Admin_PushContent'                => array('内容', ''),
    'Admin_PushRule'                   => array('规则', ''),
    'Admin_AutoLevelMachine'           => array('机型', ''),
    'Admin_AutoLevelVersion'           => array('版本', ''),
    'Admin_AutoLevelYunying'           => array('运营', ''),
    'Admin_BrowserRssReader'           => array('聚合阅读', ''),
    'Admin_User'                       => array('管理员列表', $entry . '/Admin/User/index'),
    'Admin_Group'                      => array('用户组管理', $entry . '/Admin/Group/index'),
    'Admin_Log'                        => array('操作日志', $entry . '/Admin/User/log'),
    'Admin_User_passwd'                => array('修改密码', $entry . '/Admin/User/passwd'),
    'Admin_Product'                    => array('产品列表', $entry . '/Admin/Product/index'),
    'Admin_Series'                     => array('系列管理', $entry . '/Admin/Series/index'),
    'Admin_Sites'                      => array('网址大全', $entry . '/Admin/Sites/index'),
    'Admin_Ad'                         => array('广告管理', $entry . '/Admin/Ad/index'),
    'Admin_Config'                     => array('系统设置', $entry . '/Admin/Config/index'),
    'Admin_Config_cronrun_1'           => array('旧版外部数据抓取', $entry . '/Admin/Config/cronrun?type=nav_news'),
    'Admin_Config_cronrun_2'           => array('旧版聚合新闻抓取', $entry . '/Admin/Config/cronrun?type=out_news'),
    'Admin_Config_cronrun_4'           => array(
        '卡片外部数据抓取',
        $entry . '/Admin/Config/cronrun?type=localnav_news_get_rss'
    ),
    'Admin_Config_cronrun_5'           => array(
        '卡片二级更新列表',
        $entry . '/Admin/Config/cronrun?type=localnav_news_up_list'
    ),
    'Admin_Config_cronrun_3'           => array('卡片页面数据更新', $entry . '/Admin/Config/cronrun?type=localnav_up_page'),
    'Admin_Config_cronlog'             => array('crontab任务日志', $entry . '/Admin/Config/cronlog'),
    'Admin_Config_errlog'              => array('逻辑运行错误日志', $entry . '/Admin/Config/errlog'),
    'Admin_Config_Channel'             => array('渠道号管理', $entry . '/Admin/Config/channel'),
    'Admin_Inbuilt'                    => array('中转链接列表', $entry . '/Admin/Inbuilt/index', $op),
    'Admin_Inbuilt_bookmark'           => array('书签页列表', $entry . '/Admin/Inbuilt/index?type=1'),
    'Admin_Incomelog'                  => array('收入统计', $entry . '/Admin/Incomelog/index'),
    'Admin_Userinnermsg_listtpl'       => array('站内消息模板配置', $entry . '/Admin/Userinnermsg/listtpl'),
    'Admin_Register'                   => array('用户列表', $entry . '/Admin/Register/index'),
    'Admin_UCenter_Config'             => array('基本配置', $entry . '/Admin/UCenter/config'),
    'Admin_Position'                   => array('广告位管理', $entry . '/Admin/Position/index'),
    'Admin_Ads'                        => array('广告管理', $entry . '/Admin/Ads/index'),
    'Admin_Uprivilege_base'            => array('基本配置管理', $entry . '/Admin/Uprivilege/base'),
    'Admin_Rule'                       => array('规则说明', $entry . '/Admin/Uprivilege/rule'),
    'Admin_Uprivilege'                 => array('等级权限管理', $entry . '/Admin/Uprivilege/level'),
    'Admin_Category'                   => array('分类管理', $entry . '/Admin/Category/index'),
    'Admin_Produces'                   => array('获取积分类物品', $entry . '/Admin/Produces/index'),
    'Admin_Commodities'                => array('消耗积分类物品', $entry . '/Admin/Commodities/index'),
    'Admin_Order'                      => array('待处理订单', $entry . '/Admin/Order/index?type=0'),
    'Admin_Order_Done'                 => array('已完成订单', $entry . '/Admin/Order/index?type=1'),
    'Admin_Order_Cancel'               => array('已取消订单', $entry . '/Admin/Order/index?type=-1'),
    'Admin_Order_New'                  => array('新增订单', $entry . '/Admin/Order/new'),
    'Admin_Logs'                       => array('积分日志', $entry . '/Admin/Logs/score'),
    'Admin_Logs_InnerMsg'              => array('站内信', $entry . '/Admin/Logs/innerMsg'),
    'Admin_Logs_Api'                   => array('充值API接口日志', $entry . '/Admin/Logs/rechargeLog'),
    'Admin_Usummary'                   => array('积分/任务统计', $entry . '/Admin/Usummary/score'),
    'Admin_Usummary_money'             => array('资金统计', $entry . '/Admin/Usummary/money'),
    'Admin_Usummary_order'             => array('订单统计', $entry . '/Admin/Usummary/order'),
    'Admin_Ulottery'                   => array('抽奖配置管理', $entry . '/Admin/Ulottery/config'),
    'Admin_Ulottery_index'             => array('奖品列表', $entry . '/Admin/Ulottery/index'),
    'Admin_Ulottery_winner'            => array('中奖信息列表', $entry . '/Admin/Ulottery/winner'),
    'Admin_Ulottery_Coupon'            => array('书券管理', $entry . '/Admin/Ulottery/couponlist'),
    'Admin_Ulottery_Quiz'              => array('答题管理', $entry . '/Admin/Ulottery/quiz'),
    'Admin_Model_Attributes'           => array('属性管理', $entry . '/Admin/Model/attributes'),
    'Admin_Umoney'                     => array('用户资金列表', $entry . '/Admin/Umoney/index'),
    'Admin_Umoney_Log'                 => array('资金日志', $entry . '/Admin/Umoney/log'),
	'Admin_Umoney_api'				=>array('api调用日志',$entry.'/Admin/Umoney/api'),
    'Admin_Umoney_Order'               => array('充值订单列表', $entry . '/Admin/Umoney/order'),
    'Admin_Experience'                 => array('经验等级信息', $entry . '/Admin/Experience/index'),
    'Admin_Experience_Type'            => array('经验等级类型', $entry . '/Admin/Experience/cat'),
    'Admin_Experience_Log'             => array('用户经验日志', $entry . '/Admin/Experience/log'),
    'Admin_Experience_Config'          => array('配置信息', $entry . '/Admin/Experience/config'),
    'Admin_Uaccount'                   => array('日兑换数据', $entry . '/Admin/Uaccount/index'),
    'Admin_Uaccount_Month'             => array('月兑换数据', $entry . '/Admin/Uaccount/month'),
    'Admin_Uaccount_BlackList'         => array('黑名单管理', $entry . '/Admin/Uaccount/blacklist'),
    'Admin_Model'                      => array('机型列表', $entry . '/Admin/Model/index'),
    'Admin_Stat_pv2_all'               => array('所有PV统计', $entry . '/Admin/Stat/pv2?group=all'),
    'Admin_Stat_pv2_3g'                => array('3gPV统计', $entry . '/Admin/Stat/pv2?group=3g'),
    'Admin_Stat_pv2_user'              => array('用户PV统计', $entry . '/Admin/Stat/pv2?group=user'),
    'Admin_Stat_pv2_voip'              => array('畅聊PV统计', $entry . '/Admin/Stat/pv2?group=voip'),
    'Admin_Stat_uv2_all'               => array('所有UV统计', $entry . '/Admin/Stat/uv2?group=all'),
    'Admin_Stat_uv2_3g'                => array('3gUV统计', $entry . '/Admin/Stat/uv2?group=3g'),
    'Admin_Stat_uv2_user'              => array('用户UV统计', $entry . '/Admin/Stat/uv2?group=user'),
    'Admin_Stat_uv2_voip'              => array('畅聊UV统计', $entry . '/Admin/Stat/uv2?group=voip'),
    'Admin_Stat_shorturl'              => array('短链统计', $entry . '/Admin/Stat/shorturl'),
    'Admin_Stat_ng'                    => array('导航统计', $entry . '/Admin/Stat/ng?tab=1'),
    'Admin_Stat_content'               => array('内容统计', $entry . '/Admin/Stat/content'),
    'Admin_Stat_column'                => array('栏目统计', $entry . '/Admin/Stat/column'),
    'Admin_Stat_leading'               => array('CP&导流统计', $entry . '/Admin/Stat/leading'),
    'Admin_Stat_hotwords'              => array('热词&搜索统计', $entry . '/Admin/Stat/hotwords'),
    'Admin_Stat_amount'                => array('当日总点击量', $entry . '/Admin/Stat/amount'),
    'Admin_Stat'                       => array('专题意见统计', $entry . '/Admin/Stat/suggestion'),
    'Admin_Stat_topiclist'             => array('专题统计', $entry . '/Admin/Stat/topicList'),
    'Admin_Ollog'                      => array('日志列表', $entry . '/Admin/Ollog/index'),
    'Admin_Page'                       => array('三屏配置', $entry . '/Admin/Page/index'),
    'Admin_Bookmark'                   => array('书签配置', $entry . '/Admin/Bookmark/index', $op),
    'Admin_Browserurl'                 => array('搜索配置', $entry . '/Admin/Browserurl/index?type=1', $op),
    'Admin_Browserurl_2'               => array('推荐网址', $entry . '/Admin/Browserurl/index?type=2'),
    'Admin_Browserurl_3'               => array('网址库', $entry . '/Admin/Browserurl/index?type=3'),
    'Admin_Browserfavicon'             => array('收藏图标', $entry . '/Admin/Browserfavicon/list'),
    'Admin_Browserreplacesearch'       => array('搜索替换', $entry . '/Admin/Browserreplacesearch/list'),
    'Admin_Browser_brand'              => array('品牌管理', $entry . '/Admin/Browserurl/brandlist'),
    'Admin_Browser_series'             => array('系列管理', $entry . '/Admin/Browserurl/serieslist'),
    'Admin_Browser_model'              => array('机型管理', $entry . '/Admin/Browserurl/modellist'),
    'Admin_Browser_conf'               => array('设置', $entry . '/Admin/Browserurl/conf'),
    'Admin_Blackurl'                   => array('黑白名单', $entry . '/Admin/Blackurl/index', $op),
    'Admin_Welcome'                    => array('欢迎图片', $entry . '/Admin/Welcome/index'),
    'Admin_Splash'                     => array('闪屏管理', $entry . '/Admin/Splash/index', $op),
    'Admin_Models'                     => array('机型管理', $entry . '/Admin/Models/index'),
    'Admin_Gioneeuser'                 => array('会员管理', $entry . '/Admin/Gioneeuser/index'),
    'Admin_Resource'                   => array('资源管理', $entry . '/Admin/Resource/index'),
    'Admin_Resourceassign'             => array('分配资源管理', $entry . '/Admin/Resourceassign/index'),
    'Admin_Area'                       => array('省市管理', $entry . '/Admin/Area/index'),
    'Admin_Address'                    => array('网点管理', $entry . '/Admin/Address/index'),
    'Admin_Questions'                  => array('常见问题管理', $entry . '/Admin/Questions/index'),
    'Admin_React'                      => array('用户反馈', $entry . '/Admin/React/index'),
    'Admin_Subject'                    => array('专题管理', $entry . '/Admin/Subject/index', $op),
    'Admin_Gousubject'                 => array('金立购专题', $entry . '/Admin/Gousubject/index'),
    'Admin_Gamesubject'                => array('游戏专题', $entry . '/Admin/Gamesubject/index'),
    'Admin_Imcsubject'                 => array('IMC专题', $entry . '/Admin/Imcsubject/index'),
    'Admin_Caipiao'                    => array('彩票合作活动专题', $entry . '/Admin/Subject/Caipiao'),
    'Admin_Voip'                       => array('基本信息', $entry . '/Admin/Voip/index'),
    'Admin_Voip_phonevest'             => array('用户马甲', $entry . '/Admin/Voip/phonevest'),
    'Admin_Attribute'                  => array('属性管理', $entry . '/Admin/Attribute/index'),
    'Admin_Label'                      => array('标签管理', $entry . '/Admin/Label/index'),
    'Admin_Jhnews'                     => array('新闻管理', $entry . '/Admin/Jhnews/index'),
    'Admin_Jhtype'                     => array('分类管理', $entry . '/Admin/Jhtype/index'),
    'Admin_Sohu'                       => array('搜狐广告管理', $entry . '/Admin/Sohu/index', $op),
    'Admin_Ngtype'                     => array('首页分类管理', $entry . '/Admin/Ngtype/index?page_id=1'),
    'Admin_Ngtype2'                    => array('子页分类管理', $entry . '/Admin/Ngtype/index?page_id=2'),
    'Admin_Ng'                         => array('导航管理', $entry . '/Admin/Ng/index', $op),
    'Admin_Ngimport'                   => array('导航导入', $entry . '/Admin/Ng/import'),
    'Admin_Ng_picimport'               => array('导航图标导入', $entry . '/Admin/Ng/picimport'),
    'Admin_Vendor'                     => array('渠道号管理', $entry . '/Admin/Vendor/index'),
    'Admin_Machine'                    => array('功能机导航', $entry . '/Admin/Machine/index'),
    'Admin_Apptype'                    => array('应用分类管理', $entry . '/Admin/Apptype/index'),
    'Admin_App'                        => array('应用管理', $entry . '/Admin/App/index', $op),
    'Admin_Webapptype'                 => array('应用分类管理', $entry . '/Admin/Webapptype/index'),
    'Admin_Webthemetype'               => array('专题分类管理', $entry . '/Admin/Webthemetype/index'),
    'Admin_Webapp'                     => array('应用管理', $entry . '/Admin/Webapp/index', $op),
    'Admin_H5game_list'                => array('H5游戏列表管理', $entry . '/Admin/h5game/list', $op),
    'Admin_H5game_type'                => array('H5游戏分类管理', $entry . '/Admin/h5game/typelist', $op),
    'Admin_H5game_cate'                => array('H5游戏主题管理', $entry . '/Admin/h5game/catelist', $op),
    'Admin_Webapp_picimport'           => array('图标导入', $entry . '/Admin/Webapp/picimport?type=icon'),
    'Admin_Webapp_picimport2'          => array('图标2导入', $entry . '/Admin/Webapp/picimport?type=icon2'),
    'Admin_Appconfig'                  => array('应用配置', $entry . '/Admin/Appconfig/index'),
    'Admin_Productattribute'           => array('属性管理', $entry . '/Admin/Productattribute/index'),
    'Admin_Topic'                      => array('专题管理', $entry . '/Admin/Topic/index', $op),
    'Admin_Feedback'                   => array('反馈管理', $entry . '/Admin/Feedback/index'),
    'Admin_Feedbackconf'               => array('用户反馈配置', $entry . '/Admin/Feedback/config'),
    'Admin_Feedbackmsg'                => array('用户反馈管理', $entry . '/Admin/Feedback/msglist'),
    'Admin_Feedbackfaq'                => array('FAQ反馈管理', $entry . '/Admin/Feedback/faqlist'),
    'Admin_Feedbackkey'                => array('关键字管理', $entry . '/Admin/Feedback/keylist'),
    'Admin_Createurl'                  => array('生成内容链接', $entry . '/Admin/Topic/createurl'),
    'Admin_Elifeserver'                => array('产品管理', $entry . '/Admin/Elifeserver/index'),
    'Admin_Shorturl'                   => array('自定义短链接列表', $entry . '/Admin/Shorturl/list'),
    'Admin_Column'                     => array('聚合栏目管理', $entry . '/Admin/Column/index'),
    'Admin_Outnews'                    => array('聚合新闻管理', $entry . '/Admin/Outnews/index'),
    'Admin_Gamepad_setting'            => array('问卷设置', $entry . '/Admin/Gamepad/setting'),
    'Admin_Gamepad_list'               => array('问卷用户列表', $entry . '/Admin/Gamepad/list'),
    'Admin_Gamepad_stats'              => array('问卷统计报表', $entry . '/Admin/Gamepad/stats'),
    'Admin_E7'                         => array('E7用户报表', $entry . '/Admin/E7/index'),
    'Admin_CleanVer'                   => array('缓存清除', $entry . '/Admin/Config/cleanver'),
    'Admin_BaiduKeywords'              => array('百度热词', $entry . '/Admin/Config/baidukeywords'),
    'Admin_CheckUrl'                   => array('URL检测', $entry . '/Admin/Config/checkUrl'),
    'Admin_Recwebsite'                 => array('推荐网址', $entry . '/Admin/Recwebsite/listsite?type=url', $op),
    'Admin_Recwebsite_group'           => array('推荐站点', $entry . '/Admin/Recwebsite/listsite?type=site'),
    //'Admin_Recwebsite_tourl'    => array('固定网址', $entry . '/Admin/Recwebsite/tourl'),
    'Admin_Stat_tourl'                 => array('书签统计', $entry . '/Admin/Stat/tourl'),
    'Admin_Stat_monkeynum'             => array('接口性能', $entry . '/Admin/Stat/monkeynum'),
    'Admin_stat_pv3'                   => array('分页面日报', $entry . '/Admin/Stat/pvstat'),
    'Admin_stat_CpMonth'               => array('月CP对账', $entry . '/Admin/Stat/cpmonth'),
    'Admin_Stat_CpDay'                 => array('CP日报', $entry . '/Admin/Stat/cpday'),
    'Admin_Stat_CpInit'                => array('CP数据初始化', $entry . '/Admin/Stat/cpinit'),
    'Admin_Stat_inbuilt'               => array('中转链接统计', $entry . '/Admin/Stat/inbuilt'),
    'Admin_Voipstat'                   => array('畅聊统计信息', $entry . '/Admin/Voipstat/visit'),
    'Admin_Userstat_entrance'          => array('入口日报', $entry . '/Admin/Userstat/entrance'),
    'Admin_Userstat_signin'            => array('用户签到', $entry . '/Admin/Userstat/signin'),
    'Admin_Userstat_exchange'          => array('金币兑换日报', $entry . '/Admin/Userstat/exchange'),
    'Admin_Userstat_task'              => array('任务日报', $entry . '/Admin/Userstat/task'),
    'Admin_Userstat_day'               => array('日报数据', $entry . '/Admin/Userstat/day'),
    'Admin_Userstat_month'             => array('月报数据', $entry . '/Admin/Userstat/month'),
    'Admin_Userstat_rank'              => array('金币领取排名', $entry . '/Admin/Userstat/rank'),
    'Admin_Userstat_lottery'           => array('抽奖日报', $entry . '/Admin/Userstat/lottery'),
    'Admin_Userstat_scratch'           => array('刮奖日报', $entry . '/Admin/Userstat/scratch'),
    'Admin_Userstat'                   => array('畅聊通话时长兑换', $entry . '/Admin/Userstat/chatday'),
    'Admin_Userstat_snatch'            => array('夺宝奇兵日报', $entry . '/Admin/Userstat/snatch'),
    'Admin_Userstat_Experience'        => array('用户经验等级统计', $entry . '/Admin/Userstat/expUser'),
    'Admin_Userstat_quiz'              => array('答题日报', $entry . '/Admin/Userstat/quiz'),
    'Admin_Userstat_browseronlinestat' => array('浏览器在线数据', $entry . '/Admin/Userstat/browseronlinestat'),
    'Admin_Localnav'                   => array('导航数据', $entry . '/Admin/Localnav/list', $op),
    'Admin_Localnav_type'              => array('导航卡片', $entry . '/Admin/Localnav/listtype'),
    'Admin_Localnav_column'            => array('导航栏目', $entry . '/Admin/Localnav/listcolumn'),
    'Admin_Localnav_import'            => array('导入', $entry . '/Admin/Localnav/import'),
	'Admin_Localnav_cardtitle'         => array('卡片管理配置', $entry . '/Admin/Localnav/cardtitle'),
    'Admin_Weixin'                     => array('配置管理', $entry . '/Admin/Weixin/config'),
    'Admin_Weixin_user'                => array('用户管理', $entry . '/Admin/Weixin/user'),
    'Admin_Weixin_menu'                => array('菜单管理', $entry . '/Admin/Weixin/menu'),
    'Admin_Weixin_command'             => array('指令管理', $entry . '/Admin/Weixin/command'),
    'Admin_Weixin_feedback'            => array('反馈管理', $entry . '/Admin/Weixin/feedback'),
    'Admin_Weixin_msg'                 => array('图文管理', $entry . '/Admin/Weixin/msg'),
    'Admin_Weixin_concern'             => array('关注送金币', $entry . '/Admin/Weixin/concern'),
    'Admin_Wxhelp'                     => array('数据列表', $entry . '/Admin/Wxhelp/list'),
    'Admin_Wxhelp_user'                => array('用户列表', $entry . '/Admin/Wxhelp/userlist'),
    'Admin_Wxhelp_address'             => array('地址列表', $entry . '/Admin/Wxhelp/addresslist'),
    'Admin_Wxhelp_stat'                => array('操作统计', $entry . '/Admin/Wxhelp/stat'),
    'Admin_Wxhelp_statuserincr'        => array('用户增长', $entry . '/Admin/Wxhelp/statuserincr'),
    'Admin_Parter'                     => array('账号管理', $entry . '/Admin/Parter/account'),
    'Admin_Parter_Businuss'            => array('CP业务管理', $entry . '/Admin/Parter/business'),
    'Admin_Parter_Qualification'       => array('CP资质管理', $entry . '/Admin/Parter/qualification'),
    'Admin_Parter_Click'               => array('业务点击查询', $entry . '/Admin/Parter/clicks'),
    'Admin_Parter_Day'                 => array('CP日报查询', $entry . '/Admin/Parter/day'),
    'Admin_Parter_Month'               => array('CP月对账系统', $entry . '/Admin/Parter/month'),
    'Admin_Localstat'                  => array('二级新闻统计', $entry . '/Admin/Localstat/subpage'),
    'Admin_Navad_stat'                 => array('卡片功能广告', $entry . '/Admin/Navad/stat'),
    'Admin_Localstat_statall'          => array('内容总点击报表', $entry . '/Admin/Localstat/statall'),
    //'Admin_Localstat_Channel'    => array('频道管理页统计', $entry . '/Admin/Localstat/channel'),
    //'Admin_Localstat_Detail'     => array('详情页统计', $entry . '/Admin/Localstat/detail'),
    'Admin_Navnews_column'             => array('栏目列表', $entry . '/Admin/Navnews/listcolumn?group=news'),
    'Admin_Navnews_source'             => array('来源列表', $entry . '/Admin/Navnews/listsource?group=news'),
    'Admin_Navad_listnews'             => array('广告列表', $entry . '/Admin/Navad/listnews'),
    'Admin_Navad_listfun'              => array('广告列表', $entry . '/Admin/Navad/listfun'),
    'Admin_Navad_listpic'              => array('广告列表', $entry . '/Admin/Navad/listpic'),
    'Admin_Navnews'                    => array('数据列表', $entry . '/Admin/Navnews/listrecord?group=news'),
    'Admin_Navpic_column'              => array('栏目列表', $entry . '/Admin/Navnews/listcolumn?group=pic'),
    'Admin_Navpic_source'              => array('来源列表', $entry . '/Admin/Navnews/listsource?group=pic'),
    'Admin_Navpic'                     => array('数据列表', $entry . '/Admin/Navnews/listrecord?group=pic'),
    'Admin_Navfun_column'              => array('栏目列表', $entry . '/Admin/Navnews/listcolumn?group=fun'),
    'Admin_Navfun_source'              => array('来源列表', $entry . '/Admin/Navnews/listsource?group=fun'),
    'Admin_Navfun'                     => array('数据列表', $entry . '/Admin/Navnews/listrecord?group=fun'),
    'Admin_Partner_Weathercolumn'      => array('栏目列表', $entry . '/Admin/Navnews/listcolumn?group=weather'),
    'Admin_Partner_Weathersource'      => array('来源列表', $entry . '/Admin/Navnews/listsource?group=weather'),
    'Admin_Partner_Weather'            => array('数据列表', $entry . '/Admin/Navnews/listrecord?group=weather'),
    'Admin_Navad_listweather'          => array('天气广告', $entry . '/Admin/Navad/listweather'),
    'Admin_Partner_Desktop'            => array('合作-桌面', $entry . '/Admin/Partner/desktop'),
    'Admin_Partner_listsingernews'     => array('歌手新闻列表', $entry . '/Admin/Partner/listsingernews'),
    'Admin_Partner_listhistorytoday'   => array('历史今天列表', $entry . '/Admin/Partner/listhistorytoday'),
    'Admin_Navad_listcalendar'         => array('日历广告', $entry . '/Admin/Navad/listcalendar'),
    'Admin_Config_changyan'            => array('畅言配置', $entry . '/Admin/Config/changyan'),
    'Admin_Config_geetest'             => array('极验验证配置', $entry . '/Admin/Config/geetest'),
    'Admin_Voip_config'                => array('云之讯配置', $entry . '/Admin/Voip/config'),
    'Admin_Partner_statweather'        => array('天气统计', $entry . '/Admin/Partner/statweather'),
    'Admin_Partner_statmusic'          => array('音乐统计', $entry . '/Admin/Partner/statmusic'),
    'Admin_Partner_statcalendar'       => array('日历统计', $entry . '/Admin/Partner/statcalendar'),
    'Admin_Wanka'                      => array('玩咖配置', $entry . '/Admin/Wanka/index', $op),
    'Admin_Pushtools'                  => array('PUSH工具', $entry . '/Admin/Pushtools/index', $op),
    'Admin_Duanwu_config'              => array('基础配置', $entry . '/Admin/Duanwu/config'),
    'Admin_Duanwu_listuser'            => array('用户列表', $entry . '/Admin/Duanwu/listuser'),
    'Admin_Duanwu_listlog'             => array('获奖日志', $entry . '/Admin/Duanwu/listlog'),
    'Admin_Config_browserbase'         => array('基础配置', $entry . '/Admin/Config/browserbase'),
    'Admin_Stat_duanwu'                => array('端午活动统计', $entry . '/Admin/Stat/duanwu'),
	'Admin_Stat_qixi'						 =>array('七夕活动统计',$entry.'/Admin/Stat/qixi'),
	'Admin_Stat_national'				=>array('国庆活动统计',$entry.'/Admin/Stat/national'),
    'Admin_Stat_seckill'				=>array('双十一活动统计',$entry.'/Admin/Stat/seckill'),
    'Admin_Label_listimei'             => array('标签imei数据管理', $entry . '/Admin/Label/listimei'),
    'Admin_Config_search'              => array('搜索引擎管理', $entry . '/Admin/Config/searchlist'),
    'Admin_Stat_accesstimes'           => array('接口访问统计', $entry . '/Admin/Stat/accesstimes'),
    'Admin_Navnews_statfun'            => array('二级段子统计', $entry . '/Admin/Navnews/statfun'),
    'Admin_Navnews_statpic'            => array('二级美图统计', $entry . '/Admin/Navnews/statpic'),
	'Admin_Link_config'						=>array('基本配置',$entry.'/Admin/Link/config'),
	'Admin_Link_index'						=>array('中奖商品管理',$entry.'/Admin/Link/index'),
	'Admin_Link_log'							=>array('日志信息',$entry.'/Admin/Link/log'),

	'Admin_National_Config'				=>array('活动配置',$entry.'/Admin/National/config'),

	'Admin_National_Index'				=>array('奖品列表',$entry.'/Admin/National/index'),
	'Admin_National_Log'					=>array('中奖日志',$entry.'/Admin/National/log'),
	'Admin_Event'								=>array('活动基本信息',$entry.'/Admin/Event/type'),
	'Admin_Event_Goods'					=>array('活动商品管理',$entry.'/Admin/Event/goodslist'),
	'Admin_Event_Prize'					=>array('活动奖品管理',$entry.'/Admin/Event/prizelist'),
	'Admin_Event_Log'					=>array('中奖日志',$entry.'/Admin/Event/logs'),
	'Admin_Seckill_Config'              =>array('双十一预约配置',$entry.'/Admin/Seckill/config'),
	'Admin_Seckill_remind'              =>array('双十一活动预约列表',$entry.'/Admin/Seckill/list'),
);

return array($config, $view);