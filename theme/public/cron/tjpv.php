<?php
include 'common.php';
/**
 *全站pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('Theme_pv'));

if (Theme_Service_Stat::incrementPv($pv, 0)) {
	$cache->set('Theme_pv', 0);
}

//首页pv
$index_pv  = intval($cache->get('Theme_index_pv'));

if (Theme_Service_Stat::incrementPv($index_pv, 1)) {
	$cache->set('Theme_index_pv', 0);
}
echo CRON_SUCCESS;
