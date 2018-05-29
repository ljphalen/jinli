<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MsgController extends Api_BaseController {
    
    public function receiveMsgAction() {
        $echostr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $postXml = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        if ($echostr && $this->checkSignature($signature, $timestamp, $nonce)) {
        	exit($echostr);
        } else {
            Common::log('$postXml='.$postXml, "weixin.log");
            $msgCenter = new WeiXin_Msg_Center($signature, $timestamp, $nonce);
            $msg = array();
            $result = $msgCenter->decryptMsg($postXml, $msg);
            Common::log('$result='.$result.'  msg = ', "weixin.log");
            Common::log($msg, "weixin.log");
            if ($result == WeiXin_Msg_Error::$OK) {
                $msgCenter->dispatch($msg);
            }
        }
    }
    
    private function checkSignature($signature, $timestamp, $nonce) {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array(WeiXin_Config::getToken(), $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
    
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>