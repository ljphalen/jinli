<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_UpdateController extends Api_BaseController {
	public $perpage = 50;
	const UPGRADE_ALL = "update";//整包更新
	const UPGRADE_DIFF = "splitupdate";//差分包更新
	private $webroot = "";
	private $cache = null;

	/**
	 * 接收包名
	 * 新策略为3天内不重复处理任何更新请求
	 */
	public function updateAction() {
		
		$apkInfo = $this->getInput('apk_info');
		if (!$apkInfo){
			$this->output(-1, "");
		} 
		//1.4.8之后都会有应用版本信息
		$apkVersion = $this->getInput('version');		
		
		$packageInfo = $this->getPackageInfo($apkInfo);		
		//分解apk_info 过滤无用的包信息
		$packageInfo = $this->filterPackage($packageInfo);
		//请求中没有游戏大厅游戏的包处理
		if(count($packageInfo) == 0){
			$this->output(0, "", array());
		}
		
		$data = $this->getOutputData ( $packageInfo);
		$this->output(0, "",  $data);
	}
	
	private function getOutputData($packageInfo) {
		
		$cache = Cache_Factory::getCache();
		$hashKey = Util_CacheKey::GAME_PACEKAGE_INFO;
		$webroot = Common::getWebRoot();
		$outputData = array();
		foreach ($packageInfo as $key => $value){
		    //get resource games
			$packagecrc  = $value['packagecrc'];
			$gameId   = $cache->hGet($hashKey, $packagecrc);		
			$gameInfo = $cache->get(Util_CacheKey::GAME_PACKAGE_DIFF_INFO.$gameId);
			//游戏不存在，不安装
			if (!$gameInfo){
				continue;
			} 
			//VersionCode比服务器VersionCode高或相等，不安装
			$versionCode = $value['versionCode'];
			if ($versionCode >= $gameInfo['version_code']){
				continue;
			} 
		
			$md5Code     = $value['md5Code'];
			$diffPackageInfo = $this->getDiffPackage($gameInfo, $md5Code);
			$upgradeType = $diffPackageInfo ? self::UPGRADE_DIFF : self::UPGRADE_ALL;			
			$outputData[] = $this->makeOutputData ( $webroot, $gameInfo, $diffPackageInfo, $upgradeType );

		}		
		return $outputData;	
	}
	
	private function makeOutputData($webroot, $gameInfo, $diffPackageInfo, $upgradeType) {
		//组装数据
		$outputData= array(
				"mTitle"=>'游戏详情',
				"mPackage"=>$gameInfo['package'],
				"mAppId"=>$gameInfo['id'],
				"mNewVersionCode"=>$gameInfo['version_code'],
				"mAppSize"=>$gameInfo['size'],
				"mNewVersionName"=>$gameInfo['version'],
				"mPageUrl"=>sprintf("%s/channel/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", $webroot, $gameInfo['id'], $upgradeType, self::getSource()),
				"mDownLoadUrl"=>$gameInfo['link'],
				"mSdkVersion"=>$gameInfo['min_sys_version_title'],
				"mResolution"=>sprintf("%s-%s", $gameInfo['min_resolution_title'], $gameInfo['max_resolution_title']),
				"mPatchUrl"=>($diffPackageInfo['link']) ? $diffPackageInfo['link'] : '',
				"mPatchSize"=>($diffPackageInfo['size']) ? $diffPackageInfo['size'] : 0
		);
		return $outputData;
	}


	
	/**取差分包信息*/
	private function getDiffPackage($game, $md5Code) {
		$diffPackageInfo = array();
		if ($md5Code) {
			$oldVersionId = $game['versions'][$md5Code];
			if ($oldVersionId) {
				//md5不相等，不用找差分包
				$diffPackageInfo = $game['diff'][$oldVersionId];
			}
		}
		return $diffPackageInfo;
	}
	
	/**
	 * 过滤无用的包信息
	 */
	private function filterPackage($pagekageInfo){
	    if(!is_array($pagekageInfo)){
	    	return ;
	    }
		
	    $cache = Cache_Factory::getCache();
	    $hashKey = Util_CacheKey::GAME_PACEKAGE_INFO;
		foreach($pagekageInfo as $val){
			if($cache->hExists($hashKey, $val['packagecrc'])) {
				$tmp[] = $val;
			}
		}
		return $tmp;
	}
	
	private function getPackageInfo($pagekageInfo){
		$pagekages = explode('|',  $pagekageInfo);
		$pagekageList = array();
		foreach ($pagekages as $val){
			$items = explode(":", $val);
			$packagecrc = crc32(trim($items[0]));
			$versionCode = trim($items[1]);
			$versionName = trim($items[2]);
			$md5Code = trim($items[3]);
			if($versionCode == '' || $md5Code == ''){
				continue;
			}
			$pagekageList[] = array('packagecrc'=>$packagecrc,
					                'versionCode'=>$versionCode,
					                'versionName'=>$versionName,
					                'md5Code'=>$md5Code
					);
		}
		return $pagekageList;
	}
	
	
	
}