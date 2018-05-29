<?php
include 'common.php';
/**
 *刷新游戏缓存数据-2015-02-09
 *fanch
 */

$page = 1;
do {
    //只扫上线的游戏
	list($total, $games) = Resource_Service_Games::getList($page , 100, array('status'=>1));
	if(empty($games)) exit("not found data\r\n");
	foreach ($games as $value){
			//同步应用
			if($value['appid']){
				Dev_Service_Sync::setGameOn($value['appid']);
			}
	  echo "-game-sync-ok:{$value['id']}\r\n";
	}
	$page++;
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;
