<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_UpdateController extends Api_BaseController {
	public $perpage = 50;

	/**
	 * 接收包名
	 * 新策略为3天内不重复处理任何更新请求
	 */
	public function updateAction() {
		$data = array();
		$webroot = Common::getWebRoot();
		$info = $this->getInput('apk_info');
		if (!$info) $this->output(-1, "");
		 
		//1.4.8之后都会有应用版本信息
		$apkVersion = $this->getInput('version');
		 
		//过滤无用的重复请求
		$cache = Common::getCache();
		 
		$data = explode('|',$info);
		$len = count($data);
		//分解apk_info 过滤无用的包信息
		$apkInfo = $this->_filterPackage($data);
		//请求中没有游戏大厅游戏的包处理
		if(count($apkInfo) == 0){
			$this->output(0, "", array());
		}
		 
		$tmp = array();
		foreach ($apkInfo as $key => $value){
			$type = "update";
			$patchs = array();
			$items = explode(":", $value);
			$packagecrc = crc32(trim($items[0]));
			$version_code = trim($items[1]);
			$version_name = trim($items[2]);
			$md5_code = trim($items[3]);
				
			if (!$md5_code) continue;
			//get resource games
			$cacheData = $cache->get("-all-package");
			$gameId = $cacheData[$packagecrc];
				
			$gkey = "-g-u-". $gameId;
			$game = $cache->get($gkey);
	
			//游戏不存在，不安装
			if (!$game) continue;
			//VersionCode比服务器VersionCode高或相等，不安装
			if ($version_code >= $game['version_code']) continue;
			//差分包信息
			if ($md5_code) {
				$old_version_id = $game['versions'][$md5_code];
				if ($old_version_id) {
					//md5不相等，不用找差分包
					$patchs = $game['diff'][$old_version_id];
					if($patchs) $type = "splitupdate";
				}
			}
				
			//组装数据
			$tmp[] = array(
					"mTitle"=>'游戏详情',
					"mPackage"=>$game['package'],
					"mAppId"=>$game['id'],
					"mNewVersionCode"=>$game['version_code'],
					"mAppSize"=>$game['size'],
					"mNewVersionName"=>$game['version'],
					"mPageUrl"=>sprintf("%s/channel/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", $webroot, $game['id'], $type, self::getSource()),
					"mDownLoadUrl"=>$game['link'],
					"mSdkVersion"=>$game['min_sys_version_title'],
					"mResolution"=>sprintf("%s-%s", $game['min_resolution_title'], $game['max_resolution_title']),
					"mPatchUrl"=>($patchs['link']) ? $patchs['link'] : '',
					"mPatchSize"=>($patchs['size']) ? $patchs['size'] : 0
			);
		}
		 
		$this->output(0, "",  $tmp);
	}
	
	/**
	 * 过滤无用的包信息
	 */
	private function _filterPackage($data){
		$ckey = "-all-package";
		$cache = Common::getCache();
		$cacheData = $cache->get($ckey);
		$tmp = array();
		if(!$cacheData) return $tmp;
		$list = array_keys($cacheData);
		$len = count($data);
	
		for($i=0; $i< $len-2; $i++){
			$info = explode(":", $data[$i]);
			$crcData = crc32(trim($info[0]));
			if(in_array($crcData, $list)) $tmp[] = $data[$i];
		}
		return $tmp;
	}
	
}