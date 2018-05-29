<?php
include 'common.php';
/**
 *update game_resource_games label
 */
//header("content-type:text/html; charset=utf-8");
$webroot = Common::getWebRoot();
list($total,$games) = Resource_Service_Games::getAllResourceGames();
$dataPath = Common::getConfig('siteConfig', 'dataPath');
$path = $dataPath.'/label.csv';
//echo $path;


$file = fopen($path,'r+');
$hot_list = $data = array();
$i = 0;
while ($data = fgetcsv($file)) { 
	  $hot_list[$i]['id'] = $data[0];
      $hot_list[$i]['label'] = $data[1];
      $i++;
 }
 
 
 foreach($games as $key=>$value){
 	foreach($hot_list as $k=>$val){
 		if($val['id'] == $value['id']){
 			Resource_Service_Games::updateResourceGames(array('label'=>$val['label']), $value['id']);
 		}
 	}
 
 }
 
 echo CRON_SUCCESS;