<?php
include 'common.php';
/**
 *每15分钟扫游戏包数据到redis
 */


/**
 * 获取游戏版本md5_code跟版本ID对应关系。
 * @param int $gameID
 */
function getVersionIds($gameID){
	$versionInfo = Resource_Service_Games::getIdxVersionByGameId($gameID);
	$versionIds = array();
	foreach ($versionInfo as $key=>$value) {
		$versionIds[$value['md5_code']] = $value['id'];
	}
	return $versionIds;
}

/**
 * 获取上线游戏版本的拆分信息
 * @param int $gameID
 * @param int $gameID
 */
function getDiff($gameID, $versionId){
	$diffs = Resource_Service_Games::getsByIdxDiff(array('game_id'=>$gameID,'version_id'=> $versionId)); 
	$diffInfo = array();
	foreach ($diffs as $key=>$value) {
		$diffInfo[$value['object_id']] = array(
			'link' => $value['link'],
			'size' => $value['size']	
		);
	}
	return $diffInfo;
}

function saveGameDiffPackageToCache($cache, $gameInfo) {
	//游戏-版本-差分包
	$game = array(
			"id"=> $gameInfo['id'],
			"package"=>$gameInfo['package'],
			"version"=> $gameInfo['version'],
			'version_code' => $gameInfo['version_code'],
			'min_sys_version_title' => $gameInfo['min_sys_version_title'],
			'min_resolution_title' =>$gameInfo['min_resolution_title'],
			'max_resolution_title' =>$gameInfo['max_resolution_title'],
			"size" => $gameInfo['size'],
			"link" => $gameInfo['link'],
			"img" => $gameInfo['img'],
			"versions"=> getVersionIds($gameInfo['id']),
			'diff' => getDiff($gameInfo['id'], $gameInfo['version_id']),
			'freedl' => $gameInfo['freedl'],
			'reward' => $gameInfo['reward'],
			'changes'=>($gameInfo['changes']) ? preg_replace('/\<br(\s*)?\/?\>/i', "\n", html_entity_decode($gameInfo['changes'])) : ""
	);
	$key = Util_CacheKey::GAME_PACKAGE_DIFF_INFO.$gameInfo['id'];
	$ret = $cache->set($key, $game, 1800);
	if($ret == 'ok'){
		echo date('Y-m-d H:i:s') . $key . " write ok .\n";
	}else{
		echo date('Y-m-d H:i:s') . $key . " write fail .\n";
	}
}

function saveGamePackageToCache($cache, $packages) {
	//上线的游戏包-Id
	$hashKey = Util_CacheKey::GAME_PACEKAGE_INFO;
	$ret = $cache->hMset($hashKey, $packages, 1800);
	if($ret == 'ok'){
		echo date('Y-m-d H:i:s') . $hashKey . " write ok .\n";
	}else{
		echo date('Y-m-d H:i:s') . $hashKey . " write fail .\n";
	}
}

function offCacheLock($cache) {
	$cache->set(Util_CacheKey::LOCK_SCAN_GAMEDATA, 0, 16*60); //11分钟过期
}

function onCacheLock($cache) {
	$lock = $cache->get(Util_CacheKey::LOCK_SCAN_GAMEDATA);
	if($lock) exit("nothing to do.\n");
	$cache->set(Util_CacheKey::LOCK_SCAN_GAMEDATA, 1, 16*60);
}

function main(){
	$cache = Cache_Factory::getCache();
	onCacheLock ( $cache );
	$page = 1;
	$packages = array();
	do {
		//只扫上线的游戏
		list($total, $games) = Resource_Service_Games::getList($page ,
				100,
				array('status'=> '1'));
		foreach($games as $key=>$value){
			//包信息
			$packages[$value['packagecrc']]  =  $value['id'];
			//上线游戏信息
			$gameInfo = Resource_Service_Games::getGameAllInfo(array('id' =>$value['id']));
			saveGameDiffPackageToCache ($cache, $gameInfo );
		}
		$page++;
		sleep(1);
	} while ($total>(($page -1) * 100));
	saveGamePackageToCache ( $cache, $packages );
	offCacheLock ( $cache );
	echo CRON_SUCCESS;
	exit;
}


main();

