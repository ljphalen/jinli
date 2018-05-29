<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * JSAPI支付——H5网页端调起支付接口
 */
class WeiXin_Server_User extends WeiXin_Server_Base
{
    public $code;//code码，用以获取openid
    public $openid;//用户的openid
    public $userInfo; //用户信息
    public $parameters;//jsapi参数，格式为json
    public $prepay_id;//使用统一支付接口得到的预支付id
    public $curl_timeout;//curl超时时间
    public $jsapi_ticket; //用户ticket

    function __construct()
    {
        //设置curl超时时间
        $this->curl_timeout = WeiXin_Config::CURL_TIMEOUT;
    }

    /**
     * 	作用：生成可以获得code的url
     */
    function createOauthUrlForCode($redirectUrl, $chk)
    {
        $urlObj["appid"] = WeiXin_Config::APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = $chk ? "snsapi_userinfo" :"snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     * 	作用：生成可以获得openid的url
     */
    function createOauthUrlForToken()
    {
        $urlObj["appid"] = WeiXin_Config::APPID;
        $urlObj["secret"] = WeiXin_Config::APPSECRET;
        $urlObj["code"] = $this->code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function getOutInfo() {
        $userInfo = WeiXin_Service_Base::getOutUserInfo($this->openid, $this->data["access_token"]);
        Common::log($userInfo, "wx.log");
        return $userInfo;
    }

    /**
     *获取用户信息
     */ 
    public function getInfo() {
		$userInfo = WeiXin_Service_Base::getUserInfo($this->openid);
        Common::log($userInfo, "wx.log");
        return $userInfo;	
    }


    /**
     * 	作用：通过curl向微信提交code，以获取openid
     */
    function getToken()
    {
        $url = $this->createOauthUrlForToken();
        Common::log($url,"weixin.log");
        $curl = new Util_Http_Curl($url);
        $data = $curl->send();
        $this->data= json_decode($data, true);
        Common::log($this->data, "weixin.log");
        $this->openid = $this->data['openid'];
        return $this->data;
    }

    /**
     * 	作用：设置prepay_id
     */
    function setPrepayId($prepayId)
    {
        $this->prepay_id = $prepayId;
    }

    /**
     * 	作用：设置code
     */
    function setCode($code_)
    {
        $this->code = $code_;
    }

    /**
     * 	作用：设置jsapi的参数
     */
    public function getParameters()
    {
        $jsApiObj["appId"] = WeiXin_Config::APPID;
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = self::createNoncestr();
        $jsApiObj["package"] = "prepay_id=".$this->prepay_id;
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->getSign($jsApiObj);
        $this->parameters = json_encode($jsApiObj);

        return $this->parameters;
    }
}
