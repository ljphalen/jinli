<?php
include 'common.php';
/**
 * 有奖A券 有奖礼包 附加属性刷新计划任务
 * 解决处理开始时间|结束时间后附件状态的变化
 * 每1小时/02/分执行一次
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_EXTRA_HOUR_CRON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 3*60);

$data = Resource_Service_GameExtraCache::getAllExtra();
foreach ($data as $gameId) {
    Resource_Service_GameExtraCache::refreshGameRewardAcoupon($gameId);
    Resource_Service_GameExtraCache::refreshGameRewardGift($gameId);
}
echo CRON_SUCCESS;

$cache->set($lockName, 0, 3*60);
exit;
