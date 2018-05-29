<?php
include 'common.php';
$ret = Gionee_Service_Baidu::updateKeywords();
$msg = date('Y-m-d H:i:s') . "=>keywords:" . count($ret['baidu_keywords']) . ";hotwords:" . count($ret['hotwords']) . "\n";
Gionee_Service_CronLog::add(array('type' => 'baidu', 'msg' => $msg));

Gionee_Service_Log::syncReqTimes(date('Ymd', strtotime('-1 day')));
//$res = Gionee_Service_Searchwords::write2DB();
echo CRON_SUCCESS;

?>