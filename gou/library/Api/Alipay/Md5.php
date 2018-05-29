<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 产生对称密钥
 *$res = openssl_pkey_new();
 *openssl_pkey_export($res,$pri);
 *$d= openssl_pkey_get_details($res);
 *$pub = $d['key'];
 *var_dump($pri,$pub);
 */
class Api_Alipay_Md5 {
    
    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    public static function md5Sign($prestr, $key) {
    	$prestr = $prestr . $key;
    	return md5($prestr);
    }
    
    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    public static function md5Verify($prestr, $sign, $key) {
    	$prestr = $prestr . $key;
    	$mysgin = md5($prestr);
    
    	if($mysgin == $sign) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

}
