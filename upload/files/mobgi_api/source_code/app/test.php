<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 公钥解密
 */
function publickey_decoding($crypttext, $pubkey) {
    //$crypttextArr = json_decode(urldecode($crypttext), true);
    $pubkey='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEarjGbIAtSasw18BskdzT6WFgiLr+oRj0WWLQJ4JLzuP+tj2gyjSMCYRnHomu2esKGkh0tzRzULNMm9aBhLG2ajxD0QEb7c7UJTKTNBR/IO3xp5DpEmWKgbjhz9XONy4hN3OObJfWCFvu5PA+lqIbNB0SFKz0B0gjll2IKwngrwIDAQAB';
    $prikeyid = openssl_get_publickey($pubkey);
    var_dump($prikeyid);
    openssl_public_decrypt(base64_decode($crypttext), $sourcestr, $prikeyid);
//    foreach ($crypttextArr as $value) {
//        if (openssl_public_decrypt(base64_decode($value), $sourcestr, $prikeyid)) {
//            $ret .=$sourcestr;
//        }
//    }
    return $sourcestr;
}
/*
 *解析skynet_config.txt中的信息
 */
function decoding_skynet_config($pubkey) {
    if(!file_exists("/tmp/assets/skynet_config.txt")){
        return false;
    }
    $crypttext=  file_get_contents("/tmp/assets/skynet_config.txt");
    $crypttextArr = json_decode(urldecode($crypttext), true);
    foreach ($crypttextArr as $value) {
            $ret .=publickey_decoding($value,$pubkey);
    }
    if(empty($ret)){
        return false;
    }
    $ret=  json_decode($ret);
    if(isset($ret["channel_id"])){
        return $ret;
    }
    return false;
}
$myconfig["PUBKEY_NETGAME"]="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDczwo3vjIb+Xa9+xfOxzkODhAakss39GubdgPLyl+XPmCkwCc53htPsfWdV2EqHA9AK4T/gPcXPgxniaguNrGGaUn9s+jliMF88O3aR68rAC/CgxdvEkapAH34qtrRrV4TTSdiXIOQmwS9u+3O4wteHbZ2S/9vBBMIbRh3RIgDpQIDAQAB";
$myconfig["PUBKEY_ONEPACKAGE"]="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEarjGbIAtSasw18BskdzT6WFgiLr+oRj0WWLQJ4JLzuP+tj2gyjSMCYRnHomu2esKGkh0tzRzULNMm9aBhLG2ajxD0QEb7c7UJTKTNBR/IO3xp5DpEmWKgbjhz9XONy4hN3OObJfWCFvu5PA+lqIbNB0SFKz0B0gjll2IKwngrwIDAQAB";
echo decoding_skynet_config($myconfig["PUBKEY_NETGAME"]);
?> 
