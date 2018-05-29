<?php
include 'common.php';

/**
 *每天凌晨1点刷新vip排行榜
 */
echo User_Api_VipRank::updateDbRank();
