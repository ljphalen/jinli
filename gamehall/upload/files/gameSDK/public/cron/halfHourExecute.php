<?php
include 'common.php';

/**
 *每半小时固定执行
 */
updateOpenList();

function updateOpenList() {
    //开服信息
    try {
		Game_Api_WebRecommendOpen::updateOpenListCache();
    } catch (Exception $e) {
    }
}

echo CRON_SUCCESS;
