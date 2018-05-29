<?php
include 'common.php';
/**
 *获取新闻
 */
set_time_limit(0);


function run1() {//歌手数据抓取
	$out= Partner_Service_SingerNews::run();
	//echo $out;
}

function run2() {//日历数据抓取
	//Partner_Service_HistoryToday::downDetailContent();
}

echo "\nrun1--------------------------------\n";
run1();
echo "\nrun2--------------------------------\n";
//run2();

Gionee_Service_CronLog::add(array('type' => 'partner', 'msg' => ''));
echo CRON_SUCCESS;

//
$ret  = User_Service_Order::verifyFlowOrder();
