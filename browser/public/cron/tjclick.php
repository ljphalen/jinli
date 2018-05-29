<?php
include 'common.php';
/**
 *V1.06首页统计点击量
 */
$cache = Common::getCache();
$cr  = $cache->get('browser_click');

if (Browser_Service_Click::incrementClick($cr)) {
	$cache->set('browser_click', 0);
}
echo CRON_SUCCESS;
