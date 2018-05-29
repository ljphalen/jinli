<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Gionee 帐号系统 GSP注册
 * @author fanch
 *
*/
class Api_Gionee_Account {
	
	/**
	 * 获取金立账号分配游戏大厅配置信息
	 * @return array
	 */
	public static function getAccountConfig(){
		$config = Common::getConfig('accountConfig');
		return $config;
	}
	
	/**
	 * gionee 接口domain
	 * @return string
	 */
	public static function getDomain(){
		return (ENV == 'product') ? 'https://id.gionee.com' : 'https://t-id.gionee.com:6443';
	}
	
	/**
	 * 获取服务器时间
	 * @return string
	 */
	public static function getServerTime(){
		$url = self::getDomain() . '/time.do';
		$result = Util_Http::post($url, '', array('Content-Type' => 'application/json'));
		return $result->headers;
	}
	
	/**
	 * 刷新/获取验证码
	 * @return vda,vid
	 */
	public static function refreshGVC(){
		$url = self::getDomain() . '/account/refreshgvc.do';
		$data = json_encode(array('vty'=> 'vtext'));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 获取短信(注册)
	 * @param string $tn
	 * @param string $s
	 * @return 
	 */
	public static function getSmsForRegister($tn, $s){
		$url = self::getDomain() . '/api/gsp/get_sms_for_register.do';
		$data = json_encode(array('tn'=> $tn, 's' => $s));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
		return json_decode($result->data, true);
	}

	/**
	 * Gionee 验证图形验证(注册)
	 * @param string $tn
	 * @param string $vid
	 * @param string $vtx
	 * @param string $vty
	 * @return
	 */
	public static function registerByGvc($tn, $vid, $vtx, $vty='vtext'){
		$url = self::getDomain() . '/api/gsp/register_by_gvc.do';
		$data = json_encode(array('tn'=> $tn, 'vid' => $vid, 'vtx' => $vtx, 'vty' => $vty));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 验证短信(注册)
	 * @param string $tn
	 * @param string $sc
	 * @return
	 */
	public static function registerBySmsCode($tn, $sc, $s){
		$url = self::getDomain() . '/api/gsp/register_by_smscode.do';
		$data = json_encode(array('tn'=> $tn, 'sc' => $sc, 's' => $s));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 设置密码(注册)
	 * @param string $s 验证短信 session
	 * @param string $p 设置的密码
 	 * @return Ambigous <boolean, unknown, stdClass>
	 */
	public static function registerByPass($s, $p){
		$url = self::getDomain() . '/api/gsp/register_by_pass.do';
		//增加注册接口中使用的appid
		$config = self::getAccountConfig();
		$a = $config['AppId'];
		$data = json_encode(array('s'=> $s, 'p' => $p, 'a' => a));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json'));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 身份认证(普通登录)
	 * @param string $tn
	 * @param string $p
	 * @param string $vid
	 * @param string $vtx
	 * @param string $vty
	 */
	public static function auth($tn, $p, $vid, $vtx, $vty){
		$url = self::getDomain() . '/api/gsp/auth_for_player.do';
		//增加注册接口中使用的appid
		$config = self::getAccountConfig();
		$a = $config['AppId'];
		$data = array('a'=>$a);
		$auth = "Basic " .base64_encode($tn . ':' . $p);
		if ($vid && $vtx && $vty)
			$data = array('a'=>$a, 'vid' => $vid, 'vtx' => $vtx, 'vty' => $vty);
		$data = json_encode($data);
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth ));
		return json_decode($result->data, true);
	}

	/**
	 * Gionee 身份认证(mac签名自动登录) 1.5.0 使用
	 * @param string $uuid
	 * @param string $ts
	 * @param string $noce
	 * @param string $mac
	 *
	 */
	public static function auth2($uuid,$ts,$nonce,$mac){
		$url = self::getDomain() . '/account/auth.do';
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
		$result = Util_Http::post2($url, '', array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 身份认证(mac签名自动登录)1.5.1 开始使用
	 * 1.5.1 增加 appid
	 * @param string $uuid
	 * @param string $ts
	 * @param string $noce
	 * @param string $mac
	 * 
	 */
	public static function auth3($uuid,$ts,$nonce,$mac){
		$url = self::getDomain() . '/api/gsp/autologon_for_player.do';
		//增加注册接口中使用的appid
		$config = self::getAccountConfig();
		$a = $config['AppId'];
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
		$data = json_encode(array('a' => $a));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 帐号修改密码
	 * @param string $tn
	 * @param string $p
	 * @param string $newp
	 * @param string $vid
	 * @param string $vtx
	 * @param string $vty
	 */
	public static function passmodify($tn, $p, $newp, $vid, $vtx, $vty){
		$url = self::getDomain() . '/account/pass/modify.do';
		$data = array('p' => $newp);
		$auth = "Basic " .base64_encode($tn . ':' . $p);
		if ($vid && $vtx && $vty)
			$data = array_merge($data, array('vid' => $vid, 'vtx' => $vtx, 'vty' => $vty));
		$data = json_encode($data);
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth ));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 密码加密算法(帐号系统)
	 * @param string $p
	 * @return string
	 */
	public static function encode_password($p){
	 	return strtoupper(sha1($p));
	}
}