<?php
class Api_Baidu_Fanyi {

    //请开发者设置自己的apiKey
    public static $apiKey = "v0Vod3TVOjdLdftQICpesQBB";
    public static $url = 'http://openapi.baidu.com/public/2.0/bmt/translate';


    /**
     * 百度翻译接口
     * $from 源语言语种
     * $to 目标语言语种
     * $content 待翻译内容 该字段必须为UTF-8编码，并且以GET方式调用API时，需要进行urlencode编码。
     * $custom_content json格式 如url或者activity
     */
    public static function translate($from, $to, $content)
    {
        if(!$from || !$to || !$content) return false;
        if($from==$to) return $content;
        $url = self::$url;
        $data = array(
                        'from'=>$from, 
                        'to'=>$to, 
                        'q'=>$content,
                        'client_id'=>self::$apiKey
        );
        $curl = new Util_Http_Curl($url);
        $result = $curl->post($data);
        $result = json_decode($result, true);
        if($result['error_code']) return false;
        
        $ret = $result['trans_result'][0];
        return $ret['dst'];
    }

}