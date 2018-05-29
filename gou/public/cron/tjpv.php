<?php
include 'common.php';
/**
 *update pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('gou_pv'));
if (Gou_Service_Stat::incrementPv($pv)) {
		$cache->delete('gou_pv');
}

//add same show pv
$apk_same_show_pv  = intval($cache->get('apk_same_show'));
if (Gou_Service_Stat::increment("apk_same_show",$apk_same_show_pv)) {
	$cache->delete('apk_same_show');
}

//add same show pv
 $ios_same_show_pv  = intval($cache->get('ios_same_show'));
if (Gou_Service_Stat::increment("ios_same_show",$ios_same_show_pv)) {
    $cache->delete('ios_same_show');
}
//add same hits pv
$apk_same_hits  = intval($cache->get('apk_same_hits'));
if (Gou_Service_Stat::increment('apk_same_hits',$apk_same_hits)) {
	$cache->delete('apk_same_hits');
}

//add same hits pv
$ios_same_hits  = intval($cache->get('ios_same_hits'));
if (Gou_Service_Stat::increment('ios_same_hits',$ios_same_hits)) {
	$cache->delete('ios_same_hits');
}

//add reduce goods
$rgpv  = intval($cache->get('gou_reduce_goods'));
if (Gou_Service_Stat::incrementRGPv($rgpv)) {
	$cache->delete('gou_reduce_goods');
}

//update uv
$uv  = intval($cache->get('gou_uv'));
if (Gou_Service_Stat::incrementUv($uv)) {
	$cache->delete('gou_uv');
}
//update ad version
$time = Common::getTime();
$ad = Gou_Service_Ad::getBy(array('status'=>1, 'end_time'=>array('>=', $time)), array('end_time'=>'ASC'));
$ad_version_id = Gou_Service_Config::getValue('Ad_Version_id');
if($ad['id'] != $ad_version_id) Gou_Service_Config::setValue('Ad_Version_id', $ad['id']);
//update client_channel version
$time = Common::getTime();
$channel_version_id = Gou_Service_Config::getValue('Channel_Version_id');
$client_channel = Client_Service_Channel::getBy(array('status'=>1, 'end_time'=>array('>=', $time)), array('end_time'=>'ASC'));
if($client_channel['id'] != $channel_version_id) Gou_Service_Config::setValue('Channel_Version_id', $client_channel['id']);
echo CRON_SUCCESS;
echo PHP_EOL;