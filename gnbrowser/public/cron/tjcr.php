<?php
include 'common.php';
/**
 *统计点击量
 */
$cache = Common::getCache();
$cr  = $cache->get('3g_cr');

if (Gionee_Service_Cr::incrementCr($cr)) {
	$cache->set('3g_cr', 0);
}
echo CRON_SUCCESS;
