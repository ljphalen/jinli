<?php
include 'common.php';
/**
 *update idx_game_client_news ntype
 */

list(,$games) = Resource_Service_Games::getAllResourceGames();
$versions = Resource_Service_Games::getIdxGameResourceVersion2();

foreach($games as $key=>$value){
	foreach($versions as $k=>$v){
		if($v['status'] && ($value['id'] == $v['game_id'])) {
			Resource_Service_Games::updateResourceGames(array('online_time'=>$v['create_time']), $v['id']);
		}
	}
}





echo CRON_SUCCESS;