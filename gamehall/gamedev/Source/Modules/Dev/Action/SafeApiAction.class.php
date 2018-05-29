<?php
class SafeApiAction extends BaseAction
{
	function _initialize()
	{
		A("Common")->_initialize();
		
		// 帮助信息不需要登陆
		loadClient(array("Accounts"));

		$this->uid = AccountsClient::checkAuth ();
		$this->isLogin = true;
		$this->assign ( "uid", $this->uid );
		$this->assign ( "email", session('email') );
		
		//import('@.lib.Model.Safe');
	}
	
	public function index()
	{
		//dev.gionee.com/Data/Attachments/dev/apks/2014/01/08/1389192642338.apk
		//$str = '{"get":{"site":"baidu","tpl":"baidu","v":"2","sign":"3b2ab4544666277d3670fff07588071c","_URL_":["SafeApi","callback","site","baidu"]},"post":{"scanResult":"{&quot;resultList&quot;: [{&quot;safe_type&quot;: &quot;1&quot;, &quot;md5&quot;: &quot;af46154b874720a439538e1d2fe8b504&quot;, &quot;error_code&quot;: 0, &quot;error_info&quot;: &quot;no error&quot;}]}"},"server":{"USER":"apache","HOME":"\/var\/www","FCGI_ROLE":"RESPONDER","QUERY_STRING":"s=\/SafeApi\/callback\/site\/baidu&tpl=baidu&v=2&sign=3b2ab4544666277d3670fff07588071c","REQUEST_METHOD":"POST","CONTENT_TYPE":"application\/x-www-form-urlencoded","CONTENT_LENGTH":"193","SCRIPT_NAME":"\/index.php","REQUEST_URI":"\/SafeApi\/callback\/site\/baidu?tpl=baidu&v=2&sign=3b2ab4544666277d3670fff07588071c","DOCUMENT_URI":"\/index.php","DOCUMENT_ROOT":"\/home\/gionee\/wwwroot","SERVER_PROTOCOL":"HTTP\/1.1","GATEWAY_INTERFACE":"CGI\/1.1","SERVER_SOFTWARE":"nginx\/1.4.1","REMOTE_ADDR":"115.239.212.134","REMOTE_PORT":"61890","SERVER_ADDR":"211.157.185.89","SERVER_PORT":"80","SERVER_NAME":"dev.gionee.com","REDIRECT_STATUS":"200","SCRIPT_FILENAME":"\/home\/gionee\/wwwroot\/\/index.php","HTTP_HOST":"211.157.185.89","HTTP_ACCEPT_ENCODING":"identity","HTTP_CONTENT_LENGTH":"193","HTTP_CONTENT_TYPE":"application\/x-www-form-urlencoded","PHP_SELF":"\/index.php","REQUEST_TIME_FLOAT":1389679262.499,"REQUEST_TIME":1389679262,"PATH_INFO":"\/SafeApi\/callback\/site\/baidu"}}';

		$str = '{"get":{"site":"Tencent","_URL_":["SafeApi","callback","site","Tencent"]},"post":{"authkey":"59935c92b8bde2afd33ee4ffaf15e4e2","response":"{ &quot;result&quot;: &quot;ok&quot;, &quot;resultlist&quot;: [ { &quot;banner&quot;: &quot;0&quot;, &quot;boutiquerecommand&quot;: &quot;0&quot;, &quot;floatwindows&quot;: &quot;0&quot;, &quot;integralwall&quot;: &quot;0&quot;, &quot;md5&quot;: &quot;800862d43d800395615993f1d4b8bb1e&quot;, &quot;notifybar&quot;: &quot;0&quot;, &quot;pluginlist&quot;: [  ], &quot;safetype&quot;: &quot;1&quot;, &quot;sid&quot;: &quot;123&quot;, &quot;virusdesc&quot;: &quot;&quot;, &quot;virusname&quot;: &quot;&quot; } ] }"},"server":{"USER":"apache","HOME":"\/var\/www","FCGI_ROLE":"RESPONDER","QUERY_STRING":"s=\/SafeApi\/callback\/site\/Tencent","REQUEST_METHOD":"POST","CONTENT_TYPE":"application\/x-www-form-urlencoded","CONTENT_LENGTH":"319","SCRIPT_NAME":"\/index.php","REQUEST_URI":"\/SafeApi\/callback\/site\/Tencent","DOCUMENT_URI":"\/index.php","DOCUMENT_ROOT":"\/home\/gionee\/wwwroot","SERVER_PROTOCOL":"HTTP\/1.1","GATEWAY_INTERFACE":"CGI\/1.1","SERVER_SOFTWARE":"nginx\/1.4.1","REMOTE_ADDR":"183.60.9.217","REMOTE_PORT":"54980","SERVER_ADDR":"211.157.185.89","SERVER_PORT":"80","SERVER_NAME":"dev.gionee.com","REDIRECT_STATUS":"200","SCRIPT_FILENAME":"\/home\/gionee\/wwwroot\/\/index.php","HTTP_CONNECTION":"close","HTTP_CONTENT_LENGTH":"319","HTTP_CONTENT_TYPE":"application\/x-www-form-urlencoded","HTTP_HOST":"211.157.185.89","HTTP_USER_AGENT":"TC_Http","PHP_SELF":"\/index.php","REQUEST_TIME_FLOAT":1389769836.9008,"REQUEST_TIME":1389769836,"PATH_INFO":"\/SafeApi\/callback\/site\/Tencent"}}';
		$str = '{"get":{"site":"Tencent","_URL_":["SafeApi","callback","site","Tencent"]},"post":{"authkey":"4ca0f68b0c4875880086b1101586235d","response":"{ &quot;result&quot;: &quot;ok&quot;, &quot;resultlist&quot;: [ { &quot;banner&quot;: &quot;1&quot;, &quot;boutiquerecommand&quot;: &quot;0&quot;, &quot;floatwindows&quot;: &quot;0&quot;, &quot;integralwall&quot;: &quot;1&quot;, &quot;md5&quot;: &quot;c18d05cafebc1d9bb034ec575a6e08e0&quot;, &quot;notifybar&quot;: &quot;1&quot;, &quot;pluginlist&quot;: [ { &quot;plugindesc&quot;: &quot;\u542b\u767e\u5ea6(baidu)\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1\u3002&quot;, &quot;pluginname&quot;: &quot;\u767e\u5ea6(a.spot.baidu.a)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u767e\u5ea6\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1\u3002&quot;, &quot;pluginname&quot;: &quot;\u767e\u5ea6(a.banner.baidu.a)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u767e\u5ea6(baidu)\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1\u3002&quot;, &quot;pluginname&quot;: &quot;\u767e\u5ea6(a.spot.baidu)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u7b2c\u4e03\u4f20\u5a92(mobile7)\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u672c\u673a\u53f7\u7801,\u53ef\u80fd\u5bfc\u81f4\u60a8\u6536\u5230\u5783\u573e\u77ed\u4fe1\u6216\u8005\u964c\u751f\u7535\u8bdd\u7684\u9a9a\u6270;\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1;\u53ef\u8bfb\u53d6\u8f6f\u4ef6\u5217\u8868,\u53ef\u80fd\u5bfc\u81f4\u60a8\u7684\u624b\u673a\u88ab\u63a8\u9001\u5176\u4ed6\u8f6f\u4ef6\u3002&quot;, &quot;pluginname&quot;: &quot;\u7b2c\u4e03\u4f20\u5a92(a.notifyad.mobile7)&quot;, &quot;plugintype&quot;: &quot;1&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u7b2c\u4e03\u4f20\u5a92(mobile7)\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u672c\u673a\u53f7\u7801,\u53ef\u80fd\u5bfc\u81f4\u60a8\u6536\u5230\u5783\u573e\u77ed\u4fe1\u6216\u8005\u964c\u751f\u7535\u8bdd\u7684\u9a9a\u6270;\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1;\u53ef\u8bfb\u53d6\u8f6f\u4ef6\u5217\u8868,\u53ef\u80fd\u5bfc\u81f4\u60a8\u7684\u624b\u673a\u88ab\u63a8\u9001\u5176\u4ed6\u8f6f\u4ef6\u3002&quot;, &quot;pluginname&quot;: &quot;\u7b2c\u4e03\u4f20\u5a92(a.banner.mobile7)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u7b2c\u4e03\u4f20\u5a92(mobile7)\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u672c\u673a\u53f7\u7801,\u53ef\u80fd\u5bfc\u81f4\u60a8\u6536\u5230\u5783\u573e\u77ed\u4fe1\u6216\u8005\u964c\u751f\u7535\u8bdd\u7684\u9a9a\u6270;\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1;\u53ef\u8bfb\u53d6\u8f6f\u4ef6\u5217\u8868,\u53ef\u80fd\u5bfc\u81f4\u60a8\u7684\u624b\u673a\u88ab\u63a8\u9001\u5176\u4ed6\u8f6f\u4ef6\u3002&quot;, &quot;pluginname&quot;: &quot;\u7b2c\u4e03\u4f20\u5a92(a.spot.mobile7)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;\u542b\u767e\u5ea6\u5e7f\u544a\u63d2\u4ef6,\u53ef\u8bfb\u53d6\u8bbe\u5907\u4fe1\u606f,\u53ef\u80fd\u6cc4\u9732\u60a8\u7684\u4e2a\u4eba\u9690\u79c1\u3002&quot;, &quot;pluginname&quot;: &quot;\u767e\u5ea6(a.banner.baidu)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;&quot;, &quot;pluginname&quot;: &quot;a.adwall.mobile7&quot;, &quot;plugintype&quot;: &quot;2&quot; } ], &quot;safetype&quot;: &quot;1&quot;, &quot;sid&quot;: &quot;29&quot;, &quot;virusdesc&quot;: &quot;&quot;, &quot;virusname&quot;: &quot;&quot; } ] }"},"server":{"USER":"apache","HOME":"\/var\/www","FCGI_ROLE":"RESPONDER","QUERY_STRING":"s=\/SafeApi\/callback\/site\/Tencent","REQUEST_METHOD":"POST","CONTENT_TYPE":"application\/x-www-form-urlencoded","CONTENT_LENGTH":"2033","SCRIPT_NAME":"\/index.php","REQUEST_URI":"\/SafeApi\/callback\/site\/Tencent","DOCUMENT_URI":"\/index.php","DOCUMENT_ROOT":"\/home\/gionee\/wwwroot","SERVER_PROTOCOL":"HTTP\/1.1","GATEWAY_INTERFACE":"CGI\/1.1","SERVER_SOFTWARE":"nginx\/1.4.1","REMOTE_ADDR":"183.60.9.218","REMOTE_PORT":"36262","SERVER_ADDR":"211.157.185.89","SERVER_PORT":"80","SERVER_NAME":"dev.gionee.com","REDIRECT_STATUS":"200","SCRIPT_FILENAME":"\/home\/gionee\/wwwroot\/\/index.php","HTTP_CONNECTION":"close","HTTP_CONTENT_LENGTH":"2033","HTTP_CONTENT_TYPE":"application\/x-www-form-urlencoded","HTTP_HOST":"211.157.185.89","HTTP_USER_AGENT":"TC_Http","PHP_SELF":"\/index.php","REQUEST_TIME_FLOAT":1389924725.3051,"REQUEST_TIME":1389924725,"PATH_INFO":"\/SafeApi\/callback\/site\/Tencent"}}';
		$str = '{"get":{"site":"baidu","tpl":"baidu","v":"2","sign":"41003d5b4560548f6db4d662fd573ffb","_URL_":["SafeApi","callback","site","baidu"]},"post":{"scanResult":"{&quot;resultList&quot;: [{&quot;safe_type&quot;: &quot;1&quot;, &quot;error_info&quot;: &quot;no error&quot;, &quot;error_code&quot;: 0, &quot;md5&quot;: &quot;c18d05cafebc1d9bb034ec575a6e08e0&quot;}]}"},"server":{"USER":"apache","HOME":"\/var\/www","FCGI_ROLE":"RESPONDER","QUERY_STRING":"s=\/SafeApi\/callback\/site\/baidu&tpl=baidu&v=2&sign=41003d5b4560548f6db4d662fd573ffb","REQUEST_METHOD":"POST","CONTENT_TYPE":"application\/x-www-form-urlencoded","CONTENT_LENGTH":"193","SCRIPT_NAME":"\/index.php","REQUEST_URI":"\/SafeApi\/callback\/site\/baidu?tpl=baidu&v=2&sign=41003d5b4560548f6db4d662fd573ffb","DOCUMENT_URI":"\/index.php","DOCUMENT_ROOT":"\/home\/gionee\/wwwroot","SERVER_PROTOCOL":"HTTP\/1.1","GATEWAY_INTERFACE":"CGI\/1.1","SERVER_SOFTWARE":"nginx\/1.4.1","REMOTE_ADDR":"115.239.212.138","REMOTE_PORT":"10898","SERVER_ADDR":"211.157.185.89","SERVER_PORT":"80","SERVER_NAME":"dev.gionee.com","REDIRECT_STATUS":"200","SCRIPT_FILENAME":"\/home\/gionee\/wwwroot\/\/index.php","HTTP_HOST":"211.157.185.89","HTTP_ACCEPT_ENCODING":"identity","HTTP_CONTENT_LENGTH":"193","HTTP_CONTENT_TYPE":"application\/x-www-form-urlencoded","PHP_SELF":"\/index.php","REQUEST_TIME_FLOAT":1390202075.3533,"REQUEST_TIME":1390202075,"PATH_INFO":"\/SafeApi\/callback\/site\/baidu"},"res":true}';
		var_dump(json_decode($str,true));
		
	}
	
	public function scanAll()
	{
		$id = $_GET['id'];
		$res = D('Safe')->scanAll($id);
		var_dump($res);
	}
	
	public function rescan()
	{
		$id = $_GET['id'];
		$res = D('SafeBaidu')->rescan($id);
		var_dump($res);
	}
	
	/*
	 * 第三方回调接口
	 */
	function callback()
	{
		$site = ucfirst($this->_get('site'));
		$log_data = array('get' => $_GET,
			'post' => $_POST,
			'server' =>$_SERVER,
			'put' =>file_get_contents("php://input"),
		);
		F('API_before_'.$site.'_'.time(),$log_data);
		
		if ($site == SafeModel::SITE_BAIDU)
		{
			$sign  = $this->_get('sign');
			$data = htmlspecialchars_decode($_POST['scanResult']);
		
			$d_baidu = D('SafeBaidu');
			$sign_data = array(
				'tpl' => $_GET['tpl'],
				'v' => $_GET['v'],
				'scanResult' => $data,
			);
			
			//验证签名
			$local_sign = $d_baidu->generateSig($sign_data, C('SAFE_BAIDU_SECURITY'));
			if ($local_sign != $sign)
			{
				echo json_encode(array('error_code'=>-1,'data' => 'sign error'));
				//exit;
			}
			$res = $d_baidu->scanResult($sign,$data);
			if ($res)
			{
				echo json_encode(array('error_code'=>0,'data' => 'suc'));
			}else 
			{
				echo json_encode(array('error_code'=>-1,'data' => 'fail'));
			}
		}elseif ($site == SafeModel::SITE_TENCENT)
		{
			$sign = $this->_post('authkey');
			$data = urldecode($_POST['response']);
			
			$d_tencent = D('SafeTencent');
			
			//$sign = '4ca0f68b0c4875880086b1101586235d';
			//$data = '{ &quot;result&quot;: &quot;ok&quot;, &quot;resultlist&quot;: [ { &quot;banner&quot;: &quot;1&quot;, &quot;boutiquerecommand&quot;: &quot;0&quot;, &quot;floatwindows&quot;: &quot;0&quot;, &quot;integralwall&quot;: &quot;1&quot;, &quot;md5&quot;: &quot;c18d05cafebc1d9bb034ec575a6e08e0&quot;, &quot;notifybar&quot;: &quot;1&quot;, &quot;pluginlist&quot;: [ { &quot;plugindesc&quot;: &quot;含百度(baidu)广告插件,可读取设备信息,可能泄露您的个人隐私。&quot;, &quot;pluginname&quot;: &quot;百度(a.spot.baidu.a)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;含百度广告插件,可读取设备信息,可能泄露您的个人隐私。&quot;, &quot;pluginname&quot;: &quot;百度(a.banner.baidu.a)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;含百度(baidu)广告插件,可读取设备信息,可能泄露您的个人隐私。&quot;, &quot;pluginname&quot;: &quot;百度(a.spot.baidu)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;含第七传媒(mobile7)广告插件,可读取本机号码,可能导致您收到垃圾短信或者陌生电话的骚扰;可读取设备信息,可能泄露您的个人隐私;可读取软件列表,可能导致您的手机被推送其他软件。&quot;, &quot;pluginname&quot;: &quot;第七传媒(a.notifyad.mobile7)&quot;, &quot;plugintype&quot;: &quot;1&quot; }, { &quot;plugindesc&quot;: &quot;含第七传媒(mobile7)广告插件,可读取本机号码,可能导致您收到垃圾短信或者陌生电话的骚扰;可读取设备信息,可能泄露您的个人隐私;可读取软件列表,可能导致您的手机被推送其他软件。&quot;, &quot;pluginname&quot;: &quot;第七传媒(a.banner.mobile7)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;含第七传媒(mobile7)广告插件,可读取本机号码,可能导致您收到垃圾短信或者陌生电话的骚扰;可读取设备信息,可能泄露您的个人隐私;可读取软件列表,可能导致您的手机被推送其他软件。&quot;, &quot;pluginname&quot;: &quot;第七传媒(a.spot.mobile7)&quot;, &quot;plugintype&quot;: &quot;0&quot; }, { &quot;plugindesc&quot;: &quot;含百度广告插件,可读取设备信息,可能泄露您的个人隐私。&quot;, &quot;pluginname&quot;: &quot;百度(a.banner.baidu)&quot;, &quot;plugintype&quot;: &quot;3&quot; }, { &quot;plugindesc&quot;: &quot;&quot;, &quot;pluginname&quot;: &quot;a.adwall.mobile7&quot;, &quot;plugintype&quot;: &quot;2&quot; } ], &quot;safetype&quot;: &quot;1&quot;, &quot;sid&quot;: &quot;29&quot;, &quot;virusdesc&quot;: &quot;&quot;, &quot;virusname&quot;: &quot;&quot; } ] }';
			$data = htmlspecialchars_decode($data);
			//验证签名
			$local_sign = $d_tencent->generateSig($data);
			if ($local_sign != $sign)
			{
				//var_dump($local_sign,$sign);
				echo json_encode(array('result'=>-1,'data' => 'sign error'));
				//exit;
			}
			
			$res = $d_tencent->scanResult($sign,$data);
			if ($res)
			{
				echo json_encode(array('result'=>'ok','reason' => 'suc'));
			}else 
			{
				echo json_encode(array('result'=>'error','data' => 'fail'));
			}
		}elseif ($site == SafeModel::SITE_QIHU)
		{
			$data = file_get_contents("php://input");
			$data = simplexml_load_string($data);
			$data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$data = json_decode($data, true);
			
			//{"retcode":"0","file":{"state":"1","md5":"0b2da20379fb2f638cc110436d596b6b","type":"apk","sign":"安全","desc":{},
			//"safe":"插屏广告/普通广告","pms":{},"prop":{},
			//"url":"http://s.game.3gtest.gionee.com/Attachments/dev/apks/2014/11/19/1416365378321.apk"}}
			if($data["retcode"] > 0)
				exit(200);
			else{
				$d_qihu = D('SafeQihu');
				$res = $d_qihu->scanResult($sign, (array)$data["file"]);
			}
		}
		
		$log_data = array('get' => $_GET,
			'post' => $_POST,
			'server' =>$_SERVER,
			'res' => $res,
		);
		F('API'.$site.'_'.time(),$log_data);
		exit();
	}
	
}
