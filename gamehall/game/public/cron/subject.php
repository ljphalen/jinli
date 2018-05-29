<?php
include 'common.php';

echo CRON_SUCCESS;

list(, $games) = Resource_Service_Games::getAllResourceGames();
$games = Common::resetKey($games, 'id');

$subject_game_ids = Client_Service_Game::getIdxSubjectBySubjectAllId();
$tmp = array();
foreach($subject_game_ids as $key=>$value){
	Client_Service_Game::updateSubjectsByGameIds($value['resource_game_id'],$games[$value['resource_game_id']]['status']);
}