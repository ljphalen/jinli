<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		/*淘宝客*/
		/* 'taobao_top_key'=>'21138672',
		'taobao_top_secret'=>'0d781c03997eb65d78392c99955fa2a1',
		'taobao_taobaoke_nick' => '17goucn',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',*/
			
		'taobao_top_key'=>'21622272',
		'taobao_top_secret'=>'8153fa24b617b0165740211f4965dd2f',
		'taobao_taobaoke_nick' => 'internetmobile',
		'taobao_taobaoke_pid' => 'mm_31749056_5906928_32786483',	 	
			
		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',
		
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
		'hotwifi_token'=>'8153fa24b617b0165740211f4965dd2f',
		//帐号
		'gionee_user_appid'=>'52D82E745D899249A9748D05D76A8A4E',
		'gionee_user_appkey'=>'F5D8B710FF6E8CA6F0056177866CC4D5',
			
		//金立帐号中心
		'gionee_user_url'=>'http://t-id.gionee.com',
			
		//收银台
		'gionee_pay_url' =>'http://test3.gionee.com/pay',
		'gionee_coin_appid'=>'0010B01FE62744188AEE99DC96CF9259',
			
		/*push接口*/
		'appid' => '9ee477ac1738437791485d6045ef1d94',
		'password' => 'bestBuy201304',
		'url' => 'http://push.gionee.com',
			
		/*欧飞手机充值接口*/
		'ofpay_userid'=>'A937303',
		'ofpay_userpws'=>'gionee123',
		'ofpay_url'=>'http://api2.ofpay.com/',
		'ofpay_keyStr'=>'OFCARD',
	
	    /* taobao search pid*/
	    'taobao_search_pid'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_type'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_h5'=>'http://ai.m.taobao.com/search.html?pid=mm_31749056_5906928_23022592&q=',
        'share_qq_url'=>'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey',
        'share_weibo_url'=>'http://v.t.sina.com.cn/share/share.php',
	
    	// spider
    	'spider_url'=>'http://gou.3gtest.gionee.com:8881',
//    	'spider_url'=>'127.0.0.1:8080',

	    //唯品会接口
    	'vip_app_id' => '9ee477ac1738437791485d6045ef1d94',
    	'vip_app_secret' => 'bestBuy201304',
    	'vip_api_url' => 'https://open.oapi.vip.com/',
	    'vip_api_state'=>'RK6cMoQ3JEm4f5mZ0XUh9UQG',
	
	    //美丽说
    	'meilishuo_na_id' => '10452',
    	'meilishuo_appkey' => '72eac4e3cebed8f9593d1e31ac28aeb0',

        //一号店
        'yhd_tracker_u' => '103526100',
        'yhd_secret_key' => 'vet2ozs0411itis7tmjpp0si1vzgg8i3',

	    'suning_server_url' => 'http://open.suning.com/api/http/sopRequest',
	    'suning_app_key' => '4717b4b6ec2e4a0a9f8d155f4302b196',
	    'suning_app_secret' => '4f99b8606a6545efef3d9f89c9ec037d',
        //国美电器
        'gome_app_key' => '33577620488',
        'gome_app_secret' => 'CEB33854DC0DC0C5BE856BB49BD08C7F',

	    //ios push
    	/* 'ios_push_ssl_url' => 'ssl://gateway.sandbox.push.apple.com:2195',
    	'ios_push_pass' => '123456',
	    'ios_push_certificate'=>'aps_development_catphp.pem', */
	
    	'ios_push_ssl_url' => 'ssl://gateway.push.apple.com:2195',
    	'ios_push_pass' => '123456',
    	'ios_push_certificate'=>'aps_production_catphp.pem',
    	'superJs'=>"http://assets.3gtest.gionee.com/apps/gou/assets/js/single/grap.js",
    	'superCss'=>"http://assets.3gtest.gionee.com/apps/gou/assets/css/grap.plugin.css",
	),
	'product' => array(
		/*淘宝客*/
		/* 'taobao_top_key'=>'21138672',
		'taobao_top_secret'=>'0d781c03997eb65d78392c99955fa2a1',
		'taobao_taobaoke_nick' => '17goucn',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',  */
			
		'taobao_top_key'=>'21622272',
		'taobao_top_secret'=>'8153fa24b617b0165740211f4965dd2f',
		'taobao_taobaoke_nick' => 'internetmobile',
		'taobao_taobaoke_pid' => 'mm_31749056_5906928_32786483',
			
		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,		
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',

		'internet_taobao_top_key'=>'21622272',
		'internet_taobao_top_secret'=>'8153fa24b617b0165740211f4965dd2f',
		'internet_taobao_taobaoke_nick' => 'internetmobile',
		'taobao_top_api_https_url'=>'https://eco.taobao.com/router/rest',
			
		//sms短信接口
		'sms_url' => 'http://id.gionee.com:10808/sms/mt.do',
		'hotwifi_token'=>'8153fa24b617b0165740211f4965dd2f',
			
		//帐号
		'gionee_user_appid'=>'52D82E745D899249A9748D05D76A8A4E',
		'gionee_user_appkey'=>'F5D8B710FF6E8CA6F0056177866CC4D5',
			
		//金立帐号
		'gionee_user_url'=>'http://id.gionee.com',
		//收银台
		'gionee_pay_url' =>'http://pay.gionee.com',
		'gionee_coin_appid'=>'88BC3A6CDFDC43F7AB05DBE7548ED4DB',
			
		/*push接口*/
		'appid' => '9ee477ac1738437791485d6045ef1d94',
		'password' => 'bestBuy201304',
		'url' => 'http://push.gionee.com',
			
		/*欧飞手机充值接口*/
		'ofpay_userid'=>'A937303',
		'ofpay_userpws'=>'gionee123',
		'ofpay_url'=>'http://api2.ofpay.com/',
		'ofpay_keyStr'=>'OFCARD',
	
	    /* taobao search pid*/
	    'taobao_search_pid'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_type'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_h5'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',

        'share_qq_url'=>'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey',
        'share_weibo_url'=>'http://v.t.sina.com.cn/share/share.php',
	
	    // spider
    	'spider_url'=>'http://10.162.88.159:8090',
	
	    //唯品会接口
    	'vip_app_id' => '9ee477ac1738437791485d6045ef1d94',
    	'vip_app_secret' => 'bestBuy201304',
    	'vip_api_url' => 'https://open.oapi.vip.com/',
	    'vip_api_state'=>'RK6cMoQ3JEm4f5mZ0XUh9UQG',
	
    	//美丽说
    	'meilishuo_na_id' => '10452',
    	'meilishuo_appkey' => '72eac4e3cebed8f9593d1e31ac28aeb0',

        'yhd_tracker_u' => '103526100',
        'yhd_secret_key' => 'vet2ozs0411itis7tmjpp0si1vzgg8i3',

	    'suning_server_url' => 'http://open.suning.com/api/http/sopRequest',
	    'suning_app_key' => '4717b4b6ec2e4a0a9f8d155f4302b196',
	    'suning_app_secret' => '4f99b8606a6545efef3d9f89c9ec037d',
		//国美电器
	    'gome_app_key' => '33577620488',
	    'gome_app_secret' => 'CEB33854DC0DC0C5BE856BB49BD08C7F',
    	//ios push
    	'ios_push_ssl_url' => 'ssl://gateway.push.apple.com:2195',
    	'ios_push_pass' => '123456',
	    'ios_push_certificate'=>'aps_production_catphp.pem',
	    'superJs'=>"http://assets.gionee.com/apps/gou/assets/js/single/grap.js",
    	'superCss'=>"http://assets.gionee.com/apps/gou/assets/css/grap.plugin.css",
	),
	'develop' => array(
		/*淘宝客*/
		/*'taobao_top_key'=>'21138672',
		'taobao_top_secret'=>'0d781c03997eb65d78392c99955fa2a1',
		'taobao_taobaoke_nick' => '17goucn',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',*/
			
		'taobao_top_key'=>'21622272',
		'taobao_top_secret'=>'8153fa24b617b0165740211f4965dd2f',
		'taobao_taobaoke_nick' => 'internetmobile',
		'taobao_taobaoke_pid' => 'mm_31749056_5906928_32786483',

		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,		
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',			
			
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
		'hotwifi_token'=>'8153fa24b617b0165740211f4965dd2f',
			
		//帐号
		'gionee_user_appid'=>'52D82E745D899249A9748D05D76A8A4E',
		'gionee_user_appkey'=>'F5D8B710FF6E8CA6F0056177866CC4D5',
			
		//金立帐号
		'gionee_user_url'=>'http://t-id.gionee.com',
		//收银台
		'gionee_pay_url' =>'http://test3.gionee.com/pay',
		'gionee_coin_appid'=>'0010B01FE62744188AEE99DC96CF9259',
			
		/*push接口*/
		'appid' => '9ee477ac1738437791485d6045ef1d94',
		'password' => 'bestBuy201304',
		'url' => 'http://push.gionee.com',
			
		/*欧飞手机充值接口*/
		'ofpay_userid'=>'A937303',
		'ofpay_userpws'=>'gionee123',
		'ofpay_url'=>'http://api2.ofpay.com/',
		'ofpay_keyStr'=>'OFCARD',
	
	    /* taobao search pid*/
	    'taobao_search_pid'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_type'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',
	    'taobao_search_pid_h5'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=',

         'share_qq_url'=>'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey',
         'share_weibo_url'=>'http://v.t.sina.com.cn/share/share.php',
	
    	// spider
    	'spider_url'=>'http://127.0.0.1:8090',
	
	    //唯品会接口
    	'vip_app_id' => '9ee477ac1738437791485d6045ef1d94',
    	'vip_app_secret' => 'bestBuy201304',
    	'vip_api_url' => 'https://open.oapi.vip.com/',
	    'vip_api_state'=>'RK6cMoQ3JEm4f5mZ0XUh9UQG',
	
    	//美丽说
    	'meilishuo_na_id' => '10452',
    	'meilishuo_appkey' => '72eac4e3cebed8f9593d1e31ac28aeb0',
        'yhd_tracker_u' => '103526100',
        'yhd_secret_key' => 'vet2ozs0411itis7tmjpp0si1vzgg8i3',
	    'gome_app_key' => '33577620488',
	    'gome_app_secret' => 'CEB33854DC0DC0C5BE856BB49BD08C7F',
	    'suning_server_url' => 'http://open.suning.com/api/http/sopRequest',
	    'suning_app_key' => '4717b4b6ec2e4a0a9f8d155f4302b196',
	    'suning_app_secret' => '4f99b8606a6545efef3d9f89c9ec037d',
	    //ios push
    	'ios_push_ssl_url' => 'ssl://gateway.sandbox.push.apple.com:2195',
    	'ios_push_pass' => '123456',
	    'ios_push_certificate'=>'aps_development_catphp.pem',
	
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
