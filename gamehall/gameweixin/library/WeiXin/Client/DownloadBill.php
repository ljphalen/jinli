<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 对账单接口
 */
class WeiXin_Client_DownloadBill extends WeiXin_Client_Base
{

    function __construct()
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
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
            if($this->parameters["bill_date"] == null )
            {
                throw new WeiXin_Error("对账单接口中，缺少必填参数bill_date！"."<br>");
            }
            $this->parameters["appid"] = WeiXin_Config::getAppID();//公众账号ID
            $this->parameters["mch_id"] = WeiXin_Config::MCHID;//商户号
            $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters);//签名
            return  $this->arrayToXml($this->parameters);
        }catch (WeiXin_Error $e)
        {
            Common::log($e->errorMessage(), "weixin.log");
            return false;
        }
    }

    /**
     * 	作用：获取结果，默认不使用证书
     */
    function getResult()
    {
        $this->postXml();
        $this->result = $this->xmlToArray($this->result_xml);
        return $this->result;
    }



}