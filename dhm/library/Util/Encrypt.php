<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 加密解密算法
 */
class Util_Encrypt {
    private $_key; //密匙
    private $_iv;

    function __construct($key, $iv = 0) {
        $this->setInit($key, $iv);
    }
    /**
     * 设置密钥
     * @param string $key
     * @param int $iv
     */
    public function setInit($key, $iv = 0) {
        $this->_key = $key;
        if ($iv == 0)  $this->_iv = 12345678;
    }
    /**
     * des 加密
     * @param string $str
     * @return string
     */
    public function desEncrypt($str) {
        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');

        mcrypt_generic_init($cipher, $this->_key, $this->_iv);
        $encrypted = mcrypt_generic($cipher,$str);
        mcrypt_generic_deinit($cipher);

        return base64_encode($encrypted);
    }

    /**
     * des 解密
     * @param string $str
     * @return string
     */
    public function desDecrypt($str) {
        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');

        mcrypt_generic_init($cipher, $this->_key, $this->_iv);
        $decrypted = mdecrypt_generic($cipher,base64_decode($str));
        mcrypt_generic_deinit($cipher);

        return $decrypted;
    }
}