<?php
include 'common.php';
ini_set('memory_limit', '1G');
/**
 *全站pv
 */
$cache = Common::getCache();
$pv    = intval($cache->get('fanfan_pv'));
//总pv
if (Widget_Service_Stat::incrementPv($pv, 0)) {
	$cache->set('fanfan_pv', 0);
}

$d   = date('Ymd');

//统计段链接次数
$file = '/tmp/cron_fanfan_syncdb_'.date('Ymd');
foreach(Widget_Service_Log::$types as $v) {
	$sTime = microtime();
	$msg = date('Y-m-d H:i:s')."start:{$v}\n";
	error_log($msg, 3, $file);
	Widget_Service_Log::sync2DB($v);
	$eTime = microtime();
	$diff =  sprintf('%.3f', ($eTime - $sTime));
	$msg = date('Y-m-d H:i:s')."end:{$v}:({$diff})\n";
	echo $msg;
	error_log($msg, 3, $file);
}


W3_Service_Column::syncSourceIdsByColumn();
Widget_Service_Column::syncSourceIdsByColumn();
echo CRON_SUCCESS;
