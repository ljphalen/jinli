<?php
include 'common.php';
/**
 *update game_resource_games label
 */

$webroot = Common::getWebRoot();
list($total,$games) = Resource_Service_Games::getAllResourceGames();
$dataPath = Common::getConfig('siteConfig', 'dataPath');
$path = $dataPath.'/label.csv';
//echo $path;


$file = fopen($path,'r+');
$labels_list = $data = array();
while ($data = fgetcsv($file)) { 
	  $labels_list[$i]['id'] = $data[0];
      $labels_list[$i]['label'] = $data[1];
      $i++;
 }

 /*
echo "<pre>";
print_r($codes_list);
*/

fclose($file);


foreach($games as $key=>$value){
	foreach($labels_list as $k=>$val){
		if($val['id'] == $value['id']){
			Resource_Service_Games::updateResourceGames(array('label'=>$val['label']), $value['id']);
		}
	}
	
}


echo CRON_SUCCESS;