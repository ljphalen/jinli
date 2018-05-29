<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_GameController extends Api_BaseController {
	
	public $perpage = 10;
	
	/**
	 * 游戏大厅下载跳转地址
	 */
	public function gamehallDownloadAction() {
		//游戏大厅的正式站的id是117，测试站是66
		if(ENV == 'product'){
			$game = Resource_Service_GameData::getBasicInfo(117);
		} else {
			$game = Resource_Service_GameData::getBasicInfo(66);
		}
		$this->redirect($game['link']);
		exit;
	}
	
	/**
	 * 获取sdk游戏升级信息
	 */
	public function gameUpgradeAction() {
		$data = array();
		
		$packageName = $this->getInput('packageName');
		$versionName = $this->getInput('versionName');
		$versionCode = $this->getInput('versionCode');
		$md5_code = $this->getInput('md5');
		if(!$packageName || !$versionName || !$versionCode || !$md5_code){
			$this->localOutput(0,'参数错误',$data);
		}
		
		$packagecrc = strval(crc32($packageName));
		$gameInfo = $this->getPackageGameInfo($packagecrc);
		
		if (!$gameInfo) {
			$this->localOutput(0,'',$data);
		}
		
		$data['packageName'] = $packageName;
	    $data['appSize'] = $gameInfo['size'];
	    $data['gameId'] = $gameInfo['id'];
		
		if ($versionCode >= $gameInfo['version_code']) {
		    $data['upgradeType'] = "none";
			$this->localOutput(0,'',$data);
		}
		
		$data['newVersionName'] = $gameInfo['version'];
		$data['newVersionCode'] = $gameInfo['version_code'];
		
		if ($md5_code){
			$old_version_id = $gameInfo['versions'][$md5_code];
			if ($old_version_id) {
				$patchs = $gameInfo['diff'][$old_version_id];
				if($patchs) $type = "splitupdate";
			}
			$data['patchSize'] = ($patchs['size']) ? $patchs['size'] : '';
		}
		
		$updateType = 0;
		$versions = Resource_Service_Games::getIdxVersionByGameId(intval($gameInfo['id']));
		foreach($versions as $key=>$value){
			if ($value['status'] == 1) {
				$updateType = $value['update_type'];
			}
		}
		$data['upgradeType'] = $updateType == 0 ? "normal" : "force";
		$this->localOutput(0,'',$data);
	}
	
	/**获取游戏信息*/
	private function getPackageGameInfo($packagecrc) {
		$cache    = Cache_Factory::getCache();
		$gameId   = $cache->hGet(Util_CacheKey::GAME_PACEKAGE_RELATION_GAMEID, $packagecrc);
		$gameDiffInfo = $cache->get(Util_CacheKey::GAME_PACKAGE_DIFF_INFO.$gameId);
		
		$gameBaseInfo = Resource_Service_GameData::getGameAllInfo($gameId);
    	$gameInfo = array_merge($gameDiffInfo, $gameBaseInfo);
        return $gameInfo;
	}
	
}
