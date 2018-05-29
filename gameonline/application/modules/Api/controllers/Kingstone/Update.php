<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_UpdateController extends Api_BaseController {
	public $perpage = 50;
    
    /**
     * 接收包名
     */
    public function updateAction() {
    	$data = array();
    	$webroot = Common::getWebRoot();
     	$info = $this->getInput('apk_info');
    	if (!$info) $this->output(-1, "");
    	
    	//1.4.8之后都会有应用版本信息
    	$apkVersion = $this->getInput('version');
    	
    	//过滤无用的重复请求
    	$ckey = "-up-".crc32($info);
    	$cache = Common::getCache();
    	$cdata = $cache->get($ckey);
    	if ($cdata) exit;
    	
    	$data = explode('|',$info);
    	$len = count($data);
    	//分解apk_info 过滤无用的包信息
    	$apkInfo = $this->_filterPackage($data);
		//请求中没有游戏大厅游戏的包处理
    	if(count($apkInfo) == 0){ 
    		if($apkVersion){
    			//1.4.8之后版本处理方式
    			$this->output(0, "", array());
    		} else {
    			//1.4.8之前版本处理方式
    			$cache->set($ckey, 1, 60 * 60 * 26);
    		}
    	}

    	//队列
    	$queue = Common::getQueue();
    	$qlen  = $queue->len('game-queue');
    	//首次入队列初始化队列长度
    	if($qlen == 0) $qlen = 1;
    	//队列超过限制处理
    	if($qlen > 10){
    		$queue->noRepeatPop("game-queue", "k");
    		exit; 
    	}

    	//锁操作
    	$lockObj = Common::getLockHandle();
    	//循环加锁操作
    	for($i=1; $i <= 10; $i++){
    		$lockName = 'GIONEE_KINGSTONE_UP_LOCK_'. $i;
    		$flag = $lockObj->lock($lockName);
    		if($flag) break;
    	}
    	if($flag == false) exit;
    	
    	//入队列操作
    	$queue->noRepeatPush("game-queue", crc32($info), "k");

    	$rom_resolution = explode('*', $data[$len-1]);
    	//分辨率宽高
    	$rom_width = $rom_resolution[0];
    	$rom_height = $rom_resolution[1];
    	//系统版本
    	$rom_sys_version = $data[$len-2];
    	
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
			$game = Resource_Service_Games::getGameAllInfo(array('packagecrc'=>$packagecrc));
			
			//游戏不存在，不安装
			if (!$game) continue;
			//VersionCode比服务器VersionCode高或相等，不安装
			if ($version_code >= $game['version_code']) continue;
			//游戏系统版本比客户端系统版本高，不安装
			if (strnatcmp($game['min_sys_version_title'], $rom_sys_version) > 0) continue;
			//版本号（Versionname)高于或等于服务器版本号（Versionname),不安装
			if (strnatcmp($version_name, $game['version']) >= 0) continue;
 			
			//手机分辨率或系统版本不在游戏支持分辨率和系统版本范围,不安装
			$min_resulotion = explode("*", $game['min_resolution_title']);
			$max_resulotion = explode("*", $game['max_resolution_title']);
			
			$min_width = $min_resulotion[0];
			$max_width = $max_resulotion[0];
			
			$min_height = $min_resulotion[1];
			$max_height = $max_resulotion[1];
			
			//分辨率判断  game_min_width < rom_width < game_min_width  && game_min_height <rom_height< game_min_height
			/*
			if ($rom_width < $min_width) continue;
			if ($rom_width > $max_width) continue;
			
			if ($rom_height < $min_height) continue;
			if ($rom_height > $max_height) continue;
			*/
			
    		$versions = Resource_Service_Games::getIdxVersionByGameId($game['id']);
			if (!$versions) continue;
			
			foreach ($versions as $key=>$value) {
				//md5不相等，不用找差分包
				if ($value['md5_code'] == $md5_code) {
					$patchs = Resource_Service_Games::getIdxDiffByVersionId($game['version_id'], $value['id']);
					if($patchs) $type = "splitupdate";
					break;
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
					"mPageUrl"=>sprintf("%s/kingstone/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", $webroot, $game['id'], $type, self::getSource()),
					"mDownLoadUrl"=>$game['link'],
					"mSdkVersion"=>$game['min_sys_version_title'],
					"mResolution"=>sprintf("%s-%s", $game['min_resolution_title'], $game['max_resolution_title']),
					"mPatchUrl"=>($patchs['link']) ? $patchs['link'] : '',
					"mPatchSize"=>($patchs['size']) ? $patchs['size'] : 0
					);
		}

		//出队列操作
		if($qlen) $queue->noRepeatPop("game-queue", "k");
		
		//解锁操作
		$lockObj->unlock($lockName);
		
		//值为空记录
		if (empty($tmp)){
			if($apkVersion){
    			//1.4.8之后版本处理方式
    			$this->output(0, "", array());
    		} else {
    			//1.4.8之前版本处理方式
    			$cache->set($ckey, 1, 60 * 60 * 26);
    		}
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
     	$len = count($data);
    	for($i=0; $i< $len-2; $i++){
    		$info = explode(":", $data[$i]);
    		$crcData = crc32(trim($info[0]));
    		if(count($cacheData)) {
    			 if(in_array(strval($crcData), $cacheData)) $tmp[] = $data[$i];
    		} else {
      			$tmp[] = $data[$i];
    		}
    	}
    	return $tmp;	
    }
    
   
}