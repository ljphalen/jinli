<?php
include 'common.php';

$cache = Common::getCache();
$cv = $cache->get(WeiXin_Config::ACCESSTOKEN_CACHE_KEY); 

if (($cv['expires_in'] - Common::getTime()) > (5*60)) {
    exit("access token not expiress.\n");
}

$ret = WeiXin_Service_Base::accessToken();
if ($ret["access_token"]) {
	$ret["expires_in"] = Common::getTime()+$ret["expires_in"];
	$cache->set(WeiXin_Config::ACCESSTOKEN_CACHE_KEY, $ret);
    Common::log("update token success", "weixin.log");

    //
    $rt = WeiXin_Service_Base::getJsApiTicket($ret["access_token"]);
    if ($rt['errcode'] == 0) {
        $cache->set(WeiXin_Config::JSAPITICKET_CACHE_KEY, $rt);
        Common::log("update ticket success", "weixin.log");
    }
}



exit(CRON_SUCCESS);
