<?php
/**
 * 腾讯安全检查
 * @author jiazhu
 *
 */
class SafeTencentModel extends SafeModel
{
	protected $trueTableName = "apk_safe";
	/*
	 * 发送请求
	 */
	public function requestScan($apk_info)
	{
		//$scan_parm="{\"scanlist\":[{\"sid\":\"123\", \"url\":\"http://dl.test.com/test.apk\", \"md5\":\"3d41f29d762ec547bfa4b42f57f3dc7c\"}]}";
		if ($_SERVER['SERVER_ADDR'] == '211.157.185.89')
		{
			$apk_path = 'http://211.157.185.89/Data/Attachments/dev/apks/'.$apk_info['file_path'];
		}else
		{
			$apk_path = Helper("Apk")->get_url('apk').$apk_info['file_path'];
		}
		
		$scan_parm = array('scanlist' => array(array('sid' => $apk_info['id'],'url' => $apk_path, 'md5' => $apk_info['apk_md5'])));
		$scan_json = json_encode($scan_parm);
		$authkey = $this->generateSig($scan_json);
		$post = array(
			'authkey' => $authkey,
			'request' => $scan_json,
		);
		
		$api_url="http://open.scan.qq.com/api/scansoft?id=".C('SAFE_TENCENT_APP_ID');
		$request_res = curl_post($api_url,$post);
		$request_arr = json_decode($api_url,$post,$request_res,true);
		$deal_status = $request_arr['result'] == 'ok'?ApkSafeModel::DEAL_STATUS_SEND:ApkSafeModel::DEAL_STATUS_ERROR;
		return array('deal_status' => $deal_status, 'request_res' => $request_res);
	}
	
	/**
	 * 
	 * 
	 * {"get":{"site":"Tencent","_URL_":["SafeApi","callback","site","Tencent"]},"post":{"authkey":"59935c92b8bde2afd33ee4ffaf15e4e2","response":"{ &quot;result&quot;: &quot;ok&quot;, &quot;resultlist&quot;: [ { &quot;banner&quot;: &quot;0&quot;, &quot;boutiquerecommand&quot;: &quot;0&quot;, &quot;floatwindows&quot;: &quot;0&quot;, &quot;integralwall&quot;: &quot;0&quot;, &quot;md5&quot;: &quot;800862d43d800395615993f1d4b8bb1e&quot;, &quot;notifybar&quot;: &quot;0&quot;, &quot;pluginlist&quot;: [  ], &quot;safetype&quot;: &quot;1&quot;, &quot;sid&quot;: &quot;123&quot;, &quot;virusdesc&quot;: &quot;&quot;, &quot;virusname&quot;: &quot;&quot; } ] }"},"server":{"USER":"apache","HOME":"\/var\/www","FCGI_ROLE":"RESPONDER","QUERY_STRING":"s=\/SafeApi\/callback\/site\/Tencent","REQUEST_METHOD":"POST","CONTENT_TYPE":"application\/x-www-form-urlencoded","CONTENT_LENGTH":"319","SCRIPT_NAME":"\/index.php","REQUEST_URI":"\/SafeApi\/callback\/site\/Tencent","DOCUMENT_URI":"\/index.php","DOCUMENT_ROOT":"\/home\/gionee\/wwwroot","SERVER_PROTOCOL":"HTTP\/1.1","GATEWAY_INTERFACE":"CGI\/1.1","SERVER_SOFTWARE":"nginx\/1.4.1","REMOTE_ADDR":"183.60.9.217","REMOTE_PORT":"54980","SERVER_ADDR":"211.157.185.89","SERVER_PORT":"80","SERVER_NAME":"dev.gionee.com","REDIRECT_STATUS":"200","SCRIPT_FILENAME":"\/home\/gionee\/wwwroot\/\/index.php","HTTP_CONNECTION":"close","HTTP_CONTENT_LENGTH":"319","HTTP_CONTENT_TYPE":"application\/x-www-form-urlencoded","HTTP_HOST":"211.157.185.89","HTTP_USER_AGENT":"TC_Http","PHP_SELF":"\/index.php","REQUEST_TIME_FLOAT":1389769836.9008,"REQUEST_TIME":1389769836,"PATH_INFO":"\/SafeApi\/callback\/site\/Tencent"}}
	 */
	public function scanResult($sign,$data)
	{
		//处理data
		$data_arr = json_decode($data,true);
		$resultlist = $data_arr['resultlist'];
		if (is_array($resultlist))
		{
			foreach ($resultlist as $key=> $val)
			{
				$safe_status = $val['safetype'] ==1?ApkSafeModel::STATUS_SUC:ApkSafeModel::STATUS_FAIL;
				$apk_md5 = $val['md5'];
				$new_data = array('safe_status' => $safe_status,'response_res' => $val);
				$this->dealScan($apk_md5,$new_data,parent::SITE_TENCENT);
			
			}
		}		
		return true;
	}
	
/**
	 * 生成签名
	 * @param string $str
	 * @param string $secret
	 */
	public function generateSig($str) 
	{
		$authkey=md5($str.C('SAFE_TENCENT_APP_ID').C('SAFE_TENCENT_APP_KEY'));
		return $authkey;
	}
	
	
	//==========================文档返回内容暂时 ===============
	/*
	 * 数字类型，分别为0-未知， 1-安全软件，2-病毒软件，3-中风险软件，4-低风险软件
	 */ 
	public static  function safeType($val=null)
	{
		$arr = array(0 => '未知',
					   1 => '安全软件',
					   2 => '病毒软件',
					   3 => '中风险软件',
					   4 => '低风险软件',
					   );
		if ($val === null)
		{
			return $arr;
		}else
		{
			return $arr[$val];
		}
		
	}
	
	
	
	/**
	 * 状态位说明
	 * @param array $res
	 */
	public static function valTxt($res)
	{
		//-1-不确定，0-否，1-是
		$arr = array(
			 -1 => '不确定',
			 0 => '否',
			 1 => '是',
			 
		);
		return $arr[$res];
	}
	
	/**
	 * 内容展现
	 * @param json $json 返回json数据
	 */
	public static function show($json)
	{
		if (empty($json)) return false;
		$str = '';
		$data = json_decode($json,true);
		
		if (!empty($data))
		{
			$str .= '<b>文件md5:</b> '.$data['md5']."<br/>";
			$str .= '<b>安全类型:</b> '.self::safeType($data['safetype'])."<br/>";
			$str .= '<b>广告软件标记:</b> '.self::valTxt($data['banner'])."<br/>";
			$str .= '<b>病毒名称:</b> '.$data['virusname']."<br/>";
			$str .= '<b>病毒描述:</b> '.$data['virusdesc']."<br/>";
			
		}
		return $str;
	}
}