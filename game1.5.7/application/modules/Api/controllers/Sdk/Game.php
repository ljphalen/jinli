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
			$game = Resource_Service_GameData::getGameAllInfo(117);
		} else {
			$game = Resource_Service_GameData::getGameAllInfo(66);
		}
		$this->redirect($webroot. $game['link']);
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
		$packagecrc = crc32($packageName);
		
		$cache = Common::getCache();
	
		$cacheData = $cache->get("-all-package");
		$gameId = $cacheData[$packagecrc];
		
		$gkey = "-g-u-". $gameId;
		$game = $cache->get($gkey);
		if (!$game) {
			$this->localOutput(0,'',$data);
		}
		
		$data['packageName'] = $packageName;
	    $data['appSize'] = $game['size'];
	    $data['gameId'] = $game['id'];
		
		if ($versionCode >= $game['version_code']) {
		    $data['upgradeType'] = "none";
			$this->localOutput(0,'',$data);
		}
		
		$data['newVersionName'] = $game['version'];
		$data['newVersionCode'] = $game['version_code'];
		
		if ($md5_code){
			$old_version_id = $game['versions'][$md5_code];
			if ($old_version_id) {
				$patchs = $game['diff'][$old_version_id];
				if($patchs) $type = "splitupdate";
			}
			$data['patchSize'] = ($patchs['size']) ? $patchs['size'] : '';
		}
		
		$updateType = 0;
		$versions = Resource_Service_Games::getIdxVersionByGameId(intval($game['id']));
		foreach($versions as $key=>$value){
			if ($value['status'] == 1) {
				$updateType = $value['update_type'];
			}
		}
		
		$data['upgradeType'] = $updateType == 0 ? "normal" : "force";
		$this->localOutput(0,'',$data);
	}
	
}
