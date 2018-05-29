<?php
/**
 * APiKey接口
 * @author yangshuhai
 * @example
 */
class ApiKeyHelper
{
	static $error = array(
		"unknown"	=> "unknown error",
		20000000	=> "OK",
		40000000	=> "请求参数格式错误",
		40001009	=> "账号申请失败",
		40001010	=> "无效的agent",
		40001011	=> "agent或apiKey无效",
		40001012	=> "重置appkey失败",
		50001000	=> "账号服务器内部错误",
		40002012	=> "App名称已存在",
		40002013	=> "App名称或AppId不存在",
		40002014	=> "无法找到应用信息",
		40002015	=> "密钥对未读取到",
		40002016	=> "激活过程发生异常",
		50000000	=> "服务器繁忙，请稍后再试",
	);
	
	/**
	 * 获取接口地址
	 * @param strting $act
	 */
	static function api($act="apply")
	{
		$host = C('GSP_PAY_API');
		$action = array(
			'app_apply'			=> "/agent/app/apply",
			'paykey_update'		=> "/agent/paykey/update",
			'notify_update'		=> "/agent/notify/update",
		);
		$act = strtolower($act);
		
		return $host . $action[$act];
	}
	
	/**
	 * 注册应用
	 * 用于游戏开发者中心向GSP开发者中心注册应用
		$data = array(
				"submit_time"		=> date("YmdHis"),
				"package_name"		=> "com.gionee.dev.test".mt_rand(1, time()),
				"app_name"			=> "金立游戏开发者中心测试环境应用, test" . date("Y-m-d H:i:s"),
				"type"				=> "单机",
				"channel"			=> "游戏大厅"
		);
	 */	
	static function app_apply($data)
	{
		$Url = self::api(__FUNCTION__);
		$result = self::query($Url, $data);
		return $result;
	}
	
	/**
	 * 更新商户私钥
	 * 本信令用于游戏开发者中心向GSP开发者中心更新商户支付私钥
		$data = array(
				"api_key"			=> "",
		);
	 */
	static function paykey_update($data)
	{
		$Url = self::api(__FUNCTION__);
		$result = self::query($Url, $data);
	
		return $result;
	}
	
	/**
	 * 更新notify通知地址
	 * 用于游戏开发者中心向GSP开发者中心更新商户notify地址
		$data = array(
				"api_key"			=> "",
				"notify_url"		=> ""
		);
	 */
	static function notify_update($data)
	{
		$Url = self::api(__FUNCTION__);
		$result = self::query($Url, $data);
	
		return $result;
	}
	
	/**
	 * 获取远程内容并格式化
	 * @param string $Url
	 * @param array  $query_data
	 */
	static function query($Url, $data)
	{
		$data["agent_id"] = C("GSP_PAY_AGENT_ID");
		$data = self::json_encode($data);

		$result = self::curl_download($Url, $data);
		if(APP_DEBUG)
		{
			Log::write(sprintf("APPKAY_API_CURL_URL : %s", $Url), Log::DEBUG);
			Log::write(sprintf("APPKAY_API_CURL_DATA : %s", $data), Log::DEBUG);
			Log::write(sprintf("APPKAY_API_CURL_RESULT : %s", $result), Log::DEBUG);
		}

		if(empty($result) || !$decode = @json_decode($result))
		{
			Log::write("URL fetch error: " . $Url, Log::EMERG);
			Log::write("URL fetch result: " . $result, Log::EMERG);
			return false;
		}
		
		return (array)$decode;
	}
	
	/**
	 * curl 获取远程链接的方法
	 * @param string $Url
	 * @param array  $query_data
	 */
	static function curl_download($Url, $query_data = array())
	{
		//https证书
		$cert = DATA_HOME.'/cert/cacert.pem';

		$post_data = is_array($query_data) ? http_build_query($query_data) : $query_data;
	
		if (!function_exists('curl_init')){
			Log::write("CURL IS NOT SUPPORT", Log::EMERG);
			return false;
		}
	
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		//分析证书是否存在
		if(!file_exists($cert))
		{
			//访问HTTPS时不验证证书
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}else{
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true); ;
			curl_setopt($ch,CURLOPT_CAINFO, $cert);
		}
	
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
	/**
	 * 不转义中文字符的 json 编码方法
	 * @param array $arr 待编码数组
	 * @return string
	 */
	static function json_encode($arr)
	{
		if(version_compare(PHP_VERSION, "5.4") >= 0)
			return json_encode($arr, JSON_UNESCAPED_UNICODE);

		foreach ($arr as $k=>$v)
			$str[] = sprintf('"%s":"%s"', $k, addslashes($v));
		
		$str = join(", ", $str);
		return sprintf("{%s}", $str);
	}
	
	/**
	 * 获取错误信息
	 * @param int $code
	 */
	static function getError($code)
	{
		if(isset(self::$error[$code]))
			return self::$error[$code];
		else
			return self::$error["unknown"];
	}
}