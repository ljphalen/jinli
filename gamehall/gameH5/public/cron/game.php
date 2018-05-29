<?php
include 'common.php';
/**
 *update game_resource_games create_time
 */

//update uv
$start_time = strtotime('2013-04-16 01:08:00');
$loop = 3800;
list($total,$games) = Resource_Service_Games::getAllResourceGames();

foreach($games as $key=>$value){
	$start_time = $start_time + $loop;
	Resource_Service_Games::updateResourceGames(array('create_time'=>$start_time), $value['id']);
}

echo CRON_SUCCESS;