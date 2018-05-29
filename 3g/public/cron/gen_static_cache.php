<?php
include 'common.php';
/**
 * 写静态文件
 */
set_time_limit(0);

$staticDir = Common::staticDir();
//-------------------------------
$str      = 0;
$filename = $staticDir . 'api_version_static';
echo Common::writeContentToFile($filename, $str). "\n";

//-------------------------------
$outData      = array(
	'success' => true,
	'msg'     => '',
	'data'    => Gionee_Service_Baidu::apiKeys()
);
$str = json_encode($outData, JSON_UNESCAPED_UNICODE);
$filename = $staticDir . 'api_ng_baidu';
echo Common::writeContentToFile($filename, $str). "\n";
echo CRON_SUCCESS;


Event_Service_Link::checkExpirePrize();