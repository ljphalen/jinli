<?php
include 'common.php';

/**
 *每小时固定执行
 */
homePageSlideAd();
homePageTextAd();
dailyRecommend();
recommendList();

webRecommendBanner();
webRecommendList();

singleRecommendBanner();
singleRecommendList();

function homePageSlideAd() {
    //首页轮播图
    try {
		Game_Api_RecommendBanner::updateClientBannerCacheData();
    } catch (Exception $e) {
    }
}

function homePageTextAd() {
    //首页文字公告
    try {
		Game_Api_RecommendText::updateClientTextCacheData();
    } catch (Exception $e) {
    }
}

function dailyRecommend() {
    //首页每日一荐
    try {
		Game_Api_RecommendDay::updateClientDayCacheData();
    } catch (Exception $e) {
    }
}

function recommendList() {
    //首页推荐列表
    try {
		Game_Api_Recommend::updateClientRecommendCacheData();
    } catch (Exception $e) {
    }
}


function webRecommendBanner() {
    //网游轮播图
    try {
        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
    } catch (Exception $e) {
    }
}

function webRecommendList() {
    //网游推荐列表
    try {
        Game_Api_WebRecommendList::updateClientRecommendCacheData();
    } catch (Exception $e) {
    }
}


function singleRecommendBanner() {
    //单机轮播图
    try {
        Game_Api_SingleRecommendBanner::updateClientBannerCacheData();
    } catch (Exception $e) {
    }
}

function singleRecommendList() {
    //单机推荐列表
    try {
        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
    } catch (Exception $e) {
    }
}

echo CRON_SUCCESS;
