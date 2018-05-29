<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author lichanghua
 *
*/
class Api_Push_Msg {
    const APPOINT_IMEI = 4;
    const TOKEN_EXPIRE = 1800;
	
	public static function getPushConfig(){
		$config = Common::getConfig('gioneePushConfig');
		return $config;
	}
	
	public static function getPostResponse($parmes, $url, $headers = array('User-Agent' => 'userAgent')){
	    if(!$parmes){
	        return  false;
	    }
	    $response = Util_Http::post($url, $parmes, $headers);
	    print_r($response);
	    print_r(json_decode($response->data,true));
	    return json_decode($response->data,true);
	}
	
	public static function getApplicationId(){
	    $config = self::getPushConfig();
	    return $config['applicationID'];
	}
	
	public static function getToken(){
	    $data = array();
	    $config = self::getPushConfig();
	    $data['applicationID']= $config['applicationID'];
	    $data['passwd']= $config['passwd'];
	    $url = $config['pushTokenUrl'];
	    $parmes =  sprintf('applicationID=%s&passwd=%s', $data['applicationID'], $data['passwd']);
	    return self::getPostResponse($parmes, $url);
	}
	
	public static function pushMsg($msg) {
	    $config = self::getPushConfig();
	    $url = $config['pushServiceUrl'];
	    $json_data = json_encode($msg);
	    $result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
	    print_r(json_decode($result->data,true));
	    return json_decode($result->data,true);
	}
	
	public static function getUserInfo($uuid){
	    return Account_Service_User::getUser(array('uuid'=>$uuid));
	}
	
	public static function decryptImei($imei){
	    $imeiDecrypt = Util_Imei::decryptImei($imei);
	    return  strtoupper($imeiDecrypt);
	}
	
	public static function assembleRequestData($msg){
	    $cache = Cache_Factory::getCache();
	    $tokenKey = Util_CacheKey::PUSH_TOKEN;
	    $expiredKey = Util_CacheKey::PUSH_EXPIRED;
	    $token = $cache->get($tokenKey);
	    $expired = $cache->get($expiredKey);
	
	    if($token === false || $expired === false || $expired <= Common::getTime()) {
	        $tokenResult = self::getToken();
	        $cache->set($tokenKey, $tokenResult['authToken'], self::TOKEN_EXPIRE);
	        $cache->set($expiredKey, $tokenResult['expired'], self::TOKEN_EXPIRE);
	        $token = $tokenResult['authToken'];
	        $expired = $tokenResult['expired'];
	    }
	
	    $userInfo = self::getUserInfo($msg['sendInput']);
	    $imei = $userInfo['imei'];
	    $uname = $userInfo['uname'];
	    $decryptImei = self::decryptImei($imei);
	    return self::assembleMsgSystem($msg, $decryptImei, $token, $uname);
	}
	
	public static function assembleMsgSystem($msg, $decryptImei, $token, $uname){
	    $sendInfo = array();
	    if(!$decryptImei) {
	        return $sendInfo;
	    }
	    $item = Game_Service_Msg::genSysMsgOutput($msg, $msg['sendInput'],$uname);
	    $time = Common::getTime();
	    $startTime = strtotime("+10 minute", $time);
	    $msgData = Common::getOrderOutput('gamehall.noti.msg.system', $item);
	    $msgContent = array(
	            'type' => 'notification',
	            'save' => 'true',
	            'expired' => $startTime,
	            'p' => 'gn.com.android.gamehall',
	            'msgid' =>$msg['id'],
	            'msg' => urlencode(json_encode($msgData)),
	            'rids' => '',
	            'imeis' => array($decryptImei),
	            'tags' => 'pushMsg',
	            'i' =>''
	    );
	
	    $sendInfo = array(
	            'appid' =>  self::getApplicationId(),
	            'token' =>  $token,
	            'pt' => self::APPOINT_IMEI,
	            'msgs' => array($msgContent),
	    );
	    return $sendInfo;
	}
	
}