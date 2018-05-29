<?php
include 'common.php';
ini_set('memory_limit', '1G');

$msg = '';
$file  = '/tmp/cron_fanfan_syncdb_' . date('Ymd');
$sTime = microtime();
$hKey1 = 'SYNC_VISIT_WIDGET';
$total = Common::getCache()->hLen($hKey1);
if ($total > 10000){
	Common::getCache()->delete($hKey1);
}
$msg .=$hKey1 . ':' . $total . "\n";

$hKey2 = 'SYNC_VISIT_W3';
$total = Common::getCache()->hLen($hKey2);
if ($total > 10000){
	Common::getCache()->delete($hKey2);
}
$msg .= $hKey2 . ':' . $total . "\n";
echo $msg;
error_log($msg, 3, $file);


$list = Common::getCache()->hGetAll($hKey1);
$i    = 0;
foreach ($list as $id => $t) {
	Widget_Service_User::set(array('last_visit_at' => $t), $id);
	Common::getCache()->hDel($hKey1, $id);
	$i++;
}
$eTime = microtime();
$diff  = sprintf('%.3f', ($eTime - $sTime));
$msg   = date('Y-m-d H:i:s') . $hKey1 . ":{$i}:({$diff})\n";
echo $msg;
error_log($msg, 3, $file);


$sTime = microtime();

$list = Common::getCache()->hGetAll($hKey2);
$i    = 0;
foreach ($list as $id => $t) {
	W3_Service_User::set(array('last_visit_at' => $t), $id);
	Common::getCache()->hDel($hKey2, $id);
	$i++;
}
$eTime = microtime();
$diff  = sprintf('%.3f', ($eTime - $sTime));
$msg   = date('Y-m-d H:i:s') . $hKey2 . ":{$i}:({$diff})\n";
echo $msg;
error_log($msg, 3, $file);