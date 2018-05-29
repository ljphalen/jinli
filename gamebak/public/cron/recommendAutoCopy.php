<?php
include 'common.php';

/**
 *每天２３：３０分检查第二天的首页推荐内容是否未空，如果为空就复制当天数据到第二天
 */
$today = strtotime(date("Y-m-d"));
$tomorrow = strtotime("+1 day", $today);

$tomorrowList = Game_Service_RecommendList::getRecommendList($tomorrow);
if(! $tomorrowList) {
    $todayList = Game_Service_RecommendList::getRecommendList($today);
    if($todayList) {
        Game_Service_RecommendList::copyRecommendListByDayId($today, $tomorrow);
    }
}

echo CRON_SUCCESS;
