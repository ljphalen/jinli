<?php
/* 
 * $myconfig["cusome"]=array("1")
 */ 
$myconfig["test"]="";
//每页显示数量
define("PERPAGE",10);
define('PAGESIZE', 6);
$myconfig["PROJECT_TITLE"]="开发环境--磨基广告管理系统后台";
$myconfig["PROJECT_VERSION"]="2.0";
$myconfig["PROJECT_OFFER"]="相关操作人员";
$myconfig["PROJECT_DEVELOPER"]="Stephen.Feng  Int";
$myconfig["PRODUCT_MANAGER"]="Phil";
$myconfig["PROJECT_COPYRIGHT"]="CopyRight@2013";
//javascript,css版本号，每次更新代码的时候若有更改js,css，则需要更新该版本号。格式：年月日00~年月日99 (2014121601)
$myconfig["STATIC_VERSION"]="2014121601";
$myconfig["AD_TYPE_CATE"]=array(
                                "0"=>array("name"=>"抢占式",
                                           "subtype"=>array(
                                                            0=>"半屏",
                                                            1=>"全屏"
                                                            )
                                           ),
                                "1"=>array("name"=>"嵌入式",
                                           "subtype"=>array(
                                                            0=>"Banner横幅图片广告",
                                                            1=>"Banner横幅文字广告",
                                                            2=>"插页广告",
                                                            3=>"浮窗广告"
                                                            )
                                           ),
                                "2"=>array("name"=>"PUSH",
                                           "subtype"=>array(
                                                            0=>"消息",
                                                            1=>"APP",
                                                            2=>"快捷方式"
                                                            )
                                           ),
                                "3"=>array("name"=>"自定义",
                                           "subtype"=>array(
                                                            0=>"信息流",
                                                            )
                                           )
                                );
//导量限制字段
$myconfig["AD_LIMIT_STAT_ITEM"]=array("impressions"=>"展现","clicks"=>"点击","installs"=>"安装","first_starts"=>"首次启动","first_registers"=>"首次注册");
$myconfig["AD_SUBTYPE_NAME"]=array("nembd"=>0,"embd"=>1);
//频道接口
$myconfig["CHANNEL_URL"]="http://v1-feed.idreamsky.com/channel/getChannel?channel_id=%s";
//上传文件路径
define("UPLOAD_PATH",$config['SITE_PATH'].'misc/'.$config['TEMPLATE_PATH'].'/upload/');

//是否走Redis,外网需谨慎打开,切记
$myconfig["ADS_IS_RDIS"]=TRUE;
//从sdk获取游戏信息的url
$myconfig["GAME_INFO_URL"]='http://v1-feed.idreamsky.com/games/get_game_info?type=%d&value=%s';

//生成push文件的目录,此目录为可写
$myconfig["PUSH_PATH"]=$config['SITE_PATH'].'misc/push/';
$myconfig["PUSH_PATH_CONFIG"]=$myconfig["PUSH_PATH"].'config/';
$myconfig["PUSH_JSON_PATH"]=$myconfig["PUSH_PATH"].'appkeys/';

//生成push广告的过滤条件配置json的模版
$myconfig["PUSH_JSON_TEMPLATE"]='{
		"type":"%s",
		"params":"%s",
		"des":"%s",
		"values":"%s",
		"operate":"%s",
		"expected_value":"%s"
	}';
$myconfig["PUSH_CONFIG_JSON_TEMPLATE"]='{
        "app_list":[%s],
        "interval":%d
        }';
$myconfig["PUSH_INTERVAL"]=10000000;

$myconfig['report_channel_conf'] = array(
        '安卓市场' => 'AD0S0N00000',
        '通用渠道' => 'CURRENT00000',
        '360联运' => 'QH0S0N10000',
        '安智网' => 'AZ0S0N00000,1AZ0S0N10000',
        'T优亿市场' => 'YY0S0N00000',
        '桌面推广' => 'DS0S0N01000,DS0S0N10000,DS0S0N11000,DS0S0N12000,DS0S0N13000',
        '磨基网游' => 'MO0S0N00001,MO0S0N00002,MO0S0N00003,MO0S0N00004,MO0S0N00005,MO0S0N00006',
        '磨基单机' => 'MO0S0N00010,MO0S0N00011,MO0S0N00012,MO0S0N00013,MO0S0N00014',
        '交叉推广' => 'DS0S0N0010,DS0S0N0020,DS0S0N0040,DS0S0N0050,DS0S0N0090,DS0S0N0070',
        'N多网' => 'NDOSON0000,NDOSON0100',
        '百度移动' => 'BD0S0N00000,VF0S0N00000',
        'oppo' => 'OPOSON00000,OPOSON00001,OPOSON01000,OPOSON02000,OPOSON02001',
        '手游wap官网' => 'GW0S0N10000',
        '步步高网游cpt' => 'BG0S0N02000',
        '腾讯应用' => 'TX0S0N00000,TX0S0N01000',
        '仝氏网络网游cps' => 'TS0S0N10000',
        'T3533手机世界' => 'MG0S0N30000',
        'T掌上应用汇' => 'ZS0S0N00000',
        'T友盟' => 'TU0S0N00021',
        'T创天游科技' => 'TU0S0N00052',
        'T酷安网' => 'LI0S0N00020,RE0S0N00000,WG0S0N03000',
);
$myconfig['APPKEY_CONF']  = array(
        'C4E37EFB26F986D67D72' => '44ca08e61776c232ccb1',
        '8E69498B356D95CCB579' => 'ff15f96a336e5340a33c',
);

$myconfig["AD_POS_TYPE"]=array(
        "HALF"=>"插页广告",
        //"FULL"=>"全屏插页广告",
        //"BANNER_TEXT"=>"文字banner广告",
        "BANNER"=>"图片BANNER广告",
        'LIST'=>"推荐墙广告",
        'CUSTOM'=>"自定义",
    );
$myconfig["INCOME_RATE"]=0.8;#扣量比例
//广告位默认计价方式及单价
$myconfig["ACOUNTING_METHOD"]=array(
    //核算方式1:eCPM 2:cpc 3:下载完成数 4:安装数
    "HALF"=>array(
           "1",#核算方式
            "6"#单价
            ),
//    "FULL"=>array(
//            "2",
//            "5"
//            ),
    "BANNER"=>array(
            "2",
            "0.15"
            ),
    "LIST"=>array(
            "2",
            "0.15"
            ),
    "CUSTOM"=>array(
            "1",
            "6"
            ),
);

//渠道源地址配置
//$myconfig["CHANNEL_SOURCE_URL"] = "http://backend.idreamsky.com/";;
$myconfig["CHANNEL_SOURCE_URL"] = $config['APP_URL'];

//后台操作日志抓取网页快照模拟登录使用的用户名和密码
$myconfig['logger']['username']='hunter.fang';
$myconfig['logger']['password']='96e79218965eb72c92a549dd5a330112';

$myconfig["PUBKEY_NETGAME"]="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDczwo3vjIb+Xa9+xfOxzkODhAakss39GubdgPLyl+XPmCkwCc53htPsfWdV2EqHA9AK4T/gPcXPgxniaguNrGGaUn9s+jliMF88O3aR68rAC/CgxdvEkapAH34qtrRrV4TTSdiXIOQmwS9u+3O4wteHbZ2S/9vBBMIbRh3RIgDpQIDAQAB";
$myconfig["PUBKEY_ONEPACKAGE"]="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEarjGbIAtSasw18BskdzT6WFgiLr+oRj0WWLQJ4JLzuP+tj2gyjSMCYRnHomu2esKGkh0tzRzULNMm9aBhLG2ajxD0QEb7c7UJTKTNBR/IO3xp5DpEmWKgbjhz9XONy4hN3OObJfWCFvu5PA+lqIbNB0SFKz0B0gjll2IKwngrwIDAQAB";
$myconfig["AAPT_BIN"]="/bin/aapt";
