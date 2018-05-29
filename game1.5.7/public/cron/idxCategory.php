<?php
include 'common.php';
/**
 *更新游戏分类索引表的上线时间和下载量
 */

//update category
//@return Resource_Dao_IdxGameResourceCategory

list(,$games) = Resource_Service_Games::getAllResourceGames();

foreach($games as $key=>$value){
	$downloads = array();
	//获取该游戏当前的下载量
	$downloads = Client_Service_WeekRank::getRankGameId($value['id']);
	$ds = ($downloads ? $downloads['DL_TIMES'] : 0) ; 
	//获取该游戏当前的月下载量
	$monthDownloads = Client_Service_NewMonthRank::getRankGameId($value['id']);
	$mds = ($monthDownloads ? $monthDownloads['DL_TIMES'] : 0) ;
	//更新游戏库表的总下载量
	Resource_Service_Games::updateResourceGamesDownload($ds,$value['id']);
	//更新游戏库表的月下载量
	Resource_Service_Games::updateResourceGamesMonthDownload($mds,$value['id']);
	//更新游戏分类索引表的下载量
	Resource_Service_Games::updateIdxGameCategoryOntime($value['online_time'],$ds, $value['id']);
	//更新猜你喜欢索引表的下载量
	Client_Service_Game::updateIdxGameGuessOntime($value['online_time'],$ds, $value['id']);
	//更新月榜索引表的下载量
	Client_Service_Game::updateIdxGameMonthRankOntime($value['online_time'],$ds, $value['id']);
}

//mark sync time

echo CRON_SUCCESS;