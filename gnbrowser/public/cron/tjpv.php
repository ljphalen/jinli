<?php
include 'common.php';
/**
 *全站pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('3g_pv'));

if (Gionee_Service_Stat::incrementPv($pv, 0)) {
	$cache->set('3g_pv', 0);
}

//首页pv
$index_pv  = intval($cache->get('3g_index_pv'));

if (Gionee_Service_Stat::incrementPv($index_pv, 1)) {
	$cache->set('3g_index_pv', 0);
}
echo CRON_SUCCESS;
