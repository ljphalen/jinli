<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 静态链接二维码
 */
class WeiXin_Server_NativeLink  extends WeiXin_Server_Base
{
    var $parameters;//静态链接参数
    var $url;//静态链接

    function __construct()
    {
    }

    /**
     * 设置参数
     */
    function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * 生成Native支付链接二维码
     */
    function createLink()
    {
        try
        {
            if($this->parameters["product_id"] == null)
            {
                throw new WeiXin_Error("缺少Native支付二维码链接必填参数product_id！"."<br>");
            }
            $this->parameters["appid"] = WeiXin_Config::APPID;//公众账号ID
            $this->parameters["mch_id"] = WeiXin_Config::MCHID;//商户号
            $time_stamp = time();
            $this->parameters["time_stamp"] = "$time_stamp";//时间戳
            $this->parameters["nonce_str"] = self::createNoncestr();//随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters);//签名
            $bizString = $this->formatBizQueryParaMap($this->parameters, false);
            $this->url = "weixin://wxpay/bizpayurl?".$bizString;
        }catch (WeiXin_Error $e)
        {
            Common::log($e->errorMessage(), "weixin.log");
        }
    }

    /**
     * 返回链接
     */
    function getUrl()
    {
        $this->createLink();
        return $this->url;
    }
}