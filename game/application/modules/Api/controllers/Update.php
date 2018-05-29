<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UpdateController extends Api_BaseController {
	const UPGRADE_ALL = "update";//整包更新
	const UPGRADE_DIFF = "splitupdate";//差分包更新
	private $webroot = "";
	private $cache = null;
    
    /**
     * 检查客户端游戏是否有新版本
     * 新策略为3天内不重复处理任何更新请求
     */
    public function updateAction() {
    	//1.4.8之后都会有应用版本信息
    	$apkVersion = $this->getInput('version');
        //$this->saveUpdateBehaviour($apkVersion);

     	$info = $this->getInput('apk_info');
    	if (!$info) $this->output(-1, "");
    	$this->webroot = Common::getWebRoot();
    	$this->cache = Cache_Factory::getCache();
    	
    	
    	//分解apk_info 过滤无用的包信息
    	$apkInfo = $this->getPackageInfo($info);
    	$apkInfo = $this->extractGames($apkInfo);
        //检查合法性
    	$this->checkApkInfo($apkInfo, $apkVersion);
    	//1.5.7增加手机内存判断
    	$phoneRam = $this->getInput("phone_ram");
    	//手机内存峰值
    	$maxRamValue = $this->getMaxRamValue($phoneRam);
    	
    	//获取游戏升级的数据
    	$outputData = $this->getOutputData($apkInfo, $maxRamValue, $info, $apkVersion);
		//增加百分比提示
		$special = $this->getPercent();
		
		$this->upOutput(0, "",  $outputData,  $special);
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
    
	private function saveUpdateBehaviour($clientVersion) {
		if (strnatcmp($clientVersion, '1.4.9.s') < 0) {
			/*Client has a deffect before 1.4.9.s, all clients access Update API at the same time*/
			return;
		}

		$imei = trim($this->getInput('imei'));
		if (!$imei) {
			$sp = $this->getInput('sp');
			$imei = Common::parseSp($sp, 'imei');
		}
		$behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_HALL);
        $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_HIGH_VER_GAME);
	}


    private function extractGames($apkInfo){
	    if(!is_array($apkInfo)){
	    	return ;
	    }		
	    $hashKey = Util_CacheKey::GAME_PACEKAGE_INFO;
	    $tmp = array();
		foreach($apkInfo as $val){
			if($this->cache->hExists($hashKey, $val['packagecrc'])) {
				$tmp[] = $val;
			}
		}
		return $tmp;
	}
    
    private function checkApkInfo($apkInfo, $apkVersion) {
    	//默认游戏大厅1.4.9.s版本信息-该版本已修复8点集中请求升级接口的问题
    	$default = $this->getDefaultOutput();
        //请求中没有游戏大厅游戏的包处理
        if(count($apkInfo) == 0) {
            //1.4.9之前版本特殊处理 用于应用外发包 防止外发包推游戏大厅的问题
            $default = (strnatcmp($apkVersion, '1.4.9') < 0) ? $default : array();
            $this->output(0, "", array($default));
        }
    }
    
    /**
     * 获取手机内存对应差分包与原始包之和的峰值（M）
     * @param unknown $phoneRam
     */
    private function getMaxRamValue($phoneRam) {
        if (!$phoneRam || !intval($phoneRam)) return 0;
        /**
         如果实际差分包与原始包之和没有超过该手机内存对应的峰值，就返回有差分升级！
         手机内存（M）        差分包与原始包之和的峰值（M）
         512MB, 1GB, 2GB内存配置的手机； 对 游戏原始包+差分包的大小 在 50MB以下、50-100MB， 100-150MB， 150MB
         */
        $maxRamValue = PHP_INT_MAX;
        if ($maxRamValue)
            return $maxRamValue;//1.5.7暂时不需要用内存判断
        
        $list = Resource_Service_Upgrade::getUpgradeCacheData();
        foreach ($list as $upgrade) {
            if($phoneRam >= $upgrade['phone_ram_min'] && $phoneRam<$upgrade['phone_ram_max']) {
                $maxRamValue = $upgrade['max_apk'];
                break;
            }
        }
        
        return $maxRamValue;
    }
    
    /**获取游戏升级的数据*/
    private function getOutputData($apkInfo, $maxRamValue, $info, $apkVersion) {
        $outputData = array();
        foreach ($apkInfo as $apk) {
                      
            //游戏相关信息
            $packagecrc  = $apk['packagecrc'];
            $gameInfo = $this->getPackageGameInfo($packagecrc);
            //游戏不存在，不安装
            if (!$gameInfo) continue;
            //VersionCode比服务器VersionCode高或相等，不安装
            $versionCode = $apk['versionCode'];
            if ($versionCode >= $gameInfo['version_code']) continue;
            // 差分包信息
            $md5Code   = $apk['md5Code'];
            $diffPackageInfo = $this->getDiffPackage($gameInfo, $md5Code);
            //如果查分包合法采用查分更新：1.5.7之前
            $upgradeType = $diffPackageInfo ? self::UPGRADE_DIFF : self::UPGRADE_ALL;
            //1.5.7之后，根据手机内存判断是否采用差分升级：add by wupeng
            $diffUpgradeFlag = $this->checkDiffUpgrade($maxRamValue, $gameInfo, $diffPackageInfo, $upgradeType);
            
            //组装数据
            $outputData[] = $this->sucOutput($diffUpgradeFlag, $gameInfo, $diffPackageInfo);
        }
        //处理1.4.6之前版本游戏大厅包名信息不存在的特殊处理 + 游戏外发包推默认游戏大厅版本的问题
        if ((strstr($info, 'gn.com.android.gamehall') == false) &&  (strnatcmp($apkVersion, '1.4.9') < 0)) {
            $default = $this->getDefaultOutput();
            array_push($outputData, $default);
        }
        return $outputData;
    }
    
    /**百分比信息*/
    private function getPercent() {
        $startPer = Game_Service_Config::getValue('game_start_per');
        $startPer = $startPer ? intval($startPer) : 0;
        $endPer = Game_Service_Config::getValue('game_end_per');
        $endPer = $endPer ? intval($endPer) : 0;
        $percent = rand($startPer, $endPer);
        $special = array('leadPercent'=>$percent);
        return $special;
    }
    
    /**获取游戏信息*/
    private function getPackageGameInfo($packagecrc) {
    	$gameId = $this->cache->hGet(Util_CacheKey::GAME_PACEKAGE_INFO, $packagecrc);
    	$gameInfo = $this->cache->get(Util_CacheKey::GAME_PACKAGE_DIFF_INFO.$gameId);
        return $gameInfo;
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
    
    /**是否差分升级标识*/
    private function checkDiffUpgrade($maxRamValue, $game, $patchs, $type) {
        //1.5.7版本增加包大小的二次判断：原始包和差分包大小和
        $diffUpgradeFlag = ($type == "splitupdate" && $patchs['link'] && $patchs['size']);
        if($diffUpgradeFlag && $maxRamValue > 0) {
            $apkSize = $game['size'] + $patchs['size'];
            $diffUpgradeFlag = $apkSize < $maxRamValue;
        }
        return $diffUpgradeFlag;
    }
    
    //升级接口特殊输出
    private function upOutput($code, $msg = '',  $data = array(), $special) {
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $code == 0 ? true : false ,
    			'msg' => $msg,
    			'leadPercent'=>($special['leadPercent']) ? $special['leadPercent'] . '%' : '',
    			'data' => $data
    	)));
    }
   
    private function getDefaultOutput() {
    	$default = array(
    			"mTitle"=>'游戏详情',
    			"mPackage"=>'gn.com.android.gamehall',
    			"mAppId"=>'117',
    			"mNewVersionCode"=> '1020001018',
    			"mAppSize"=>'1.79',
    			"mNewVersionName"=>'1.4.9.s',
    			"mPageUrl"=>sprintf("%s/client/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", 'http://game.gionee.com', '117', 'update', self::getSource()),
    			"mDownLoadUrl"=>'http://gamedl.gionee.com/Attachments/dev/apks/2014/04/12/1397270209351.apk',
    			"mSdkVersion"=>'1.6',
    			"mResolution"=>sprintf("%s-%s", '240*320', '1080*1920'),
    			"mPatchUrl"=> '',
    			"mPatchSize"=> 0,
    			"mChanges" => "",
    	);
        return $default;    	
    }
    
    private function getReward($gameId) {
        return Resource_Service_GameData::_getRewardFlag($gameId);
    }
    
    /**游戏可以升级的数据*/
    private function sucOutput($diffUpgradeFlag, $game, $diffPackageInfo) {
        $type = $diffUpgradeFlag ? self::UPGRADE_DIFF : self::UPGRADE_ALL;
        $output = array(
                "mTitle"=>'游戏详情',
                "mPackage"=>$game['package'],
                "mAppId"=>$game['id'],
                "mNewVersionCode"=>$game['version_code'],
                "mNewVersionName"=>$game['version'],
                "mPageUrl"=>sprintf("%s/client/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", $this->webroot, $game['id'], $type, self::getSource()),
                 
                "mAppSize"=>$game['size'],//原始包大小
                "mDownLoadUrl"=>$game['link'],//原始包地址
                	
                "mIconUrl" => $game['img'],
                "mSdkVersion"=>$game['min_sys_version_title'],
                "mResolution"=>sprintf("%s-%s", $game['min_resolution_title'], $game['max_resolution_title']),
                 
                "mPatchUrl"=>$diffUpgradeFlag ? $diffPackageInfo['link'] : '',//差分包地址
                "mPatchSize"=>$diffUpgradeFlag ? $diffPackageInfo['size'] : 0,//差分包大小
                	
                "mChanges" => $game['changes'],
                "freedl" => $game['freedl'],
                "reward" => $this->getReward($game['id'])
        );
        return $output;
    }
    
    
}
