<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
//系统============================================
$config['Admin_System'] = array(
    'name' => '系统',
    'items' => array(
        array(
            'name' => '用户',
            'items' => array(
                'Admin_User',
                'Admin_Group',
                'Admin_Statistic_Behavioral',
            ),
        ),
    ),
);

//全局配置========================================
$config['Admin_Global'] = array(
    'name' => '全局配置',
    'items' => array(
        array(
            'name' => '渠道号管理',
            'items' => array(
                'Admin_Channelname',
                'Admin_Channelmodule',
                'Admin_Channelcode'
            ),
        ),
        array(
            'name' => '配置管理',
            'items' => array(
                'Admin_Config',
                'Admin_Config_txt',
                'Admin_Config_History',
                'Admin_Config_Navigation',
            )
        ),
        array(
            'name' => '会员管理',
            'items' => array(
                'Admin_Gouuser',
                'Admin_User_Uid',
                'Admin_User_Score',
            )
        ),
        'Admin_Client_Start',
        'Admin_Sensitive',
    ),
);

//H5版========================================
$config['Admin_H5'] = array(
    'name' => 'H5版',
    'items' => array(
        'Admin_Ad_H5',
        'Admin_Channel_H5',
        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_H5_Pic',
                'Admin_Cod_Guide_H5_Txt',
            ),
        ),
    )
);
//预装版========================================
$config['Admin_Apk'] = array(
    'name' => '预装版',
    'items' => array(
        'Admin_Ad_apk',
//        array(
//            'name' => '预装广告',
//            'items' => array(
//                'Admin_Ad_apk_1',
//                '_apk_2',
//                '_apk_3',
//                '_apk_4',
//                'Admin_Ad_apk_5',
//                'Admin_Ad_apk_6',
//                'Admin_Ad_apk_7',
//                'Admin_Ad_apk_8',
//                'Admin_Ad_apk_9',
//                'Admin_Ad_apk_10',
//            ),
//        ),
        'Admin_Channel_apk',

        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_apk_Pic',
                'Admin_Cod_Guide_apk_Txt',
            ),
        ),
        array(
            'name' => '本地化平台',
            'items' => array(
                'Admin_Clientchannelcate',
                'Admin_Clientchannel_apk'
            ),
        ),
//              'Admin_Favorite',
    )
);
//渠道版========================================
$config['Admin_Channel'] = array(
    'name' => '渠道版',
    'items' => array(
        'Admin_Ad_channel',
        'Admin_Channel_channel',
        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_channel_Pic',
                'Admin_Cod_Guide_channel_Txt',
            ),
        ),
    )
);

//穷购物版========================================
$config['Admin_Market'] = array(
    'name' => '穷购物',
    'items' => array(
        'Admin_Ad_market',
        'Admin_Channel_market',
        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_market_Pic',
                'Admin_Cod_Guide_market_Txt',
            ),
        ),
    )
);
//APP版========================================
$config['Admin_App'] = array(
    'name' => 'APP版',
    'items' => array(
        'Admin_Ad_app',
        'Admin_Channel_app',
        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_app_Pic',
                'Admin_Cod_Guide_app_Txt',
            ),
        ),
    )
);

//IOS版========================================
$config['Admin_IOS'] = array(
    'name' => 'IOS版',
    'items' => array(
        'Admin_Ad_ios',
        'Admin_Clientchannel_ios',
        array(
            'name' => '货到付款',
            'items' => array(
                'Admin_Cod_Type',
                'Admin_Cod_Guide_ios_Pic',
                'Admin_Cod_Guide_ios_Txt',
            ),
        ),
        array(
            'name' => 'push',
            'items' => array(
                'Admin_Ios_Msg',
                'Admin_Ios_Token'
            ),
        ),
        'Admin_Activity_Recommend',
    )
);


//$config['Admin_Eb'] = array(
//    'name' => '电商管理',
//    'items' => array(
//        array(
//            'name' => '第三方报表',
//            'items' => array(
//                'Admin_Partnerorder',
//            ),
//        ),
//        array(
//            'name' => '专区',
//            'items' => array(
//                'Admin_Mall_Category',
//                'Admin_Mall_Goods',
//            )
//        ),
//        array(
//            'name' => '积分换购',
//            'items' => array(
//                'Admin_Supplier',
//                'Admin_Localgoods',
//                'Admin_Order',
//                'Admin_Readcoin',
//            )
//        ),
//        array(
//            'name' => 'Amigo商城',
//            'items' => array(
//                'Admin_Amigo_Weather',
//                'Admin_Amigo_Supplier',
//                'Admin_Amigo_Localgoods',
//                'Admin_Amigo_Order',
//                'Admin_Amigo_Orderreturn',
//                'Admin_Amigo_Reason',
//                'Admin_Amigo_Activity',
//                'Admin_Amigo_Tag',
//                'Admin_Amigo_Shipping'
//            )
//        ),
//        /* array(
//                'name'=>'去逛',
//                'items'=>array(
//                        'Admin_Client_Category',
//                        'Admin_Client_Goods'
//                ),
//        ), */
//        array(
//            'name' => '品牌汇',
//            'items' => array(
//                'Admin_Brand_Category',
//                'Admin_Brand_Brand',
//                'Admin_Brand_Goods',
//            ),
//        ),
//
//        array(
//            'name' => '话费充值',
//            'items' => array(
//                'Admin_Recharge_Price',
//                'Admin_Recharge_Operator',
//                'Admin_Recharge_Orders',
//                'Admin_Recharge_Channel',
//            )
//        ),
//        array(
//            'name' => '第三方库',
//            'items' => array(
//                'Admin_Third_Goods',
//                'Admin_Third_Shop',
//                'Admin_Third_Web',
//            )
//        ),
//    )
// );


/*
$config['Admin_Activity'] = array(
		'name' => '活动',
		'items' => array(
				array(
						'name' => '免单活动',
						'items' => array(
								'Admin_Wantlog',
								'Admin_Orderfree',
								'Admin_Orderfreelog'
						)
				),
				array(
						'name' => '抽奖管理',
						'items' => array(
								'Admin_Faterule',
								'Admin_Fateuser',
								'Admin_Fatelog',
						)
				),
				'Admin_Activity_Coin',
				'Admin_Activity_Fanli',
		)
);*/
/* $config['Admin_Fanli'] = array(
 'name' => '返利',
		'items' => array(
				'Admin_Fanli_User',
				'Admin_Fanli_Order',
				'Admin_Fanli_Wdlog',
				array(
						'name' => '返利分类',
						'items' => array(
								'Admin_Fanli_Ptype',
								'Admin_Fanli_Type'
						)
				),
		)
); */

$config['Admin_Yunying'] = array(
    'name' => '运营',
    'items' => array(
        'Admin_Url',
        'Admin_Topic',
        array(
            'name' => '淘宝好店',
            'items' => array(
                'Admin_Client_Shops',
                'Admin_Client_Tag',
            )
        ),
        array(
            'name' => '全网好货',
            'items' => array(
                'Admin_Channelgoodscate',
                'Admin_Channelgoods'
            ),
        ),
        array(
            'name' => '淘宝热门',
            'items' => array(
                'Admin_Amigo_Activity',
                'Admin_Amigo_Tag',
            ),
        ),
        array(
            'name' => '抽奖活动',
            'items' => array(
                'Admin_Activity_Lotterycate',
                'Admin_Activity_Lotteryawards',
                'Admin_Activity_Lotteryusers',
                'Admin_Activity_Lotterylog'
            ),
        ),
        'Admin_Sms',
        array(
            'name' => '金立PUSH',
            'items' => array(
                'Admin_Msg',
                'Admin_Rid',
                'Admin_Pushlog',
            )
        ),
    ),
);

$config['Admin_Content'] = array(
    'name' => '内容',
    'items' => array(
        array(
            'name' => '商品专题',
            'items' => array(
                'Admin_Fanfan_Topicgoods',
                'Admin_Fanfan_Topiccate',
                'Admin_Fanfan_Topic',
            ),
        ),

        array(
            'name' => '主题汇聚',
            'items' => array(
                'Admin_Store_Category',
                'Admin_Store_Info_activity',
                'Admin_Store_Info_pingtai',
                'Admin_Store_Info_goodsCate'
            )
        ),
        array(
            'name' => '专题汇聚',
            'items' => array(
                'Admin_Subject',
                'Admin_Goods'
            )
        ),
        array(
            'name' => '专区',
            'items' => array(
                'Admin_Mall_Category',
                'Admin_Mall_Goods',
            )
        ),
        array(
            'name' => '品牌汇',
            'items' => array(
                'Admin_Brand_Category',
                'Admin_Brand_Brand',
                'Admin_Brand_Goods',
            ),
        ),
    )
);


$config['Admin_Module'] = array(
    'name' => '模块',
    'items' => array(
        array(
            'name' => '搜索管理',
            'items' => array(
                'Admin_Client_Keywords',
                'Admin_Client_Keywordslog',
                'Admin_Type_Ptype',
                'Admin_Type_Type',
                'Admin_Config_url'
            )
        ),
        array(
            'name' => '购物教程',
            'items' => array(
                'Admin_Config_Help',
                'Admin_Help',
            )
        ),
        array(
            'name' => '知物管理',
            'items' => array(
                'Admin_Story',
                'Admin_User_Author',
                'Admin_Storycategory',
                'Admin_Comment',
                'Admin_Comment_Verify',
                'Admin_Favorite_Story',
            )
        ),
        array(
            'name' => '砍价管理',
            'items' => array(
                'Admin_Cut_Goods',
                'Admin_Cut_Store',
                'Admin_Cut_Shops',
                'Admin_Cut_Type',
                'Admin_Cut_Order',
                'Admin_Cut_Gameorder',
                'Admin_Cut_Log',
                'Admin_Cut_Game',
                'Admin_Cut_User',
                'Admin_Cut_Stat'
            )
        ),
        array(
            'name' => '问答管理',
            'items' => array(
                'Admin_Qa_Skey',
                'Admin_User_Virtual',
                'Admin_Qa_Question',
                'Admin_Qa_Answer',
                'Admin_Qa_Stat'
            )
        ),
        array(
            'name' => '客户反馈',
            'items' => array(
                'Admin_Cs_Faq_Question',
                'Admin_Cs_Faq_Category',
                'Admin_Cs_Feedback_Category',
                'Admin_Cs_Feedback_Kefu',
                'Admin_Cs_Feedback_Qa',
                'Admin_Cs_Feedback_Quick',
                'Admin_Cs_Feedback_Link',
            )
        ),
    )
);
//统计和日志========================================
$config['Admin_Statistics'] = array(
    'name' => '统计',
    'items' => array(
        'Admin_Stat',
        'Admin_Stat_uv',
        'Admin_Hash',
        'Admin_Stat_thirdpart',
//                'Admin_Stat_plot',
        'Admin_Stat_pie',
        'Admin_Stat_order',
        'Admin_Apilog',
        'Admin_Log',
        array(
            'name' => '收藏管理',
            'items' => array(
                'Admin_User_Favorite',
                'Admin_Favorite_Goods',
                'Admin_Favorite_Shop',
                'Admin_Favorite_Web',
            )
        ),
        array(
            'name' => '第三方报表',
            'items' => array(
                'Admin_Partnerorder'
            )
        ),
        array(
            'name' => '第三方库',
            'items' => array(
                'Admin_Third_Goods',
                'Admin_Third_Shop',
                'Admin_Third_Web'
            )
        ),
        'Admin_Statistic_Click',
    ),
);

//对外========================================
$config['Admin_Outside'] = array(
    'name' => '对外',
    'items' => array(
        'Admin_Amigo_Weather',
        'Admin_News',
        'Admin_Resource',
        'Admin_Marketsoft',
        'Admin_Client_Taobaourl',
        'Admin_Amigo_Shipping'
    ),
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    'Admin_User' => array('用户信息管理', $entry . '/Admin/User/index'),
    'Admin_Group' => array('用户组管理', $entry . '/Admin/Group/index'),
    'Admin_Statistic_Behavioral' => array('用户行为统计', $entry . '/Admin/Statistic_Behavioral/index'),
    'Admin_Ad_channel' => array('渠道广告', $entry . '/Admin/Ad/index?channel_id=3'),
    'Admin_Ad_H5' => array('H5广告', $entry . '/Admin/Ad/index?channel_id=1'),
    'Admin_Ad_apk' => array('预装广告', $entry . '/Admin/Ad/index?channel_id=2'),
    'Admin_Ad_market' => array('穷购物广告', $entry . '/Admin/Ad/index?channel_id=4'),
    'Admin_Ad_app' => array('app广告', $entry . '/Admin/Ad/index?channel_id=5'),
    'Admin_Ad_ios' => array('ios广告', $entry . '/Admin/Ad/index?channel_id=6'),

    'Admin_Faterule' => array('抽奖规则', $entry . '/Admin/Faterule/index'),
    'Admin_Fateuser' => array('抽奖用户', $entry . '/Admin/Fateuser/index'),
    'Admin_Fatelog' => array('抽奖日志', $entry . '/Admin/Fatelog/index'),
    'Admin_Stat' => array('PV统计', $entry . '/Admin/Stat/pv'),
    'Admin_Stat_uv' => array('UV统计', $entry . '/Admin/Stat/uv'),
    'Admin_Stat_thirdpart' => array('点击量报表', $entry . '/Admin/Stat/thirdpart'),
    //'Admin_Stat_plot'=>array('点阵报表',$entry . '/Admin/Stat/plot'),
    'Admin_Stat_pie' => array('百分比报表', $entry . '/Admin/Stat/pie'),
    'Admin_Stat_order' => array('订单报表', $entry . '/Admin/Stat/order'),

    'Admin_Links' => array('首页链接', $entry . '/Admin/Links/index'),
    'Admin_Links_tool' => array('百宝箱', $entry . '/Admin/Links/tool'),
    'Admin_React' => array('用户反馈', $entry . '/Admin/React/index'),
    'Admin_Ordershow' => array('晒单列表', $entry . '/Admin/Ordershow/index'),
    'Admin_Orderchannel' => array('下单渠道管理', $entry . '/Admin/Orderchannel/index'),
    'Admin_Ordershowconfig' => array('晒单配置', $entry . '/Admin/Ordershowconfig/index'),
    'Admin_Subject' => array('专题管理', $entry . '/Admin/Subject/index'),
    'Admin_Goods' => array('商品管理', $entry . '/Admin/Goods/index'),
    'Admin_Localgoods' => array('商品管理', $entry . '/Admin/Localgoods/Index?show_type=1'),
    'Admin_Supplier' => array('供应商管理', $entry . '/Admin/Supplier/index?show_type=1'),
    'Admin_Order' => array('订单管理', $entry . '/Admin/Order/index?show_type=1'),

    'Admin_Cod_Type' => array('分类管理', $entry . '/Admin/Cod_Type/Index'),
    'Admin_Wantlog' => array('我想要日志', $entry . '/Admin/Wantlog/index'),
    'Admin_Orderfree' => array('免单抽奖', $entry . '/Admin/Wantlog/orderfree'),
    'Admin_Orderfreelog' => array('免单日志', $entry . '/Admin/Orderfreelog/index'),
    'Admin_Gouuser' => array('会员管理', $entry . '/Admin/Gouuser/index'),
    'Admin_Channel_H5' => array('H5平台', $entry . '/Admin/Channel/index?channel_id=1'),
    'Admin_Channel_apk' => array('预装平台', $entry . '/Admin/Channel/index?channel_id=2'),
    'Admin_Channel_channel' => array('渠道平台', $entry . '/Admin/Channel/index?channel_id=3'),
    'Admin_Channel_market' => array('穷购物平台', $entry . '/Admin/Channel/index?channel_id=4'),
    'Admin_Channel_app' => array('app平台', $entry . '/Admin/Channel/index?channel_id=5'),
    'Admin_Clientchannelcate' => array('平台分类', $entry . '/Admin/Clientchannelcate/index'),
    'Admin_Clientchannel_apk' => array('Apk平台', $entry . '/Admin/Clientchannel/index?channel_id=1'),
    'Admin_Clientchannel_ios' => array('iOS平台', $entry . '/Admin/Clientchannel/index?channel_id=2'),
    'Admin_Channelgoodscate' => array('分类管理', $entry . '/Admin/Channelgoodscate/index'),
    'Admin_Channelgoods' => array('商品管理', $entry . '/Admin/Channelgoods/index'),

    'Admin_Taoke' => array('淘客商品', $entry . '/Admin/Taoke/index'),
    'Admin_Config' => array('系统配置', $entry . '/Admin/Config/index'),
    'Admin_Config_url' => array('搜索配置', $entry . '/Admin/Config/url'),
    'Admin_Config_txt' => array('文本配置', $entry . '/Admin/Config/txt'),
    'Admin_Cut_Rule' => array('砍价规则', $entry . '/Admin/Config/txt'),
    'Admin_Guide' => array('导购管理', $entry . '/Admin/Guide/index'),
    'Admin_Guidetype' => array('导购分类', $entry . '/Admin/Guidetype/index'),
    'Admin_Sms' => array('短信平台', $entry . '/Admin/Sms/index'),
    'Admin_Mall_Category' => array('分类管理', $entry . '/Admin/Mall_Category/index'),
    'Admin_Mall_Goods' => array('商品管理', $entry . '/Admin/Mall_Goods/index'),
    'Admin_Mall_Ad' => array('广告管理', $entry . '/Admin/Mall_Ad/index'),
    'Admin_Cod_Ad' => array('广告管理', $entry . '/Admin/Cod_Ad/index'),
// 	'Admin_Cod_Guide' => array('导购管理',$entry . '/Admin/Cod_Guide/index'),
    'Admin_Cod_Guide_H5_Txt'=> array('H5版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=1&type=2'),
    'Admin_Cod_Guide_apk_Txt'=> array('预装版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=2&type=2'),
    'Admin_Cod_Guide_channel_Txt'=> array('渠道版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=3&type=2'),
    'Admin_Cod_Guide_market_Txt'=> array('穷购物版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=4&type=2'),
    'Admin_Cod_Guide_app_Txt'=> array('APP版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=5&type=2'),
    'Admin_Cod_Guide_ios_Txt'=> array('iOS版文字链', $entry . '/Admin/Cod_Guide/index?channel_id=6&type=2'),
    'Admin_Cod_Guide_H5_Pic'=> array('H5版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=1&type=1'),
    'Admin_Cod_Guide_apk_Pic'=> array('预装版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=2&type=1'),
    'Admin_Cod_Guide_channel_Pic'=> array('渠道版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=3&type=1'),
    'Admin_Cod_Guide_market_Pic'=> array('穷购物版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=4&type=1'),
    'Admin_Cod_Guide_app_Pic'=> array('APP版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=5&type=1'),
    'Admin_Cod_Guide_ios_Pic'=> array('iOS版Banner', $entry . '/Admin/Cod_Guide/index?channel_id=6&type=1'),
    'Admin_Cod_Guide_H5' => array('H5导购', $entry . '/Admin/Cod_Guide/index?channel_id=1'),
    'Admin_Cod_Guide_apk' => array('预装导购', $entry . '/Admin/Cod_Guide/index?channel_id=2'),
    'Admin_Cod_Guide_channel' => array('渠道导购', $entry . '/Admin/Cod_Guide/index?channel_id=3'),
    'Admin_Cod_Guide_market' => array('穷购物导购', $entry . '/Admin/Cod_Guide/index?channel_id=4'),
    'Admin_Cod_Guide_app' => array('app导购', $entry . '/Admin/Cod_Guide/index?channel_id=5'),
    'Admin_Cod_Guide_ios' => array('ios导购', $entry . '/Admin/Cod_Guide/index?channel_id=6'),
    'Admin_Malltaoke' => array('淘客商品', $entry . '/Admin/Taoke/index'),
    'Admin_Report' => array('淘客报表', $entry . '/Admin/Report/index'),
    'Admin_Activity_Coin' => array('送积分活动', $entry . '/Admin/Activity_Coin/index'),
    'Admin_Activity_Fanli' => array('返利活动', $entry . '/Admin/Activity_Fanli/index'),
    'Admin_Url' => array('分成渠道', $entry . '/Admin/Url/index'),
    'Admin_Resource' => array('资源管理', $entry . '/Admin/Resource/index'),
    'Admin_Notice' => array('消息通知', $entry . '/Admin/Notice/index'),
    'Admin_Readcoin' => array('阅读币管理', $entry . '/Admin/Readcoin/index'),
    'Admin_Rid' => array('Rid管理', $entry . '/Admin/Rid/index'),
    'Admin_Msg' => array('push消息管理', $entry . '/Admin/Msg/index'),
    'Admin_Pushlog' => array('Push日志管理', $entry . '/Admin/Pushlog/index'),

    'Admin_Client_Category' => array('分类管理', $entry . '/Admin/Client_Category/index'),
    'Admin_Client_Goods' => array('商品管理', $entry . '/Admin/Client_Goods/index'),
    'Admin_Client_Keywords' => array('关键字', $entry . '/Admin/Client_Keywords/index'),
    'Admin_Client_Keywordslog' => array('搜索排行', $entry . '/Admin/Client_Keywordslog/index'),
    'Admin_Client_Favorite' => array('收藏', $entry . '/Admin/Client_Favorite/index'),
    'Admin_Client_Shops' => array('好店管理', $entry . '/Admin/Client_Shops/index'),
    'Admin_Client_Tag' => array('好店标签', $entry . '/Admin/Client_Tag/index'),
    'Admin_Topic' => array('活动专题', $entry . '/Admin/Topic/index'),
    'Admin_News' => array('新闻管理', $entry . '/Admin/News/index'),
    'Admin_Client_Start' => array('闪屏管理', $entry . '/Admin/Client_Start/index'),
    'Admin_Client_Taobaourl' => array('淘热卖地址', $entry . '/Admin/Client_Taobaourl/index'),
    'Admin_Fanli' => array('返利', $entry . '/Admin/Fanli/index'),
    'Admin_Fanli_User' => array('用户管理', $entry . '/Admin/Fanli_User/index'),
    'Admin_Fanli_Order' => array('返利订单', $entry . '/Admin/Fanli_Order/index'),
    'Admin_Fanli_Wdlog' => array('提现日志', $entry . '/Admin/Fanli_Wdlog/index'),
    'Admin_Activity_Share' => array('分享活动', $entry . '/Admin/Activity_Share/index'),
    'Admin_Activity_Sharelog' => array('分享日志', $entry . '/Admin/Activity_Sharelog/index'),
    'Admin_Activity_Shareqq' => array('分享号码', $entry . '/Admin/Activity_Shareqq/index'),
    'Admin_Type_Ptype' => array('一级分类', $entry . '/Admin/Type_Ptype/index'),
    'Admin_Type_Type' => array('二级分类', $entry . '/Admin/Type_Ptype/index'),
    'Admin_Type_Type' => array('二级分类', $entry . '/Admin/Type_Type/index'),
    'Admin_Type_Ad' => array('广告管理', $entry . '/Admin/Type_Ad/index'),

    'Admin_Amigo_Weather' => array('AMI天气配置', $entry . '/Admin/Amigo_Weather/index'),
    'Admin_Amigo_Localgoods' => array('商品管理', $entry . '/Admin/Amigo_Localgoods/Index'),
    'Admin_Amigo_Supplier' => array('供应商管理', $entry . '/Admin/Amigo_Supplier/index'),
    'Admin_Amigo_Order' => array('订单管理', $entry . '/Admin/Amigo_Order/index'),
    'Admin_Amigo_Orderreturn' => array('退换货管理', $entry . '/Admin/Amigo_Orderreturn/index'),
    'Admin_Amigo_Reason' => array('退换货原因', $entry . '/Admin/Amigo_Reason/index'),
    'Admin_Amigo_Activity' => array('活动管理', $entry . '/Admin/Amigo_Activity/index'),
    'Admin_Amigo_Tag' => array('活动标签', $entry . '/Admin/Amigo_Tag/index'),
    'Admin_Amigo_Shipping' => array('物流公司管理', $entry . '/Admin/Amigo_Shipping/index'),

    'Admin_Activity_Lotterycate' => array('抽奖活动', $entry . '/Admin/Activity_Lotterycate/index'),
    'Admin_Activity_Lotteryawards' => array('奖品设置', $entry . '/Admin/Activity_Lotteryawards/index'),
    'Admin_Activity_Lotteryusers' => array('奖品发放', $entry . '/Admin/Activity_Lotteryusers/list'),
    'Admin_Activity_Lotterylog' => array('抽奖记录', $entry . '/Admin/Activity_Lotterylog/index'),

    'Admin_Marketsoft' => array('软件商店', $entry . '/Admin/Marketsoft/index'),

    'Admin_Partnerorder' => array('第三方订单', $entry . '/Admin/Partnerorder/index'),
    'Admin_Fanfan_Topiccate' => array('商品专题分类', $entry . '/Admin/Fanfan_Topiccate/index'),
    'Admin_Fanfan_Topic' => array('商品专题管理', $entry . '/Admin/Fanfan_Topic/index'),
    'Admin_Fanfan_Topicgoods' => array('商品专题', $entry . '/Admin/Fanfan_Topicgoods/index'),

    'Admin_Resource' => array('精品应用', $entry . '/Admin/Resource/index'),

    'Admin_Brand_Category' => array('分类管理', $entry . '/Admin/Brand_Category/index'),
    'Admin_Brand_Brand' => array('品牌管理', $entry . '/Admin/Brand_Brand/index'),
    'Admin_Brand_Goods' => array('商品管理', $entry . '/Admin/Brand_Goods/index'),

    'Admin_Recharge_Price' => array('价格管理', $entry . '/Admin/Recharge_Price/index'),
    'Admin_Recharge_Orders' => array('订单管理', $entry . '/Admin/Recharge_Orders/index'),
    'Admin_Recharge_Operator' => array('运营商价格', $entry . '/Admin/Recharge_Operator/index'),
    'Admin_Recharge_Channel' => array('渠道管理', $entry . '/Admin/Recharge_Channel/index'),

    'Admin_Third_Goods' => array('第三方商品库', $entry . '/Admin/Third_Goods/index'),
    'Admin_Third_Shop' => array('第三方店铺库', $entry . '/Admin/Third_Shop/index'),
    'Admin_Third_Web' => array('第三方网页库', $entry . '/Admin/Third_Web/index'),

    'Admin_Channelname' => array('渠道管理', $entry . '/Admin/Channelname/index'),
    'Admin_Channelmodule' => array('模块管理', $entry . '/Admin/Channelmodule/index'),
    'Admin_Channelcode' => array('渠道号管理', $entry . '/Admin/Channelcode/index'),
    'Admin_Apilog' => array('api日志', $entry . '/Admin/Apilog/index'),
    'Admin_Log' => array('日志', $entry . '/Admin/Log/index'),
    'Admin_Statistic_Click' => array('点击统计', $entry . '/Admin/Statistic_Click/index'),
    'Admin_Hash' => array('hash管理', $entry . '/Admin/Hash/index'),

    //主题店二跳
    'Admin_Store_Category' => array('主题汇聚分类', $entry . '/Admin/Store_Category/index'),
    'Admin_Store_Category_H5' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=1'),
    'Admin_Store_Category_apk' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=2'),
    'Admin_Store_Category_channel' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=3'),
    'Admin_Store_Category_market' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=4'),
    'Admin_Store_Category_app' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=5'),
    'Admin_Store_Category_ios' => array('主题店分类', $entry . '/Admin/Store_Category/index?version_type=6'),
    'Admin_Store_Info_activity' => array('主题汇聚Banner', $entry . '/Admin/Store_Info/index?info_type=1'),
    'Admin_Store_Info_activity_H5' => array('H5活动', $entry . '/Admin/Store_Info/index?version_type=1&info_type=1'),
    'Admin_Store_Info_activity_apk' => array('预装活动', $entry . '/Admin/Store_Info/index?version_type=2&info_type=1'),
    'Admin_Store_Info_activity_channel' => array('渠道活动', $entry . '/Admin/Store_Info/index?version_type=3&info_type=1'),
    'Admin_Store_Info_activity_market' => array('穷购物活动', $entry . '/Admin/Store_Info/index?version_type=4&info_type=1'),
    'Admin_Store_Info_activity_app' => array('app活动', $entry . '/Admin/Store_Info/index?version_type=5&info_type=1'),
    'Admin_Store_Info_activity_ios' => array('ios活动', $entry . '/Admin/Store_Info/index?version_type=6&info_type=1'),
    'Admin_Store_Info_pingtai' => array('主题汇聚推荐', $entry . '/Admin/Store_Info/index?info_type=2'),
    'Admin_Store_Info_pingtai_H5' => array('H5平台', $entry . '/Admin/Store_Info/index?version_type=1&info_type=2'),
    'Admin_Store_Info_pingtai_apk' => array('预装平台', $entry . '/Admin/Store_Info/index?version_type=2&info_type=2'),
    'Admin_Store_Info_pingtai_channel' => array('渠道平台', $entry . '/Admin/Store_Info/index?version_type=3&info_type=2'),
    'Admin_Store_Info_pingtai_market' => array('穷购物平台', $entry . '/Admin/Store_Info/index?version_type=4&info_type=2'),
    'Admin_Store_Info_pingtai_app' => array('app平台', $entry . '/Admin/Store_Info/index?version_type=5&info_type=2'),
    'Admin_Store_Info_pingtai_ios' => array('ios平台', $entry . '/Admin/Store_Info/index?version_type=6&info_type=2'),
    'Admin_Store_Info_goodsCate' => array('主题汇聚精选', $entry . '/Admin/Store_Info/index?info_type=3'),
    'Admin_Store_Info_goodsCate_H5' => array('H5子分类', $entry . '/Admin/Store_Info/index?version_type=1&info_type=3'),
    'Admin_Store_Info_goodsCate_apk' => array('预装子分类', $entry . '/Admin/Store_Info/index?version_type=2&info_type=3'),
    'Admin_Store_Info_goodsCate_channel' => array('渠道子分类', $entry . '/Admin/Store_Info/index?version_type=3&info_type=3'),
    'Admin_Store_Info_goodsCate_market' => array('穷购物子分类', $entry . '/Admin/Store_Info/index?version_type=4&info_type=3'),
    'Admin_Store_Info_goodsCate_app' => array('app子分类', $entry . '/Admin/Store_Info/index?version_type=5&info_type=3'),
    'Admin_Store_Info_goodsCate_ios' => array('ios子分类', $entry . '/Admin/Store_Info/index?version_type=6&info_type=3'),
    'Admin_Story' => array('知物列表', $entry . '/Admin/Story/index'),
    'Admin_Storycategory' => array('栏目设置', $entry . '/Admin/Storycategory/index'),
    'Admin_Comment_Verify' => array('审核评论', $entry . '/Admin/Comment/verify'),
    'Admin_Comment' => array('评论管理', $entry . '/Admin/Comment/index'),
    'Admin_User_Author' => array('知物作者', $entry . '/Admin/User_Author/index'),
    'Admin_Config_Help' => array('购物教程配置', $entry . '/Admin/Config_Help/index'),
    'Admin_Config_History' => array('浏览记录配置', $entry . '/Admin/Config_History/index'),
    'Admin_Config_Navigation' => array('导航配置', $entry . '/Admin/Config_Navigation/index'),
    'Admin_Help' => array('购物教程管理', $entry . '/Admin/Help/index'),
// 	'Admin_Favorite' => array('收藏管理',$entry . '/Admin/Favorite/index'),
    'Admin_Favorite_Story' => array('知物收藏列表', $entry . '/Admin/Favorite_Story/index'),
    'Admin_Favorite_Goods' => array('商品收藏列表', $entry . '/Admin/Favorite_Goods/index'),
    'Admin_Favorite_Shop' => array('店铺收藏列表', $entry . '/Admin/Favorite_Shop/index'),
    'Admin_Favorite_Web' => array('网页收藏列表', $entry . '/Admin/Favorite_Web/index'),
    'Admin_Sensitive' => array('敏感词维护', $entry . '/Admin/Sensitive/index'),

    'Admin_User_Uid' => array('uid管理', $entry . '/Admin/User_Uid/index'),
    'Admin_User_Score' => array('积分管理', $entry . '/Admin/User_Score/index'),
    'Admin_User_Favorite' => array('总收藏列表', $entry . '/Admin/User_Favorite/index'),

    'Admin_User_Virtual' => array('虚拟用户', $entry . '/Admin/User_Virtual/index'),
    'Admin_Qa_Question' => array('问贴管理', $entry . '/Admin/Qa_Question/index'),
    'Admin_Qa_Answer' => array('回贴管理', $entry . '/Admin/Qa_Answer/index'),
    'Admin_Qa_Skey' => array('问答搜索', $entry . '/Admin/Qa_Skey/index'),
    'Admin_Qa_Stat' => array('问答统计', $entry . '/Admin/Qa_Stat/index'),

    'Admin_Cs_Faq_Question' => array('常见问题', $entry . '/Admin/Cs_Faq_Question/index'),
    'Admin_Cs_Faq_Category' => array('问题分类', $entry . '/Admin/Cs_Faq_Category/index'),
    'Admin_Cs_Feedback_Qa' => array('反馈管理', $entry . '/Admin/Cs_Feedback_Qa/index'),
    'Admin_Cs_Feedback_Quick' => array('快速回复', $entry . '/Admin/Cs_Feedback_Quick/index'),
    'Admin_Cs_Feedback_Link' => array('链接管理', $entry . '/Admin/Cs_Feedback_Link/index'),
    'Admin_Cs_Feedback_Kefu' => array('反馈客服', $entry . '/Admin/Cs_Feedback_Kefu/index'),
    'Admin_Cs_Feedback_User' => array('用户管理', $entry . '/Admin/Cs_Feedback_User/index'),
    'Admin_Cs_Feedback_Category' => array('反馈归类', $entry . '/Admin/Cs_Feedback_Category/index'),

    'Admin_Cut_Type' => array('分类管理', $entry . '/Admin/Cut_Type/index'),
    'Admin_Cut_Shops' => array('店铺管理', $entry . '/Admin/Cut_Shops/index'),
    'Admin_Cut_Goods' => array('砍价商品', $entry . '/Admin/Cut_Goods/index'),
    'Admin_Cut_Store' => array('商品库', $entry . '/Admin/Cut_Store/index'),
    'Admin_Cut_Log' => array('日志管理', $entry . '/Admin/Cut_Log/index'),
    'Admin_Cut_Game' => array('游戏日志', $entry . '/Admin/Cut_Game/index'),
    'Admin_Cut_User' => array('玩家日志', $entry . '/Admin/Cut_User/index'),
    'Admin_Cut_Order' => array('订单管理', $entry . '/Admin/Cut_Order/index'),
    'Admin_Cut_Gameorder' => array('游戏订单', $entry . '/Admin/Cut_Gameorder/index'),
    'Admin_Cut_Stat' => array('砍价统计', $entry . '/Admin/Cut_Stat/index'),

    'Admin_Ios_Msg' => array('消息管理', $entry . '/Admin/Ios_Msg/index'),
    'Admin_Ios_Token' => array('Token管理', $entry . '/Admin/Ios_Token/index'),

    'Admin_Activity_Recommend' => array('推荐抽奖', $entry . '/Admin/Activity_Recommend/index'),
);

return array($config, $view);
