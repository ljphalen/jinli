<?php
include 'common.php';

list(, $games) = Resource_Service_Games::getAllResourceGames();
$games = Common::resetKey($games, 'id');

$subject_game_ids = Client_Service_Game::getIdxSubjectBySubjectAllId();
$tmp = array();
foreach($subject_game_ids as $key=>$value){
	Client_Service_Game::updateSubjectsByGameIds($value['resource_game_id'],$games[$value['resource_game_id']]['status']);
}
echo CRON_SUCCESS;