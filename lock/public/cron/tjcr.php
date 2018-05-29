<?php
include 'common.php';
/**
 *统计点击量
 */
$cache = Common::getCache();
$cr  = $cache->get('Lock_cr');

if (Lock_Service_Cr::incrementCr($cr)) {
	$cache->set('Lock_cr', 0);
}
echo CRON_SUCCESS;
