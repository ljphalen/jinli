<?php
include 'common.php';
/**
 * 初始化排行榜数据   只执行一次
 */
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_MONTH);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_NEW);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_ONLINE);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_PC);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_UP);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_WEEK);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_SOARING);
Resource_Index_RankList::buildRankListIdx(Resource_Index_RankList::RANK_TYPE_OLACTIVE);

echo CRON_SUCCESS;