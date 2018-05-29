<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏上线版本数据缓存存储，数据采用hash结构存储。
 * 应用于前台服务器，提高游戏基本数据的响应速度。
 * @author fanch
 *
 */
class Resource_Service_GameCache extends Common_Service_Base{
	
	//过期时间为24小时
	const CACHE_EXPRIE = 86400;
	
    /**
     * 初始化游戏基本数据入缓存同时返回结果
     * @param $gameId
     */
    public static function saveGameDataToCache($gameId){
    	$gameInfo = $gameDiffs = $gameVersions = array();
    	$gameInfo = self::getGameInfoToCache($gameId);
    	if(!$gameInfo) return false;
    	//保存基本数据
    	self::saveGameInfoToCache($gameId, $gameInfo);
    	//保存差分包
    	$gameDiffs = self::getGameDiffToCache($gameId, $gameInfo['version_id']);
    	if($gameDiffs){
    		self::saveGameDiffToCache($gameId, $gameDiffs);
    	}
    	//保存历史版本
    	$gameVersions = self::getGameVersionsToCache($gameId);
    	if($gameVersions){
    		self::saveGameVersionsToCache($gameId, $gameVersions);
    	}
    	return  true;
    }

   
    
    /**
     * 删除下线的游戏数据
     * @param int $gameId
     */
    public static function deleteGameCache($gameId){
    	$infoCkey = ":" . $gameId . ":info";
    	self::_getCache()->delete($infoCkey);
    	$infoCkey = ":" . $gameId . ":diffs";
    	self::_getCache()->delete($infoCkey);
    	$infoCkey = ":" . $gameId . ":versions";
    	self::_getCache()->delete($infoCkey);
    }
    
    /**
     * 获取游戏基本信息
     * @param int $gameId
     * @return 
     */
    public static function getGameInfoFromCache($gameId){
    	$data = array();
    	$ckey = ":" . $gameId . ":info";
    	$data = self::_getCache()->hGetAll($ckey);
   		 if(empty($data)){
			$data = self::getGameInfoToCache($gameId);
			if($data){
				self::saveGameInfoToCache($gameId, $data);
			}
		 }
		return $data;
    }
    
    /**
     * 获取游戏线上版本差分包
     * @param int $gameId
     * @param int $versionId
     * @return 
     */
    public static function  getGameDiffFromCache($gameId, $versionId){
    	$data = array();
    	$ckey = ":" . $gameId . ":diffs";
    	$data = self::_getCache()->hGetAll($ckey);
    	if(empty($data)){
    		//保存差分包
    		$data = self::getGameDiffToCache($gameId, $versionId);
    		if($data){
    			self::saveGameDiffToCache($gameId, $data);
    		}
    	}
    	return $data;
    }
    
    /**
     * 游戏所有的版本数据
     * @param int $gameId
     * @return 
     */
    public static function getGameAllVersionFromCache($gameId){
    	$data = array();
    	$ckey = ":" . $gameId . ":versions";
    	$data = self::_getCache()->hGetAll($ckey);
    	if(empty($data)){
    		$data = self::getGameVersionsToCache($gameId);
    		if($data){
    			self::saveGameVersionsToCache($gameId, $data);
    		}
    	}
    	return $data;
    }
	
	/**
	 * 获取游戏主表基础数据
	 * @param int $gameId
	 * @return  array
	 * 
	 */
	private static function _getGameRerouceData($gameId){
		$result = array();
		$game =  Resource_Service_Games::getBy(array('id' => $gameId, 'status' => 1));
		foreach ($game as $key=>$value){
			$result[$key] = $value;
		}
		return $result;
	}

	/**
	 * 游戏分类
	 * @param int $gameId
	 * @return 
	 */
	private static function _getGameCategoryData($gameId){
		$result = array();
		//游戏主分类游戏数据
		$mainCategoryData =  Resource_Service_GameCategory::getBy(array('game_id'=>$gameId, 'level'=>1, 'status'=>1));
		$categoryIds = array($mainCategoryData['parent_id'], $mainCategoryData['category_id']);
		//游戏次分类数据
		$lessCategoryData =  Resource_Service_GameCategory::getBy(array('game_id'=>$gameId, 'level'=>2, 'status'=>1));
		//取主分类一级分类id
		$result['category_id'] = $mainCategoryData['parent_id'];
		//v1.5.6增加主分类
		$result['mainCategory'] = json_encode(array($mainCategoryData['parent_id'],	$mainCategoryData['category_id']));
		//v1.5.6增加次分类
		if($lessCategoryData){
			$result['lessCategory'] = json_encode(array($lessCategoryData['parent_id'], $lessCategoryData['category_id']));
		} else {
			$result['lessCategory'] = json_encode(array());
		}
		return $result;
	}
	
	/**
	 * 游戏支持外部设备
	 * @return int
	 */
	private static function _getGameDeviceData($gameId){
		$result = array();
		$device = Resource_Service_Games::getIdxGameResourceDeviceBy(array('game_id' => $gameId));
		$result['device'] = empty($device) ? 0 : 1;
		return $result;
	}
	
	/**
	 * 游戏截图处理
	 * @param int $gameId
	 * @return array
	 */
	private static function _getGameImgData($gameId){
		//游戏详情图片
		$result = $gimgs = $simgs = array();
		list(, $imgData) = Resource_Service_Img::getList(1, 10, array('game_id'=>$gameId));
		foreach($imgData as $key=>$value) {
			$file = array();
			//大图
			$gimgs[] = $value['img'];
			$file = explode(".", basename($value['img']));
			//小图
			$simgs[] = $value['img'] . '_240x400.' . $file[1];
		}
		$result= array(
				'gimgs' => json_encode($gimgs),
				'simgs' => json_encode($simgs),
		);
		return $result;
	}
	
	/**
	 * 获取游戏线上版本数据
	 * @param int $gameId 游戏id
	 * @param string $ckey 缓存key
	 *
	 */
	private static function _getOnlineVersionData($gameId){
		$result = array();
		$version = Resource_Service_Games::getGameVersionInfo($gameId);
		//存储数据
		$result['version_id'] = $version['id'];
		$result['version_code'] = $version['version_code'];
		$result['size'] = $version['size'];
		$result['version'] = $version['version'];
		$result['md5_code'] = $version['md5_code'];
		$result['link'] = $version['link'];
		$result['min_sys_version'] = $version['min_sys_version'];
		$result['min_resolution'] = $version['min_resolution'];
		$result['max_resolution'] = $version['max_resolution'];
		$result['status'] = $version['status'];
		$result['vcreate_time'] = $version['create_time'];
		$result['update_time'] = $version['update_time'];
		$result['changes'] = $version['changes'];
		$result['signature_md5'] =  $version['fingerprint'] ? self::_formartStr($version['fingerprint']) : '';
		return $result;
	}

	/**
	 * 通过游戏id获取游戏基本数据
	 * @param int $gameId
	 * @return array:
	 */
	private static function getGameInfoToCache($gameId){
		$data = array();
		//游戏主表数据
		$gameData = self::_getGameRerouceData($gameId);
		if(!$gameData) return $data;
		$data = array_merge($data, $gameData);
		//游戏分类
		$categoryData = self::_getGameCategoryData($gameId);
		$data = array_merge($data, $categoryData);
		//外设支持
		$deviceData = self::_getGameDeviceData($gameId);
		$data = array_merge($data, $deviceData);
		//游戏线上版本数据
		$versionData = self::_getOnlineVersionData($gameId);
		$data = array_merge($data, $versionData);
		//游戏截图
		$imgData = self::_getGameImgData($gameId);
		$data = array_merge($data, $imgData);
		return $data;
	}
	

	/**
	 * 保存基本数据到缓存
	 * @param int $gameId
	 * @param array $data
	 */
	public static function saveGameInfoToCache($gameId,$data){
		$ckey = ":" . $gameId . ":info";
		self::_getCache()->delete($ckey);
		foreach ($data as $key=>$value){
			self::_getCache()->hSet($ckey, $key, $value);
		}
		self::setCaheExpire($ckey);
	}
	
	/**
	 * 通过游戏id与上线的版本获取对应版本的差分包
	 * @param int $gameId
	 * @param int $versionId
	 * @return array
	 */
	private static function getGameDiffToCache($gameId, $versionId){
		$diffs = array();
		$diffs = Resource_Service_Games::getsByIdxDiff(array('game_id'=>$gameId, 'version_id'=> $versionId));
		return $diffs;
	}
	
	/**
	 * 保存游戏与上线版本数据的差分包信息
	 * @param int $gameId 游戏id
	 * 
	 */
	public static function saveGameDiffToCache($gameId, $data){
		$ckey = ":" . $gameId . ":diffs";
		self::_getCache()->delete($ckey);
		foreach ($data as $key=>$value){
				self::_getCache()->hSet($ckey, $value['object_id'], json_encode($value));
			}
		self::setCaheExpire($ckey);
	}
	
	/**
	 * 通过游戏id获取所有的版本数据
	 * @param int $gameId
	 * @return array
	 */
	private static function getGameVersionsToCache($gameId){
		$versions = array();
		$versions = Resource_Service_Games::getIdxVersionByGameId($gameId);
		return $versions;
	}
	
	/**
	 * 保存游戏版本历史版本数据入缓存
	 * @param int $gameId
	 */
	public static function saveGameVersionsToCache($gameId, $data){
		$ckey = ":" . $gameId . ":versions";
		self::_getCache()->delete($ckey);
		foreach ($data as $key => $value) {
			self::_getCache()->hSet($ckey, $value['id'], json_encode($value));
		}
		self::setCaheExpire($ckey);
	}

	/**
	 * 包中数字签名字符串格式化处理
	 * @param string $str
	 */
	private static function _formartStr($strContent){
		$flag = stristr($strContent, ',');
		$strMd5 = "";
		if($flag == false){
			//单个签名
			$strMd5=md5($strContent);
		}else{
			//多个签名
			$tmpArr=explode(',',$strContent);
			sort($tmpArr, SORT_STRING);//值按字符串升序排列
			$strMd5 = md5(implode('', $tmpArr));
		}
		return $strMd5;
	}
	
	/**
	 * 设置key过期时间
	 * @param string $ckey
	 * @param int $time
	 */
	private static function setCaheExpire($ckey, $time=self::CACHE_EXPRIE){
		self::_getCache()->expire($ckey, $time);
	}
	
	/**
	 * 获取cache实例
	 */
	private static function _getCache() {
		return Common::getCache();
	}
}