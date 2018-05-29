<?php
include 'common.php';
/**
 *每15分钟扫游戏包数据到redis
 */
$cache = Cache_Factory::getCache();
$lock = $cache->get("_cron_scan_gamedata_lock");
if($lock) exit("nothing to do.\n");
$cache->set("_cron_scan_gamedata_lock", 1, 16*60); //11分钟过期


$time = time();
/**
 * 获取游戏版本md5_code跟版本ID对应关系。
 * @param int $gameID
 */
function getVersions($gameID){
	$versions = Resource_Service_Games::getIdxVersionByGameId($gameID);
	$tmp = array();
	foreach ($versions as $key=>$value) {
		$tmp[$value['md5_code']] = $value['id'];
	}
	return $tmp;
}

/**
 * 获取上线游戏版本的拆分信息
 * @param int $gameID
 * @param int $gameID
 */
function getDiff($gameID, $versionId){
	$diffs = Resource_Service_Games::getsByIdxDiff(array('game_id'=>$gameID,'version_id'=> $versionId)); 
	$tmp = array();
	foreach ($diffs as $key=>$value) {
		$tmp[$value['object_id']] = array(
			'link' => $value['link'],
			'size' => $value['size']	
		);
	}
	return $tmp;
}

$cache = Cache_Factory::getCache();
$page = 1;
$packages = array();

do {
//只扫上线的游戏
	list($total, $games) = Resource_Service_Games::getList($page , 100, array('status'=> '1'));
	foreach($games as $key=>$value){
		$tmp = array();
		//包信息
		$packages[$value['packagecrc']]  =  $value['id'];
		//上线游戏信息
		$game = Resource_Service_Games::getGameAllInfo(array('id' =>$value['id']));
		//游戏-版本-差分包
		$tmp = array(
				"id"=> $game['id'],
				"package"=>$game['package'],
				"version"=> $game['version'],
				'version_code' => $game['version_code'],
				'min_sys_version_title' => $game['min_sys_version_title'],
				'min_resolution_title' =>$game['min_resolution_title'],
				'max_resolution_title' =>$game['max_resolution_title'],
				"size" => $game['size'],
				"link" => $game['link'],
				"img" => $game['img'],
				"versions"=> getVersions($game['id']),
				'diff' => getDiff($game['id'], $game['version_id']),
				'freedl' => $game['freedl'],
		        'reward' => $game['reward'],
				'changes'=>($game['changes']) ? preg_replace('/\<br(\s*)?\/?\>/i', "\n", html_entity_decode($game['changes'])) : ""
		);
		$gkey = "-g-u-". $game['id'];
		$cret = $cache->set($gkey, $tmp, 1800);
		if($cret == 'ok'){
			echo date('Y-m-d H:i:s', $time) . $gkey . " write ok .\n";
		}else{
			echo date('Y-m-d H:i:s', $time) . $gkey . " write fail .\n";
		}
	}
	$page++;
	sleep(1);
} while ($total>(($page -1) * 100));

//上线的游戏包-Id
$pkey = "-all-package";
$pret = $cache->set($pkey, $packages, 1800);
if($pret == 'ok'){
	echo date('Y-m-d H:i:s', $time) . $pkey . " write ok .\n";
}else{
	echo date('Y-m-d H:i:s', $time) . $pkey . " write fail .\n";
}

echo CRON_SUCCESS;

$cache->set("_cron_scan_gamedata_lock", 0, 16*60); //11分钟过期
exit;