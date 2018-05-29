<?php
include 'common.php';

//需要加入黑名单的用户ID
$ret = User_Service_DubiousIpUser::addDubiousUsers();
echo $ret."<br/>";
echo CRON_SUCCESS;