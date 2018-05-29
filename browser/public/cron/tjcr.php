<?php
include 'common.php';
/**
 *统计点击量
 */
$cache = Common::getCache();
$cr  = $cache->get('browser_cr');

if (Browser_Service_Cr::incrementCr($cr)) {
	$cache->set('browser_cr', 0);
}
echo CRON_SUCCESS;
