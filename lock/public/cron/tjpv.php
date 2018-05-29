<?php
include 'common.php';
/**
 *全站pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('Lock_pv'));

if (Lock_Service_Stat::incrementPv($pv, 0)) {
	$cache->set('Lock_pv', 0);
}

//首页pv
$index_pv  = intval($cache->get('Lock_index_pv'));

if (Lock_Service_Stat::incrementPv($index_pv, 1)) {
	$cache->set('Lock_index_pv', 0);
}
echo CRON_SUCCESS;
