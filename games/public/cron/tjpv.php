<?php
include 'common.php';
/**
 *全站pv
 */
$cache = Common::getCache();
$pv  = intval($cache->get('games_pv'));

if (Browser_Service_Stat::incrementPv($pv, 0)) {
	$cache->set('games_pv', 0);
}

//首页pv
$index_pv  = intval($cache->get('games_index_pv'));

if (Browser_Service_Stat::incrementPv($index_pv, 1)) {
	$cache->set('games_index_pv', 0);
}
echo CRON_SUCCESS;
