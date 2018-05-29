<?php
include 'common.php';

/**
 *每天23:30分检查第二天的首页/网游推荐内容是否未空，如果为空就复制当天数据到第二天
 */
$today = strtotime(date("Y-m-d"));
$tomorrow = strtotime("+1 day", $today);

try {
    homeRecommend($today, $tomorrow);
} catch (Exception $e) {
}

try {
    webGameRecommend($today, $tomorrow);
} catch (Exception $e) {
}

try {
    singleGameRecommend($today, $tomorrow);
} catch (Exception $e) {
}

echo CRON_SUCCESS;

function homeRecommend($today, $tomorrow) {
    $tomorrowList = Game_Service_RecommendList::getRecommendList($tomorrow);
    if(! $tomorrowList) {
        $todayList = Game_Service_RecommendList::getRecommendList($today);
        if($todayList) {
            Game_Service_RecommendList::copyRecommendListByDayId($today, $tomorrow);
        }
    }
}

function webGameRecommend($today, $tomorrow) {
    $tomorrowList = Game_Service_GameWebRecommend::getGameWebRecommend($tomorrow);
    if(! $tomorrowList) {
        $todayList = Game_Service_GameWebRecommend::getGameWebRecommend($today);
        if($todayList) {
            Game_Service_GameWebRecommend::copyRecommendListByDayId($today, $tomorrow);
        }
    }
}

function singleGameRecommend($today, $tomorrow) {
    $tomorrowList = Game_Service_SingleRecommend::getSingleRecommend($tomorrow);
    if(! $tomorrowList) {
        $todayList = Game_Service_SingleRecommend::getSingleRecommend($today);
        if($todayList) {
            Game_Service_SingleRecommend::copyRecommendListByDayId($today, $tomorrow);
        }
    }
}
