<?php

include 'common.php';

//国庆活动过期奖品
$ids = Event_Service_Activity::updateExpiredPrizeMiaoShaStatus();
error_log(json_encode($ids),3,'/tmp/miaosha_expire_log_'.date("Ymd").".log");
echo CRON_SUCCESS;