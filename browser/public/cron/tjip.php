<?php
include 'common.php';
/**
 *统计IP
 */

$cache = Common::getCache();
$arr_ip  = $cache->get('browser_ip');

if (Browser_Service_Stat::incrementIp($arr_ip)) {
	$cache->set('browser_ip', 0);
}
echo CRON_SUCCESS;