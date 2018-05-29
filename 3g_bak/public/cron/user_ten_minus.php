<?php
include 'common.php';

//黑名单用名
User_Service_DubiousIpUser::changeUserStatus();

echo CRON_SUCCESS;