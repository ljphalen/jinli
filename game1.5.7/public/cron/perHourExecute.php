<?php
include 'common.php';

/**
 *每小时固定执行
 */
homePageSlideAd();
homePageTextAd();
dailyRecommend();
recommendList();

echo CRON_SUCCESS;


function homePageSlideAd() {
    //首页轮播图
    try {
        Client_Service_Ad::updateSlideAdCacheData();    
    } catch (Exception $e) {
    }
}


function homePageTextAd() {
    //首页文字公告
    try {
        Client_Service_Ad::updateTextAdCacheData();
    } catch (Exception $e) {
    }
}

function dailyRecommend() {
    //首页每日一荐
    try {
        Game_Service_RecommendDay::updateIndexRecommendCacheData();
    } catch (Exception $e) {
    }
}

function recommendList() {
    //首页推荐列表
    try {
        Game_Service_RecommendNew::updateRecomendListCacheData();
    } catch (Exception $e) {
    }
}





