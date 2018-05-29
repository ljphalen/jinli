<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信回调接口
 */
class WeixinController extends Api_BaseController {
    private $token = 'gioneebrowser';

    public function indexAction() {
        if (isset($_GET['echostr'])) {
            $b = $this->_checkSignature();
            if ($b) {
                echo $_GET['echostr'];
                exit;
            }
        }

        $wx  = new Vendor_Weixin();
        $out = $wx->call();
        if (!empty($out)) {
            echo $out;
        }
        exit;
    }


    private function _checkSignature() {
        $signature = $this->getInput('signature');
        $timestamp = $this->getInput('timestamp');
        $nonce     = $this->getInput('nonce');

        $info   = Common::getConfig('weixinConfig', 'conf');
        $token  = $info['token'];
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


}
