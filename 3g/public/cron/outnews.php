<?php
include 'common.php';
/**
 *获取新闻
 */
set_time_limit(0);

function run1() {
	//彩票数据
	Gionee_Service_Ng::genLotteryData();
}

function run2() {
	//导航新闻内容
	$out = Gionee_Service_Jhnews::run();
	echo $out;
	//Gionee_Service_CronLog::add(array('type' => 'nav_news', 'msg' => $out));
}

function run3() {
	//金立购新闻抓取
	$out = Gionee_Service_Jhnews::runGrabGionee();
	echo $out;
	//Gionee_Service_CronLog::add(array('type' => 'out_', 'msg' => $out));
}

function run4() {
	//聚合阅读新闻抓取
	$out = Gionee_Service_OutNews::run();
	echo $out;
	//Gionee_Service_CronLog::add(array('type' => 'out_news', 'msg' => $out));

}

function run5() {
	$out = Gionee_Service_OutNews::runDownImg();
	echo $out;
}




echo "\nrun1--------------------------------\n";
run1();
echo "\nrun2--------------------------------\n";
run2();
echo "\nrun3--------------------------------\n";
run3();
echo "\nrun4--------------------------------\n";
run4();
echo "\nrun5--------------------------------\n";
run5();


Gionee_Service_CronLog::add(array('type' => 'out_news', 'msg' => ''));




