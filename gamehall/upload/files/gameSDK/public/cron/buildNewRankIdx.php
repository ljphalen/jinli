<?php
include 'common.php';
/**
 * 刷新新排行榜（飙升榜和活跃榜）索引，每天凌晨4点执行
 */

Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_SOARING);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_OLACTIVE);

echo CRON_SUCCESS;