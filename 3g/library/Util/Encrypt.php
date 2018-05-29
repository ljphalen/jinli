<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 加密解密算法
 */
class Util_Encrypt {
    private $_key = 'GIONEE2012061900'; //密匙
    private $_iv  = '0102030405060708';

    function __construct($key = '', $iv = 0) {
        $this->setInit($key, $iv);
    }

    /**
     * 设置密钥
     *
     * @param string $key
     * @param int    $iv
     */
    public function setInit($key, $iv) {
        if (!empty($key)) {
            $this->_key = $key;
            $this->_iv  = ($iv == 0) ? $key : $iv;
        }
    }

    /**
     * des 加密
     *
     * @param string $str
     *
     * @return string
     */
    public function desEncrypt($str) {
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str  = $this->_desPkcs5Pad($str, $size);

        @ $data = mcrypt_cbc(MCRYPT_DES, $this->_key, $str, MCRYPT_ENCRYPT, $this->_iv);
        return base64_encode($data);
    }

    /**
     * des 解密
     *
     * @param string $str
     *
     * @return string
     */
    public function desDecrypt($str) {
        $str = base64_decode($str);
        @ $str = mcrypt_cbc(MCRYPT_DES, $this->_key, $str, MCRYPT_DECRYPT, $this->_iv);
        $str = $this->_desPkcs5Unpad($str);
        return $str;
    }

    private function _desPkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function _desPkcs5Unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    public function hex2bin($hexData) {
        $bindata = '';

        for ($i = 0; $i < strlen($hexData); $i += 2) {
            $bindata .= chr(hexdec(substr($hexData, $i, 2)));
        }

        return $bindata;
    }

    /**
     * aes 加密
     *
     * @param string $str
     *
     * @return string
     */
    public function aesEncrypt($str) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $str  = $this->_desPkcs5Pad($str, $size);
        $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_key, $str, MCRYPT_MODE_CBC, $this->_iv);
        return strtoupper(bin2hex($data));
    }

    /**
     * aes 解密
     *
     * @param string $str
     *
     * @return string
     */
    public function aesDecrypt($str) {
        $str = strtolower($str);
        $str = hex2bin($str);
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_key, $str, MCRYPT_MODE_CBC, $this->_iv);
        $str = $this->_desPkcs5Unpad($str);
        return $str;
    }
	
/**
 * RSA签名
 * @param unknown $data
 * @param unknown $privateKey
 * @return string
 */
    public function rsaSign($data,$privateKey){
    	$privateKey    = chunk_split($privateKey, 64, "\n");
    	$pem = "-----BEGIN RSA PRIVATE KEY-----\n".$privateKey."-----END RSA PRIVATE KEY-----\n";
        //file_put_contents('/tmp/3g_rsa.pem',$pem);



    	$res = openssl_pkey_get_private($pem);
    	//var_dump($res,openssl_error_string());
    	$sign = false;
    	openssl_sign($data, $sign, $res);
    	openssl_free_key($res);
    	return  base64_encode($sign);
    }
    
    /**
     * RSA 签名认证
     * @param unknown $data
     * @param unknown $publicKey
     * @return number
     */
    public function rasVerify($data,$publicKey){
    	ksort($data);
    	$str ='';
    	foreach ($data as $k=>$v){
    		if($k =='sign') continue;
    		$str = "{$k}={$v}&";
    	}
    	$str = substr($str, -1,0);
    	$pubKey = chunk_split($publicKey,64,"\n");
    	$pem = "-----BEGIN RSA PUBLIC KEY-----\n".$pubKey."-----END RSA PUBLIC KEY -----\n";
    	$public_key_id = openssl_pkey_get_public($pem);
    	$signuare = base64_decode($data['sign']);
    	return openssl_verify($signuare, $str, $public_key_id);
    }
}