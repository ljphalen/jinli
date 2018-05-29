<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 订单查询接口
 */
class WeiXin_Client_OrderQuery extends WeiXin_Client_Base
{
    function __construct()
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //设置curl超时时间
        $this->curl_timeout = WeiXin_Config::CURL_TIMEOUT;
    }

    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        try
        {
            //检测必填参数
            if($this->parameters["out_trade_no"] == null &&
                $this->parameters["transaction_id"] == null)
            {
                throw new WeiXin_Error("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
            }
            $this->parameters["appid"] = WeiXin_Config::APPID;//公众账号ID
            $this->parameters["mch_id"] = WeiXin_Config::MCHID;//商户号
            $this->parameters["nonce_str"] = self::createNoncestr();//随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters);//签名
            return  $this->arrayToXml($this->parameters);
        }catch (WeiXin_Error $e)
        {
            Common::log($e->errorMessage(), "weixin.log");
            return false;
        }
    }

}
