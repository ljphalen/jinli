<?php
set_time_limit(0);

//进程锁回收机制
if(!function_exists('phpCliSyncBICategoryDailyShutdown')) {
    function phpCliSyncBICategoryDailyShutdown() {
        echo "all job done".PHP_EOL;
        $logLock = "/tmp/phpCliSyncBICategoryDaily.lock";
        if(CLI_SELF_THREAD == "SELF") {
            unlink($logLock);
        }
        exit(0);
    }
}
register_shutdown_function('phpCliSyncBICategoryDailyShutdown');

//判断进程锁
$logLock = "/tmp/phpCliSyncBICategoryDaily.lock";
if(is_file($logLock)) {
    echo 'Thread found, exit.' . PHP_EOL;
    exit(0);
}

//添加进程锁
touch($logLock);
define("CLI_SELF_THREAD", "SELF");


//start job
include 'common.php';

//config
$webroot = Common::getWebRoot();
//$version = Common::getConfig('taskConfig', 'version');

//一个月内未同步的数据重新计算
$syncDays = 30;

//是否进入初始化
if (false) {
    //初始化开始日期 2015-01-01
	$startDate = strtotime("2015-01-01");
	$endDate = time();
	$syncDays = floor(($endDate - $startDate) / (3600 * 24));
}
//var_dump($syncDays);

for ($i = $syncDays; $i >= 2; $i--) {
    $dateFilter = date('Ymd', strtotime("-{$i} day"));
    $res = Client_Service_CategoryDailySync::syncBICategoryDaily($dateFilter);
    //var_dump($dateFilter);
}

echo CRON_SUCCESS;