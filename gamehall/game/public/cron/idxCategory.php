<?php
include 'common.php';
/**
 *更新游戏分类索引表的上线时间和下载量
 */

//update category
//@return Resource_Dao_IdxGameResourceCategory
$page = 1;
do {
    //只扫上线的游戏
    list($total, $games) = Resource_Service_Games::getList($page , 100);
    if(empty($games)) exit("not found data\r\n");
    foreach($games as $key=>$value){
    	$downloads = array();
    	//获取该游戏当前的下载量
    	$downloads = Client_Service_WeekRank::getRankGameId($value['id']);
    	$currentDownloadCount = ($downloads ? $downloads['DL_TIMES'] : 0) ; 
    	
    	if($value['download_status'] == 1){
    		$defaultDownloads = $value['default_downloads'] + ($currentDownloadCount - $value['downloads']);
    	}else{
    		$defaultDownloads = 0;
    	}
    	$defaultDownloads = ($defaultDownloads < 0)?0:$defaultDownloads;
    	
    	//获取该游戏当前的月下载量
    	$monthDownloads = Client_Service_NewMonthRank::getRankGameId($value['id']);
    	$mds = ($monthDownloads ? $monthDownloads['DL_TIMES'] : 0) ;
    	//更新游戏库表的总下载量
    	Resource_Service_Games::updateResourceGamesDownload($currentDownloadCount, $defaultDownloads, $value['id']);
    	//
    	Resource_Service_GameCache::saveGameDataToCache($value['id']);
    	Resource_Service_GameListData::updateListItem($value['id']);
    	
    	//更新游戏库表的月下载量
    	Resource_Service_Games::updateResourceGamesMonthDownload($mds,$value['id']);
    	//更新游戏分类索引表的下载量
    	Resource_Service_Games::updateIdxGameCategoryOntime($value['online_time'],$currentDownloadCount, $value['id']);
    	//更新猜你喜欢索引表的下载量
    	Client_Service_Game::updateIdxGameGuessOntime($value['online_time'],$currentDownloadCount, $value['id']);
    	//更新月榜索引表的下载量
    	Client_Service_Game::updateIdxGameMonthRankOntime($value['online_time'],$currentDownloadCount, $value['id']);
    }
$page++;
} while ($total>(($page -1) * 100));

//mark sync time

echo CRON_SUCCESS;