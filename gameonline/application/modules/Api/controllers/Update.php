<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UpdateController extends Api_BaseController {
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

    	$cache = Common::getCache();
    	
    	$data = explode('|',$info);
    	$len = count($data);
    	//分解apk_info 过滤无用的包信息
    	$apkInfo = $this->_filterPackage($data);
    	
    	//默认游戏大厅1.4.9.s版本信息-该版本已修复8点集中请求升级接口的问题
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
    	
    	
		//请求中没有游戏大厅游戏的包处理
    	if(count($apkInfo) == 0){ 
    		//1.4.9之前版本特殊处理 用于应用外发包 防止外发包推游戏大厅的问题
    		$default = (strnatcmp($apkVersion, '1.4.9') < 0) ? $default : array();
    		$this->output(0, "", array($default));
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
			
			//get resource games
			$cacheData = $cache->get("-all-package");
			$gameId = $cacheData[$packagecrc];
			
			$gkey = "-g-u-". $gameId;
			$game = $cache->get($gkey);
		
			//游戏不存在，不安装
			if (!$game) continue;
			//VersionCode比服务器VersionCode高或相等，不安装
			if ($version_code >= $game['version_code']) continue;
			
			// 差分包信息
			if ($md5_code){
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
					"mPageUrl"=>sprintf("%s/client/index/detail/?id=%d&pc=1&intersrc=%s&t_bi=%d", $webroot, $game['id'], $type, self::getSource()),
					"mDownLoadUrl"=>$game['link'],
					"mIconUrl" => $game['img'],
					"mSdkVersion"=>$game['min_sys_version_title'],
					"mResolution"=>sprintf("%s-%s", $game['min_resolution_title'], $game['max_resolution_title']),
					"mPatchUrl"=>($patchs['link']) ? $patchs['link'] : '',
					"mPatchSize"=>($patchs['size']) ? $patchs['size'] : 0,
					"mChanges" => $game['changes']
					);
		}

		//处理1.4.6之前版本游戏大厅包名信息不存在的特殊处理 + 游戏外发包推默认游戏大厅版本的问题
		if ((strstr($info,'gn.com.android.gamehall') == false) && (strnatcmp($apkVersion, '1.4.9') < 0)) array_push($tmp, $default);
		
		//增加百分比提示
		$startPer = Game_Service_Config::getValue('game_start_per');
		$startPer = $startPer ? intval($startPer) : 0;
		$endPer = Game_Service_Config::getValue('game_end_per');
		$endPer = $endPer ? intval($endPer) : 0;
		$percent = rand($startPer, $endPer);
		$special = array('leadPercent'=>$percent);
		
		$this->_upOutput(0, "",  $tmp, $special);
    }
    
    //升级接口特殊输出
    private function _upOutput($code, $msg = '',  $data = array(), $special) {
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $code == 0 ? true : false ,
    			'msg' => $msg,
    			'leadPercent'=>($special['leadPercent']) ? $special['leadPercent'] . '%' : '',
    			'data' => $data
    	)));
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