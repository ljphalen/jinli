<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class WeiXin_Service_Base
 */
class WeiXin_Service_Base extends WeiXin_Base
{

	/**
	 * 验证签名
	 * @param $signature
	 * @param $timestamp
	 * @param $nonce
	 * @return bool
	 */
	public static function checkSignature($signature, $timestamp, $nonce) {
		$tmpArr = array(WeiXin_Config::getToken(), $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if ($tmpStr == $signature) return true;
		return false;
	}
	
	/**
	 * 发送消息
	 * @param $data
	 * @return mixed|null
	 */
	public static function sendMsg($data) {
	    $params = array(
	                    "access_token"=>self::getToken(),
	    );
	    return self::request("/message/custom/send", $params, $data);
	}
	
	/**
	 * 获取关注用户openid
	 * @author yinjiayan
	 * @param string $nextOpenId
	 * @return boolean
	 */
	public static function getOpenidList($nextOpenId = '') {
	    $params = array(
	                    "access_token"=>self::getToken(),
	                    'next_openid'=>$nextOpenId
	    );
	    $resultJson = self::request("/user/get", $params);
	    $result = json_decode($resultJson, true);
	    if ($result['count'] > 0) {
	        return array($result['data']['openid'], $result['next_openid']);
	    }
	    return false;
	}

	public static function getGroupName($groupId) {
	    $redis = Common::getCache();
	    $groups = $redis->get(WeiXin_Config::GROUP_CACHE_KEY);
	    if (!$groups || !$groups[$groupId]) {
    	    $groups = self::getGroupByPort();
    	    $redis->set(WeiXin_Config::GROUP_CACHE_KEY, $groups, 24*3600);
	    }
	    return $groups[$groupId];
	}
	
	/**
	 * 获取用户组id
	 * @author yinjiayan
	 * @return mixed
	 */
	public static function getUserGroupId($openId) {
	    $data = array(
	                    'openid'=>$openId
	    );
	    $resultJson = self::requestByJsonParams("/groups/getid", json_encode($data));
	    $result = json_decode($resultJson, true);
	    return $result['groupid'];
	}
	
	private static function getGroupByPort() {
	    $params = array(
	                    "access_token"=>self::getToken(),
	    );
	    $resultJson = self::request("/groups/get", $params);
	    $result = json_decode($resultJson, true);
	    $groups = $result['groups'];
	    $groupsCache = array();
	    foreach ($groups as $group) {
	        $groupsCache[$group['id']] = $group['name'];
	    }
	    return $groupsCache;
	}

	/**获取业务token
	 * @return bool
	 */
	public static function getToken() {
		//如果已经存在直接返回
		$redis = Common::getCache();
		$rvalue = $redis->get(WeiXin_Config::ACCESSTOKEN_CACHE_KEY);
		if ($rvalue) return $rvalue;
		Common::log("getToken error.", "weixin.log");
		return self::accessToken();
	}

	/**
	 * 刷新业务token
	 * @return mixed
	 */
	public static function accessToken() {
	    $params = array(
	                    "grant_type"=>"client_credential",
	                    "appid"=> Common::getConfig('weixinConfig', 'appID'),
	                    "secret"=> Common::getConfig('weixinConfig', 'appSecret'),
	    );
	    $result = self::request("/token", $params);
	    $tokenData = json_decode($result, true);
	    Common::getCache()->set(WeiXin_Config::ACCESSTOKEN_CACHE_KEY, $tokenData['access_token'], $tokenData['expires_in']);
	    return $tokenData['access_token'];
	}
	
	/**
	 *
	 * http _request
	 * @param $uri
	 * @param $params
	 * @param null $data
	 * @param string $method
	 * @return mixed|null
	 */
	public static function request($uri, $params, $data = null, $method = "POST") {
		$query = http_build_query((array)$params);
		$url = sprintf("%s%s?%s", WeiXin_Config::API_BASE_URL, $uri, $query);
		Common::log($url, "weixin.log");
		Common::log($data, "weixin.log");
		$curl = new Util_Http_Curl($url);
		$curl->setData($data);
		$result = $curl->send($method);
		self::checkErrcode($result->errcode);
		Common::log($result, "weixin.log");
		return $result;
	}
	
	public static function requestByJsonParams($uri, $jsonParams = '', $params=array()) {
	    if (!$params) {
	    	$params = array("access_token"=>self::getToken());
	    }
	    $query = http_build_query($params);
	    $url = sprintf("%s%s?%s", WeiXin_Config::API_BASE_URL, $uri, $query);
	    $result = Util_Http::post($url, $jsonParams, array('Content-Type' => 'application/json'));
	    $result = $result->data;
	    self::checkErrcode($result->data->errcode);
	    return $result;
	}
	
	private static function checkErrcode($errcode) {
	    if ($errcode == 40001 || $errcode == 40014 || $errcode == 41001 || $errcode == 42001) {
	    	self::accessToken();
	    }
	}
}
