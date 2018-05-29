<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 请求商家获取商品信息接口
 */
class WeiXin_Server_NativeCall extends WeiXin_Server_Base
{
    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        if($this->returnParameters["return_code"] == "SUCCESS"){
            $this->returnParameters["appid"] = WeiXin_Config::APPID;//公众账号ID
            $this->returnParameters["mch_id"] = WeiXin_Config::MCHID;//商户号
            $this->returnParameters["nonce_str"] = self::createNoncestr();//随机字符串
            $this->returnParameters["sign"] = $this->getSign($this->returnParameters);//签名
        }
        return $this->arrayToXml($this->returnParameters);
    }

    /**
     * 获取product_id
     */
    function getProductId()
    {
        $product_id = $this->data["product_id"];
        return $product_id;
    }

}