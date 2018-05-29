<?php
include 'common.php';
/**
 *update game_resource_games create_time
 */


updateApkFileSize();		
updatePackageFilesize();

function updateApkFileSize() {	
	$page = 1;
	$params['file_size'] = 0;
	do {
		list($apkTotal, $apkList) = Resource_Service_Games::getVersionGames($page , 100, $params);
		foreach ($apkList as $val){
			$url = $val['link'];
			$result = get_headers($url, 1);
			$apkListParams['id'] = $val['id'] ;
			$apkListData['file_size'] = intval($result['Content-Length']);
			$ret = Resource_Service_Games::updateVersions($apkListData, $apkListParams);
			echo   '更新apk版本库结果'.$ret.'，更新的游戏='.$val['game_id'].'，更新的id='.$val['id'].'，更新大小='.$result['Content-Length']."\n" ;
		}
		$page++;
	} while ($apkTotal >(($page -1) * 100));
	
}

function updatePackageFilesize() {
	$page = 1;
	do {
		$params['file_size'] = 0;
		list($diffPackageTotal, $diffPackageList) = Resource_Service_Games::getDiffPackageList($page  ,100, $params);
		foreach ($diffPackageList as $val){
		 	$url = $val['link'];
			$result = get_headers($url, 1);
			$diffPackageparams['id'] = $val['id'] ;
			$diffPackageData['file_size'] = intval($result['Content-Length']);
			$ret = Resource_Service_Games::updateByDiffpagekage($diffPackageData, $diffPackageparams);
			echo   '更新差分包结果'.$ret.'，更新的游戏='.$val['game_id'].'，更新的id='.$val['id'].'，更新大小='.$result['Content-Length']."\n" ;
		}
		$page++;
	} while ($diffPackageTotal >(($page -1) * 100));
}

 
	



