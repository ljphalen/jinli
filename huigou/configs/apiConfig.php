<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
		/*淘宝客*/
		'taobao_top_key'=>'21156865',
		'taobao_top_secret'=>'d0af543f665ee650eaa9dac7f3c51223',
		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,
		'taobao_taobaoke_nick' => 'phwind8',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
		//收银台
		'gionee_pay_url' =>'http://t-pay.gionee.com',
		'gionee_coin_appid'=>'304984',
	),
	'product' => array(
		/*淘宝客*/
		'taobao_top_key'=>'21138672',
		'taobao_top_secret'=>'0d781c03997eb65d78392c99955fa2a1',
		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,
		'taobao_taobaoke_nick' => '17goucn',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',
		//sms短信接口
		'sms_url' => 'http://id.gionee.com:10808/sms/mt.do',
		//收银台
		'gionee_pay_url' =>'http://pay.gionee.com',
		'gionee_coin_appid'=>'101',
	),
	'develop' => array(
		/*淘宝客*/
		'taobao_top_key'=>'21156865',
		'taobao_top_secret'=>'d0af543f665ee650eaa9dac7f3c51223',
		'taobao_top_api_url'=>'http://gw.api.taobao.com/router/rest',
		'taobao_top_api_container_url'=>'http://container.open.taobao.com/container',
		'taobao_top_cache_expire_time'=>10,
		'taobao_taobaoke_nick' => '17goucn',
		'taobao_taobaoke_pid' => 'mm_32564804_0_0',
		'taobao_oauth_code_url'=> 'https://oauth.taobao.com/authorize',
		'taobao_oauth_token_url'=> 'https://oauth.taobao.com/token',
		'taobao_taobaoke_ttid' => '400000_21138672@jlg_Android_1.0',
		//sms短信接口
		'sms_url' => 'http://t-id.gionee.com:10808/sms/mt.do',
		//收银台
		'gionee_pay_url' =>'http://t-pay.gionee.com',
		'gionee_coin_appid'=>'304984',
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
