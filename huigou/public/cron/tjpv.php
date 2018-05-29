<?php
include 'common.php';
/**
 *首页pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('gouapk_pv'));

if (Gc_Service_Stat::incrementPv($pv)) {
		$cache->delete('gouapk_pv');
}

echo CRON_SUCCESS;