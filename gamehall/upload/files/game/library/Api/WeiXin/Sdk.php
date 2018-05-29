<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author fanch
 *
 */
class Api_WeiXin_Sdk {
    const CACHE_WEIXIN_ACCESS_TOKEN = 'WEI_XIN_ACCESS_TOKEN';
    const CACHE_WEIXIN_JSAPI_TICKET = 'WEI_XIN_JSAPI_TICKET';

    const WEIXIN_JSAPI_TYPE = 'jsapi';
    const WEIXIN_BASE_URL = 'https://api.weixin.qq.com/cgi-bin';


    public static function getToken() {
        $redis = Cache_Factory::getCache();
        $cacheData = $redis->get(self::CACHE_WEIXIN_ACCESS_TOKEN);
        if ($cacheData) return $cacheData;
        return self::getAccessToken();
    }

    public static function getJsTicket($token) {
        $redis = Cache_Factory::getCache();
        $cacheKey = self::CACHE_WEIXIN_JSAPI_TICKET;
        $cacheData = $redis->get($cacheKey);
        if ($cacheData) return $cacheData;
        return self::getJsApiTicket($token, self::WEIXIN_JSAPI_TYPE);
    }

    public static function getJsSignature($jsTicket, $nonceStr,$time, $url){
        $string = "jsapi_ticket=$jsTicket&noncestr=$nonceStr&timestamp=$time&url=$url";
        $signature = sha1($string);
        return $signature;
    }

    public static function getNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    private static function getJsApiTicket($token, $type){
        $params = array(
            'access_token'=>$token,
            'type'=>$type
        );
        $result = self::request("/ticket/getticket", $params);
        if(!$result) return "";
        $ticketData = json_decode($result, true);
        Cache_Factory::getCache()->set(self::CACHE_WEIXIN_JSAPI_TICKET, $ticketData['ticket'], $ticketData['expires_in']);
        return $ticketData['ticket'];
    }

    private static function getAccessToken() {
        $params = array(
            "grant_type"=>"client_credential",
            "appid"=> self::getAppID(),
            "secret"=> self::getAppSecret(),
        );
        $result = self::request("/token", $params);
        if(!$result) return "";
        $tokenData = json_decode($result, true);
        Cache_Factory::getCache()->set(self::CACHE_WEIXIN_ACCESS_TOKEN, $tokenData['access_token'], $tokenData['expires_in']);
        return $tokenData['access_token'];
    }

    private static function request($uri, $params, $data = null, $method = "POST") {
        $query = http_build_query((array)$params);
        $url = sprintf("%s%s?%s", self::WEIXIN_BASE_URL, $uri, $query);
        $curl = new Util_Http_Curl($url);
        $curl->setData($data);
        $result = $curl->send($method);
        $errcode = $result->errcode;
        if ($errcode) {
            Util_Log::err(__CLASS__,'weixin.log',array('url'=>$url,'data'=>'data','result'=>$result));
            return array();
        }
        return $result;
    }

    public static function getAppID() {
        return Common::getConfig('weixinConfig', 'appID');
    }

    public static function getAppSecret() {
        return Common::getConfig('weixinConfig', 'appSecret');
    }
}