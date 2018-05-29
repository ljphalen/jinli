<?php

include 'common.php';
$indexInfo = Game_Service_H5RecommendList::getOneByDayId(strtotime(date('Y-m-d',strtotime("+1 day"))));
if (!$indexInfo) {
    Game_Service_H5HomeAutoHandle::copyDayToDay(strtotime(date('Y-m-d')), strtotime(date('Y-m-d',strtotime("+1 day"))), true);
}
echo CRON_SUCCESS;