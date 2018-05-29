<?php
include 'common.php';
/**
 * 游戏礼包状态 刷新计划任务
 * 解决处理开始时间|结束时间发生变化后相关属性
 * 每天0点05分执行一次
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_EXTRA_DAY_CRON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 60*60);

$data = Resource_Service_GameExtraCache::getAllExtra();
foreach ($data as $gameId) {
    Resource_Service_GameExtraCache::refreshGameGift($gameId);
}
echo CRON_SUCCESS;

$cache->set($lockName, 0, 60*60);
exit;
