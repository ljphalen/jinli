<?php
include 'common.php';
/**
 *刷新游戏缓存数据-2015-02-09
 *fanch
 */
$page = 1;
do {
    //只扫上线的游戏
	list($total, $games) = Resource_Service_Games::getList($page , 100);
	if(empty($games)) exit("not found data\r\n");
	foreach ($games as $value){
		if($value['status']){
			//更新上线缓存
			Resource_Service_GameCache::saveGameDataToCache($value['id']);
		}else{
			//删除下线
			Resource_Service_GameCache::deleteGameCache($value['id']);
		}
		echo "game-cache-update-ok:{$value['id']}\r\n";
	}
	$page++;
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;
