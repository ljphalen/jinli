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
	 * Gionee 接口host
	 * @return string
	 */
	public static function getHost(){
		return (ENV == 'product') ? 'id.gionee.com' : 't-id.gionee.com';
	}
	
	/**
	 * Gionee 接口port
	 * @return string
	 */
	public static function getPort(){
		return (ENV == 'product') ? '443' : '6443';
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
	 * 客户端预注册-注册准备
	 * 1 本信令用于客户端发起注册准备
	 * @param string $carrier 运营商网络标识 	MCC+MNC
	 * @param string $secretDeviceId 加密后的imei号
	 * @param string $version 版本号信息, 格式：机型/ROM版本号/App名称/App版本号, 若某项信息没有，则以空字符串代替，分隔符需要保留。
	 * @return
	 */
	public static function pregByClient($carrier, $secretDeviceId, $version){
		$url = self::getDomain() . '/account/preg.do';
		$data = array('sdid'=>$secretDeviceId, 'ver'=> $version);
		$auth = 'SMS c="' . $carrier . '"';
		$data = json_encode($data);
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return Common::object2array($result);
	}
	
	/**
	 * 客户端预注册-验证注册结果
	 * 2 本信令用于客户端发起注册验证。
	 * @param $string $session 客户端预注册-请求分配的注册会话ID
	 */
	public function pregResult($session){
		$url = self::getDomain() . '/account/preg.do';
		$auth = 'SMS s="' .$session.'"';
		$config = self::getAccountConfig();
		$a = $config['AppId'];
		$data = json_encode(array('a' => $a));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return json_decode($result->data, true);
	}
	
	
	/**
	 * Gionee 获取短信(注册)
	 * @param string $tn
	 * @param string $s
	 * @return 
	 */
	public static function getSmsForRegister($tn, $s, $sdid, $pkgVersion, $ip){
		$url = self::getDomain() . '/api/gsp/get_sms_for_register.do';
		$config = self::getAccountConfig();
		$appId = $config['AppId'];
		$appKey = $config['AppKey'];
		$ts = time();
		$path = '/api/gsp/get_sms_for_register.do';
		$host = self::getHost();
		$port = self::getPort();
		$nonce = self::make_nonce();
		$text = $ts."\n".$nonce."\nPOST\n".$path."\n".$host."\n".$port."\n\n";
		$mac = self::hmacsha1($appKey, $text);
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $appId, $ts, $nonce, $mac);
		$data = json_encode(array('tn'=> $tn, 's' => $s, 'sdid'=>$sdid, 'ver'=>$pkgVersion, 'ipaddr'=>$ip));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
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
	public static function registerByGvc($tn, $vid, $vtx, $vty='vtext', $sdid, $pkgVersion, $ip){
		$url = self::getDomain() . '/api/gsp/register_by_gvc.do';
		$config = self::getAccountConfig();
		$appId = $config['AppId'];
		$appKey = $config['AppKey'];
		$ts = time();
		$path = '/api/gsp/register_by_gvc.do';
		$host = self::getHost();
		$port = self::getPort();
		$nonce = self::make_nonce();
		$text = $ts."\n".$nonce."\nPOST\n".$path."\n".$host."\n".$port."\n\n";
		$mac = self::hmacsha1($appKey, $text);
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $appId, $ts, $nonce, $mac);
		$data = json_encode(array('tn'=> $tn, 'vid' => $vid, 'vtx' => $vtx, 'vty' => $vty, 'sdid'=>$sdid, 'ver'=>$pkgVersion, 'ipaddr'=>$ip));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 验证短信(注册)
	 * @param string $tn
	 * @param string $sc
	 * @return
	 */
	public static function registerBySmsCode($tn, $sc, $s, $sdid, $pkgVersion, $ip){
		$url = self::getDomain() . '/api/gsp/register_by_smscode.do';
		$config = self::getAccountConfig();
		$appId = $config['AppId'];
		$appKey = $config['AppKey'];
		$ts = time();
		$path = '/api/gsp/register_by_smscode.do';
		$host = self::getHost();
		$port = self::getPort();
		$nonce = self::make_nonce();
		$text = $ts."\n".$nonce."\nPOST\n".$path."\n".$host."\n".$port."\n\n";
		$mac = self::hmacsha1($appKey, $text);
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $appId, $ts, $nonce, $mac);
		$data = json_encode(array('tn'=> $tn, 'sc' => $sc, 's' => $s, 'sdid'=>$sdid, 'ver'=>$pkgVersion, 'ipaddr'=>$ip));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
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
		$data = json_encode(array('s'=> $s, 'p' => $p, 'a' => $a));
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
	
	/**
	 * Gionee 用于请求服务器获取第三方联合登录凭证
	 * @param string $a 第三方应用appid
	 * @param string $uuid 金立账号中心用户唯一标识
	 * @param string $pk   金立账号中心用户passkey
	 */
	public static function assoc($a, $uuid, $pk){
		$url = self::getDomain() . '/account/ass/assoc.do';
		$ts = time();
		$path = '/account/ass/assoc.do';
		$host = self::getHost();
		$port = self::getPort();
		$nonce = self::make_nonce();
		$text = $ts."\n".$nonce."\nPOST\n".$path."\n".$host."\n".$port."\n\n";
		$mac = self::hmacsha1($pk, $text);
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $uuid, $ts, $nonce, $mac);
		$data = json_encode(array('a' => $a));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		return json_decode($result->data, true);
	}
	
	/**
	 * Gionee 通过帐号获取用户信息
	 * @param array $account 帐号数组数据
	 */
	private static function postToAccount($account){
		$url = self::getDomain() . '/api/ida/getaccount.do';
		$config = self::getAccountConfig();
		$apiId = $config['AppId'];
		$appKey = $config['AppKey'];
		$ts = time();
		$path = '/api/ida/getaccount.do';
		$host = self::getHost();
		$port = self::getPort();
		$nonce = self::make_nonce();
		$text = $ts."\n".$nonce."\nPOST\n".$path."\n".$host."\n".$port."\n\n";
		$mac = self::hmacsha1($appKey, $text);
		$auth = sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $apiId, $ts, $nonce, $mac);
		$data = json_encode(array('acc' => $account));
		$result = Util_Http::post($url, $data, array('Content-Type' => 'application/json', 'Authorization'=> $auth));
		//记录日志
		$path = Common::getConfig('siteConfig', 'logPath');
		$fileName = date('Y-m-d').'_post_account.log';
		$logData= '请求的url= '.$url.',帐号account='.json_encode($account).', 帐号返回的数据result='.json_encode($result);
		Common::WriteLogFile($path, $fileName, $logData);
		return json_decode($result->data, true);
	}
	
	/**
	 *通过单个uname获取UUID
	 */
	public static function getUuidByName($uname){
		$uuid ='';
		$account = array($uname);
		$result = self::postToAccount($account);
		foreach ($result['data'] as $val){
			$uuid = $val['+86'.$uname]['u'];
		}
		return $uuid;
		
	}
	
	/**
	 * Gionee 生成账号mac签名数据
	 * @param string $text   待加密的字符串
	 * 						$ts."\n".$nonce."\nPOST\n".$url."\n".$host."\n".$port."\n\n";
	 * @param string $pk     passkey 字符串;
	 */
	public static function generateMac($pk, $text){
		return base64_encode(self::hmacsha1($pk, $text));
	}
	
	/**
	 * Gionee 生成passkey
	 * @param string $uuid    
	 * @param string $passPlain  加过密的密码 
	 * @return string
	 */
	public static function createPasskey($uuid, $passPlain){
		$passKey = self::encode_password($uuid . ':' . $passPlain);
		return $passKey;
	}
	
	/**
	 * Gionee Mac加密算法
	 * 
	 * @param string $key
	 * @param string $data
	 * @return string
	 */
	public static function hmacsha1($key, $data) {
		$blocksize=64;
		$hashfunc='sha1';
		if (strlen($key)>$blocksize)
			$key=pack('H*', $hashfunc($key));
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack(
				'H*',$hashfunc(
						($key^$opad).pack(
								'H*',$hashfunc(
										($key^$ipad).$data
								)
						)
				)
		);
		return base64_encode($hmac);
	}
	
	/**
	 * noce 随机字符串默认长度为9位
	 * @param number $length
	 * @return string
	 */
	private static function make_nonce($length = 9) {   
		$str = md5(uniqid(mt_rand(), true));
		return substr($str,0,8);
	}
}