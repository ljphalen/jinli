<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Api_Gionee_Oauth {
	public static $_domain = 'https://t-id.gionee.com';
	
	public static function getLogoutUrl($callback) {
		list($url, $queryString)  = self::getQueryUrl('/user/logout', array('redirect_uri'=>$callback));
		return sprintf('%s?%s', $url, $queryString);
	}
	
	/***
	 * 用户登录，第一步request_token
	 * @param $callback
	 * return array or false
	 */
	public static function requestToken($callback) {
		$params = array('redirect_uri'=> $callback, 'response_type'=>'code');
		list($url, $queryString)  = self::getQueryUrl('/oauth/authorize', $params);
		return sprintf('%s?%s', $url, $queryString);
	}
	
	/***
	 * 用户登录，第二步access_token
	 * @param $auth_token
	 * @param $auth_token_secret
	 * return array or false
	 */
	public static function accessToken($code, $callback) {
		$params = array('code'=>$code, 'redirect_uri'=>$callback, 'grant_type'=>'authorization_code');
		list($url, $data) = self::getQueryUrl('/oauth/access_token', $params);
		return self::auisResult($url, $data, 'POST');
	}
	
	/**
	 * 验证用户登录是否成功
	 * @param array $params
	 * @return mixed
	 */
	public static function verify($params) {
		$url = Common::getConfig('apiConfig', 'gionee_user_url') . '/account/verify.do';
		return self::auisResult($url, json_encode($params), 'POST');
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return multitype:string Ambigous <string, NULL>
	 */
	protected static function getQueryUrl($uri, $params) {
		$appid = Common::getConfig('apiConfig', 'gionee_user_appid');
		$params = array_merge($params, array(
				'client_id'=>$appid,
				'state'=>self::_getUuid()
		));
		$queryString = http_build_query($params);
		$url = Common::getConfig('apiConfig', 'gionee_user_url') . $uri;
		return array($url, $queryString);
	}
	
	/**
	 * 返回 auis 请求结果
	 * @param string $interface
	 * @param array $param
	 */
	protected static function auisResult($query, $data, $method = 'POST') {
		if (strtoupper($method) == 'POST') {
			$headers = self::headers($query, $method);
			$result = Util_Http::post($query, $data, $headers);
		} else {
			$query = $query . '?' . $data;
			$result = Util_Http::get($query);
		}
// 		Common::log(array($query, $headers, $result), 'oauth.log');
		if ($result->state !== 200) {
			Common::log(array($query, $headers, $result), 'oauth.log');
			return false;
		}
		$data = json_decode($result->data, true);
		if ($data['r']) {
			Common::log(array($query, $headers, $data), 'oauth.log');
			return false;
		}
		return $data;
	}
	
	/***
	 *参数加密方法
	*
	*/
	public static function headers($query ,$method) {
		$appid = Common::getConfig('apiConfig', 'gionee_user_appid');
		$appkey = Common::getConfig('apiConfig', 'gionee_user_appkey');
		
		$time = Common::getTime();
		$uuid = self::_getUuid();
			
		$parseUrl = parse_url($query);
		!isset($parseUrl['port']) && $parseUrl['port'] = 80;
		$base_string = sprintf("%s\n%s\n%s\n%s\n%s\n%s\n\n", $time, $uuid, $method, $parseUrl['path'], $parseUrl['host'], $parseUrl['port']);
		$sign = base64_encode(hash_hmac('sha1', $base_string, $appkey, true));
		$authorization = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $appid, $time, $uuid, $sign);
		$headers = array('Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization'=>$authorization);
		return $headers;
	}
	
	/**
	 * 
	 * @param string $prefix
	 * @param string $adddate
	 * @return string
	 */
	protected static function _getUuid($prefix = '') {
		return substr(uniqid(mt_rand(10, 99)), 0, 8);
	}
}
