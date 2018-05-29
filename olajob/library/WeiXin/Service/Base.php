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
		$token  = WeiXin_Config::TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if ($tmpStr == $signature) return true;
		return false;
	}

	/**
	 * 刷新业务token
	 * @return mixed
	 */
	public static function accessToken() {
		$params = array(
			"grant_type"=>"client_credential",
			"appid"=> WeiXin_Config::APPID,
			"secret"=> WeiXin_Config::APPSECRET,
		);
		$result = self::_request("https://api.weixin.qq.com/cgi-bin/token", $params);
		return json_decode($result, true);
	}

	/**
	 * 获取用户信息 
	 * @return mixed
	 */
	public static function getUserInfo($openid) {
		$params = array(
			"access_token"=>self::getToken(),
			'openid'=>$openid,
			"lang"=>'zh_CN', 
		);
		$result = self::_request("https://api.weixin.qq.com/cgi-bin/user/info", $params);
		return json_decode($result, true);
	}

    /**
     * 获取用户信息
     * @return mixed
     */
    public static function getOutUserInfo($openid, $token) {
        $params = array(
            "access_token"=>$token,
            'openid'=>$openid,
            "lang"=>'zh_CN',
        );
        $result = self::_request("https://api.weixin.qq.com/sns/userinfo", $params);
        return json_decode($result, true);
    }

    /**
     * 获取用户ticket
     * @param $token
     * @return mixed
     */
    public static function getJsApiTicket($token) {
        $params = array(
            "access_token"=>$token,
            "type"=>'jsapi',
        );
        $result = self::_request("https://api.weixin.qq.com/cgi-bin/ticket/getticket", $params);
        return json_decode($result, true);
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
		return self::_request("https://api.weixin.qq.com/cgi-bin/message/custom/send", $params, $data);
	}

	/**获取业务token
	 * @return bool
	 */
	public static function getToken() {
		//如果已经存在直接返回
		$redis = Common::getCache();
		$rvalue = $redis->get(WeiXin_Config::ACCESSTOKEN_CACHE_KEY);
		if ($rvalue) return $rvalue["access_token"];
		Common::log("getToken error.", "weixin.log");
		return false;
	}

    /**获取业务jsApiTicket
     * @return bool
     */
    public static function getTicket() {
        //如果已经存在直接返回
        $redis = Common::getCache();
        $rvalue = $redis->get(WeiXin_Config::JSAPITICKET_CACHE_KEY);
        if ($rvalue) return $rvalue["ticket"];
        Common::log("getTicket error.", "weixin.log");
        return false;
    }

    /**
     * @param $pageUrl
     */
    public static function getJsConfigParaeters($pageUrl) {
        $timeStamp = time();
        $temp['jsapi_ticket'] = self::getTicket();
        $temp["timestamp"] = "$timeStamp";
        $temp['url'] = $pageUrl;
        $temp["noncestr"] = self::createNoncestr();
        $signature = self::getJsSign($temp);

        return array(
            'appId'=>WeiXin_Config::APPID,
            'nonceStr'=>$temp['noncestr'],
            'timestamp'=>$temp["timestamp"],
            'signature'=>$signature,
            'jsApiList'=> array("onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","startRecord","stopRecord","onVoiceRecordEnd","playVoice","pauseVoice","stopVoice","onVoicePlayEnd","uploadVoice","downloadVoice","chooseImage","previewImage","uploadImage","downloadImage","translateVoice","getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","hideMenuItems","showMenuItems","hideAllNonBaseMenuItem","showAllNonBaseMenuItem","closeWindow","scanQRCode","chooseWXPay","oenProductSpecificView","addCard","chooseCard","openCard")
        );
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
	public static function _request($uri, $params, $data = null, $method = "POST") {
		$query = http_build_query((array)$params);
		$url = sprintf("%s?%s", $uri, $query);
		Common::log($url, "weixin.log");
		Common::log($data, "weixin.log");
		$curl = new Util_Http_Curl($url);
		$curl->setData($data);
		$data = $curl->send($method);
		Common::log($data, "weixin.log");
		return $data;
	}
}
