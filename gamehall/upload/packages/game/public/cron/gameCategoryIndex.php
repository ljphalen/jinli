<?php
include 'common.php';
/**
 *初始化分类游戏索引
 *执行频率每天一次
 *fanch
 */
$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_CATLIST_INDEX_CRON;
$lock = $cache->get($lockName);
if($lock) exit("nothing to do.\n");
$cache->set($lockName, 1, 60*60);
//初始化分类游戏
Resource_Index_CategoryList::initCategoryIdx();
echo CRON_SUCCESS;
$cache->set($lockName, 0, 60*60);
exit;
