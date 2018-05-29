<?php
include 'common.php';
/**
 *update pv
 */
// PV和UV的统计不全，而且目前不太需要，故而去掉。
/*
$cache = Cache_Factory::getCache();
$pv  = intval($cache->get('game_pv'));
if (Game_Service_Stat::incrementPv($pv)) {
		$cache->delete('game_pv');
}

//update uv
$uv  = intval($cache->get('game_uv'));
if (Game_Service_Stat::incrementUv($uv)) {
	$cache->delete('game_uv');
}

echo CRON_SUCCESS;
*/