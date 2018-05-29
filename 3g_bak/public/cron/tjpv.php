<?php
include 'common.php';

//统计
$msg = date('Y-m-d H:i:s') . ':';
foreach (Gionee_Service_Log::$types as $v) {
    $sTime = microtime();
    Gionee_Service_Log::sync2DB($v);
    $eTime = microtime();
    $diff  = sprintf('%.3f', ($eTime - $sTime));
    $msg .= "{$v}:({$diff});";
}

//统计用户数据
foreach (Gionee_Service_Log::$userTypes as $v) {
    $sTime = microtime();
    Gionee_Service_Log::sync2DB($v);
    $eTime = microtime();
    $diff  = sprintf('%.3f', ($eTime - $sTime));
    $msg .= "{$v}:({$diff});";
}
Gionee_Service_CronLog::add(array('type' => 'sync_log', 'msg' => $msg));


Gionee_Service_Tag::sync2DB(date('Ymd'));

echo CRON_SUCCESS;
