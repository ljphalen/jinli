<?php

include 'common.php';
//同一IP多账号限制
User_Service_DubiousIp::addDubiousIps();

echo CRON_SUCCESS;