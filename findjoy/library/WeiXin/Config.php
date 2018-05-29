<?php

/**
 * 	配置账号信息
 */

class WeiXin_Config
{
    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    const APPID = 'wxfb30cace72e2d613';
    const TOKEN = 'cx654123';
    //受理商ID，身份标识
    const MCHID = '1226466402';
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    const KEY = '99fd9d13ec6583fa279b4dcb1871b972';
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    const APPSECRET = '7c1e60aba6c1b9313415ff77e812229d';

    const ENCODING_AES_KEY = 'CiCG1QeetTWSdY1kTlEAZ6FyxAOhwwAdy2cMAdR2ngF';

    //=======【JSAPI路径设置】===================================

    const OPEN_ID_CALL_URL = 'http://www.findjoy.cn/api/weixin/openid';

    //获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    const PAY_CALL_URL = 'http://www.findjoy.cn/front/order/pay';

    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    const SSLCERT_PATH = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_cert.pem';
    const SSLKEY_PATH = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_key.pem';

    //=======【异步通知url设置】===================================
    //异步通知url，商户根据实际开发过程设定
    const NOTIFY_URL = 'http://www.findjoy.cn/api/order/notify';

    //=======【curl超时设置】===================================
    //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
    const CURL_TIMEOUT = 30;

    const ACCESSTOKEN_CACHE_KEY = 'ACCESSTOKEN';

    const JSAPITICKET_CACHE_KEY = 'JSATICKET';

    const API_BASE_URL = 'https://api.weixin.qq.com/cgi-bin';
}

?>
