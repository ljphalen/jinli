<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_Order extends Common_Service_Base {

    protected static $name = 'Theme_Dao_Order';

    private static function _getDao() {
        return Common::getDao(self::$name);
    }

    public static function getUserPrices($userId) {
        $where = "ucenter_id = '$userId'";
        $res = self::_getDao()->getDataDao($where, "*");
        return $res;
    }

    function rsa_verify($post_arr) {
        ksort($post_arr);
        foreach ($post_arr as $key => $value) {
            if ($key == 'sign') continue;
            $signature_str .= $key . '=' . $value . '&';
        }
        $signature_str = substr($signature_str, 0, -1);

        $publickey = Common::getConfig('siteConfig', 'notifyPublicKey');
        $pem = chunk_split($publickey, 64, "\n");
        $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        $public_key_id = openssl_pkey_get_public($pem);
        $signature = base64_decode($post_arr['sign']);
        return openssl_verify($signature_str, $signature, $public_key_id);
    }

    public function rsa_sign($post_arr) {
        ksort($post_arr);
        foreach ($post_arr as $key => $value) {
            $signature_str .= $value;
        }
        $private_key = Common::getConfig('siteConfig', 'privateKey');
        $pem = chunk_split($private_key, 64, "\n");
        $pem = "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
        $private_key_id = openssl_pkey_get_private($pem);
        $signature = false;
        openssl_sign($signature_str, $signature, $private_key_id);
        $sign = base64_encode($signature);
        return $sign;
    }

    public function https_curl($url, $post_arr = array(), $timeout = 10) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_arr);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $content = curl_exec($curl);
        curl_close($curl);

        return $content;
    }

}
