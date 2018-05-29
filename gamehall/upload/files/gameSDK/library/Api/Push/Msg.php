<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author lichanghua
 *
*/
class Api_Push_Msg {
    const APPOINT_IMEI = 4;
    const TARGET_ALL_ACOUNT = 1;
    const TOKEN_EXPIRE = 1800;
    const TOKEN_INVALID_CODE = 40008;
    const HASH_EXPIRE = 2592000;
    const NOTIFICATION_MSG_SYSTEM = 'gamehall.noti.msg.system';
    const NOTIFICATION_MSG_CUSTOM = 'gamehall.noti.msg.custom';
    const NOTIFICATION_MSG_PUSH  = 'gamehall.noti.msg.push';
    const GAME_PACKAGE  = 'gn.com.android.gamehall';
    const MSG_SYSTEM_MIN_VERSION  = '1.5.8.a';
    const MSG_CUSTOM_MIN_VERSION  = '1.5.9.a';
    const MSG_PUSH_MIN_VERSION  = '1.5.9.a';
	
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
	
	public static function getPushToken(){
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
	    return array($token, $expired);
	}
	
	
	public static function againGetPushToken(){
	    $cache = Cache_Factory::getCache();
	    $tokenKey = Util_CacheKey::PUSH_TOKEN;
	    $expiredKey = Util_CacheKey::PUSH_EXPIRED;
	    $tokenResult = self::getToken();
	    $cache->set($tokenKey, $tokenResult['authToken'], self::TOKEN_EXPIRE);
	    $cache->set($expiredKey, $tokenResult['expired'], self::TOKEN_EXPIRE);
	    return array($token, $expired);
	}
	
	public static function assembleRequestData($msg){
	    list($token, $expired) = self::getPushToken();
	    list($decryptImei, $msgData, $gioneePushType) =  self::assembleAppointAcountInfo($msg);
	    if(!$decryptImei) {
	        return array();
	    }

	    return self::getSendMsg($msg, $token, $msgData, $gioneePushType, $decryptImei);
	}
	
	public static function getOperatingAppointAcountMsg($msg){
	    list($token, $expired) = self::getPushToken();
	    list($decryptImei, $msgData, $gioneePushType) =  self::assembleAppointAcountInfo($msg);	
	         
	    if(!$decryptImei) {
	        return array();
	    }
	    
	    return self::getSendMsg($msg, $token, $msgData, $gioneePushType, $decryptImei);
	}
	
	public static function getOperatingAllAcountMsg($msg, $isPush){
	    list($token, $expired) = self::getPushToken();
	    list($msgData, $gioneePushType) = self::assembleAllAcountInfo($msg, $isPush);
	    return self::getSendMsg($msg, $token, $msgData, $gioneePushType);
	}
	
	public static function assembleAllAcountInfo($msg, $isPush = false){
	   	list($notification,$item, $version) = self::assembleMsgItem($msg, $isPush, false);
	    $msgData = Common::getOrderOutput($notification, $item, $version);
	    $gioneePushType =  self::TARGET_ALL_ACOUNT;
	    return array($msgData, $gioneePushType);
	}
	
	public static function assembleAppointAcountInfo($msg){
	    $userInfo = self::getUserInfo($msg['sendInput']);
	    $imei = $userInfo['imei'];
	    $uname = $userInfo['uname'];
	    $decryptImei = self::decryptImei($imei);
	    
	    if(!$decryptImei) {
	        return array();
	    }
	    
	    list($notification,$item, $version) = self::assembleMsgItem($msg, false, $uname);
	    $msgData = Common::getOrderOutput($notification, $item, $version);
	    $gioneePushType =  self::APPOINT_IMEI;
	    $decryptImei = array($decryptImei);
	    
	    return array($decryptImei, $msgData, $gioneePushType);
	}
	
	public static function assembleMsgItem($msg, $isPush = false, $uname= false){
	    
	    if($isPush){
	        $item = Game_Service_Msg::genMsgOutput($msg, $isPush);
	        $notification = self::NOTIFICATION_MSG_PUSH;
	        $version = self::MSG_PUSH_MIN_VERSION;
	    } else if($msg['top_type'] == Game_Service_Msg::YUNYING_MSG){
	        $item = Game_Service_Msg::genMsgOutput($msg, $isPush);
	        $notification = self::NOTIFICATION_MSG_CUSTOM;
	        $version = self::MSG_CUSTOM_MIN_VERSION;
	    } else if($msg['top_type'] == Game_Service_Msg::SYS_MSG){
	        $item = Game_Service_Msg::genSysMsgOutput($msg, $msg['sendInput'],$uname);
	        $notification = self::NOTIFICATION_MSG_SYSTEM;
	        $version = self::MSG_SYSTEM_MIN_VERSION;
	    }
	    
	    if($uname){
	        $item['uname'] = $uname;
	    }
	    
	    $item['param']['hashTime'] = self::getHashTime();
	    if($msg['top_type'] == Game_Service_Msg::SYS_MSG){
	        $item['param']['hashTime'] = 0;
	    }
	    	    
	    return array($notification, $item, $version);
	}
	
	public static function getSendMsg($msg, $token, $msgData, $gioneePushType, $decryptImei = array()){
	    $time = Common::getTime();
	    $startTime = strtotime("+10 minute", $time);
	    $msgContent = array(
	            'type' => 'notification',
	            'save' => 'true',
	            'expired' => $startTime,
	            'p' => self::GAME_PACKAGE,
	            'msgid' =>$msg['id'],
	            'msg' => urlencode(json_encode($msgData)),
	            'rids' => '',
	            'imeis' => $decryptImei,
	            'tags' => 'pushMsg',
	            'i' =>''
	    );
	     
	    $sendInfo = array(
	            'appid' =>  self::getApplicationId(),
	            'token' =>  $token,
	            'pt' => $gioneePushType,
	            'msgs' => array($msgContent),
	    );
	    return $sendInfo;
	}
	
	public static function getHashTime(){
	    return Game_Service_Config::getValue('game_time');
	}
}