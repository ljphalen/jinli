<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config = array(
	'test' => array(
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
	),
	'develop' => array(
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
			
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];
