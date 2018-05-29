<?php
include 'common.php';
/**
 * 游戏名称生成脚本
 * 
 */
$sourceFile = 'words.txt';
$dataPath = Common::getConfig('siteConfig', 'dataPath');
$saveFile = $dataPath . $sourceFile;
$remove = false;
if(file_exists($dataPath . $sourceFile)){
	$destFile = time();
	$remove = true;
	$saveFile = $dataPath . $destFile;
}

$page = 1;
do {
	//只扫上线的游戏
	list($total, $data) = Resource_Service_Games::getList($page , 100);
	if(!$data) break;
	$line = array();
	foreach($data as $item){
		$line[] = html_entity_decode($item['name']);
	}
	$content = implode("\n", $line);
	file_put_contents($saveFile, $content, FILE_APPEND);
	$page++;
	sleep(1);
} while ($total>(($page -1) * 100));

if($remove){
	rename($dataPath . $sourceFile, $dataPath . $destFile);
	rename($dataPath . $destFile, $dataPath . 'words.txt');
}

echo CRON_SUCCESS;