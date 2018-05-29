<?php
include 'common.php';
/**
 *update game_resource_games version_code
 */

$webroot = Common::getWebRoot();
list($total,$games) = Resource_Service_Games::getAllResourceGames();
$dataPath = Common::getConfig('siteConfig', 'dataPath');
$path = $dataPath.'/vercode.csv';
//echo $path;


$file = fopen($path,'r+');
$codes_list = $data = array();
while ($data = fgetcsv($file)) { 
	  $codes_list[$i]['id'] = $data[0];
      $codes_list[$i]['version_code'] = $data[1];
      $i++;
 }

 /*
echo "<pre>";
print_r($codes_list);
*/

fclose($file);
$tmp = array();
$contents = file_get_contents('http://game.3gtest.gionee.com/apk.txt');
$handle = @fopen("http://game.3gtest.gionee.com/apk.txt", "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $tmp[] = $buffer;
        echo $buffer;
    }
    fclose($handle);
}

//print_r($tmp);


foreach($games as $key=>$value){
	foreach($codes_list as $k=>$val){
		if($val['id'] == $value['id']){
			Resource_Service_Games::updateResourceGames(array('version_code'=>$val['version_code']), $value['id']);
		}
	}
	
}


echo CRON_SUCCESS;