<?php
return array(
	//AppKey 接口地址，测试地址
	"GSP_PAY_API"				=>"https://test3.gionee.com/pay-merchant",
	"GSP_PAY_AGENT_ID"			=>"70E62BD218B44EC782D3D17A001B27C8",	

	/*
	 * 定义安全测试接口KEY
	 */

	'SAFE_BAIDU_TPL'			=>'gionee_test',
	'SAFE_BAIDU_SECURITY'		=>'b701b3525bcc274d4eae80d181b85f2664096e4f', 

	'SAFE_TENCENT_APP_ID'		=>'jinli_Amigo',
	'SAFE_TENCENT_APP_KEY'		=>'CDnBgqRkTKZFg7OK',

	'SMS_API_URL'				=>'http://t-id.gionee.com:10808/sms/mt.do',

	//应用上下线通知地址
	'APP_ONLINE_NOTYFI_URL'		=>'http://game.3gtest.gionee.com/api/dev/on',
	'APP_OFFLINE_NOTYFI_URL'	=>'http://game.3gtest.gionee.com/api/dev/off',

	//APPID 转变为 GAMEID 接口
	'APP_APPID_TO_GAMEID_URL' => 'http://game.3gtest.gionee.com/Api/Dev/convert',
	
	//获得应用统计接口
	'GAME_GSP_URL' => 'http://bi.gionee.com/gameGSPThird_GN.action',

	//运营后台数据同步接口
	'API_URL_GAME_CATE'			=>"http://game.3gtest.gionee.com/api/game/attribute?sid=1",
	'API_URL_FEE_TYPE'			=>"http://game.3gtest.gionee.com/api/game/attribute?sid=3",
	'API_URL_RESO'				=>"http://game.3gtest.gionee.com/api/game/attribute?sid=4",
	'API_URL_LABEL'				=>"http://game.3gtest.gionee.com/api/game/attribute?sid=8",
	'API_URL_LABEL_CHILD'		=>"http://game.3gtest.gionee.com/api/game/label?lid=",
		
);