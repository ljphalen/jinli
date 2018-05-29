<?php
include 'common.php';
/**
 *每天凌晨3点执行
 */

function saveGameDiffPackageToCache($cache, $gameId) {
	$gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
	//游戏-版本-差分包
	$game = array(
			"id"           => $gameInfo['id'],
			"version"      => $gameInfo['version'],
			'version_code' => $gameInfo['version_code'],
			"versions"     => Resource_Service_GameCache::getVersionRelationIds($gameInfo['id']),
			'diff'         => Resource_Service_GameCache::getDiffInfo($gameInfo['id'], $gameInfo['version_id']),
	);
	$key = Resource_Service_GameCache::getGameDiffInfoKey($gameId);
	$ret = $cache->set($key, $game, Resource_Service_GameCache::PACKAGE_INFO_CACHE_EXPIRE);
	if($ret == 'ok'){
		echo date('Y-m-d H:i:s') . $key . " write ok .\n";
	}else{
		echo date('Y-m-d H:i:s') . $key . " write fail .\n";
	}
}

function saveGamePackageToCache($cache, $packages) {
	//上线的游戏包-Id
	$hashKey = Resource_Service_GameCache::getGamePacekageRelationGameIdfoKey();
	$ret = $cache->hMset($hashKey, $packages, Resource_Service_GameCache::PACKAGE_INFO_CACHE_EXPIRE);
	if($ret == 'ok'){
		echo date('Y-m-d H:i:s') . $hashKey . " write ok .\n";
	}else{
		echo date('Y-m-d H:i:s') . $hashKey . " write fail .\n";
	}
}

function offCacheLock($cache) {
	$cache->delete(Util_CacheKey::LOCK_SCAN_GAMEDATA);
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
			saveGameDiffPackageToCache ($cache, $value['id']);
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

