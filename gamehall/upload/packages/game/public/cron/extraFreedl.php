<?php
include 'common.php';
/**
 *免流量附加属性刷新计划任务
 * 该计划任务暂时不使用
 * 1天执行一次
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_EXTRA_FREEDL_CRON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 60*60);

$data = Freedl_Service_Hd::getActivatedItems(array('id' => 'ASC'));
if(empty($item)){
    $cache->set($lockName, 0, 60*60);
    exit("not progress data\r\n");
}

$data = Resource_Service_GameExtraCache::getAllExtra();
foreach ($data as $key => $item) {
    $gameId = $key;
    Resource_Service_GameExtraCache::refreshGameFreedl($gameId);
}
echo CRON_SUCCESS;

$cache->set($lockName, 0, 60*60);
exit;
