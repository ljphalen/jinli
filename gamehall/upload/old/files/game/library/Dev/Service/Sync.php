<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class Dev_Service_Sync{
    public static $language = array(
            '1' => "中文",
            '2' => "英文",
            '3' => "其他",
    );
    
    public static function getApkInfo($id,$apkUrl){
        return self::_getApkInfo($id, $apkUrl);
    }

    /**
     * 游戏上线
     * @param unknown $id
     */
    public static function setGameOn($id) {
        $logFile = date('Y-m-d') . '_dev.on.log';
        $gameInfo = self::_getGameInfoFormDevPlatform($id);
        if(!$gameInfo) {
            Common::log(array("_getGameInfoFormDevPlatform({$id}) failed"), $logFile);
            return;
        }
        
        $gameInfoFromDB =  Resource_Service_Games::getBy(array('package'=>trim($gameInfo['package'])));
        $gameId = self::_updateResourceGamesInfo($gameInfo, $gameInfoFromDB);
        if ($gameId) {
            self::_setCorrelationTableStatusToOn($gameInfo, $gameInfoFromDB);
            
            $log = array('act' => '1');
            $log['app_id'] =  $gameInfo['appid'];
            $log['game_id'] = $gameId;
            $log['message'] ="app:{$gameInfo['appid']}上线同步";
            $log['status'] = 1;
            Resource_Service_Sync::add($log);
            echo 'ok';
            
            Async_Task::execute('Async_Task_Adapter_AfterGameOn', 'update', $gameId);
        }
        else {
            Common::log(array("_updateResourceGamesInfo() failed"), $logFile);
        }
    }
    
    /**
     * 游戏下线
     * @param unknown $id
     */
    public static function setGameOff($id) {
        $logFile = date('Y-m-d') . '_dev.off.log';
        $game = Resource_Service_Games::getBy(array('appid'=>$id));
        if(!$game['status']) {
            Common::log(array("game({$id})'s status is off"), $logFile);
            return;
        }
    
        $version = Resource_Service_Games::getIdxVersionByResourceGameId(array('game_id'=> $game['id'], 'status' => '1'));
        $info = $version[0];
        
        Resource_Service_Games::changeCorrelationTableStatus($info['game_id'], 0);
        
        $ret = Resource_Service_Games::updateIdxGameResourceVersion($info['id'], 0);
        if ($ret) {
            $log['app_id'] =  $id;
            $log['game_id'] = $game['id'];
            $log['message'] ="app:{$id}下线同步";
            $log['status'] = 1;
            Resource_Service_Sync::add($log);
            echo 'ok';
            
            Async_Task::execute('Async_Task_Adapter_AfterGameOff', 'update', $info['game_id']);
        }
        else {
            Common::log(array("_offGame() failed"), $logFile);
        }
    }
    
    /**
     * 更新游戏可编辑的属性
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateBaseResourceGames($data,$id ,$category ,$labels, $device, $version_id) {
        if (!is_array($data)) return false;
        //开始事务
        $trans = Common_Service_Base::beginTransaction();
        try {
            //更新游戏
            $ret = Resource_Service_Games::updateResourceGames($data, $id);
            if (!$ret) throw new Exception("Update Game fail.", -202);
             
            Resource_Service_Games::deleteResourceCategory(array('game_id'=>$id));
             
            $info = Resource_Service_Games::getResourceGames($id);
            self::_addCategoryData($category, $id, $info['status'], $info['online_time'], $info['downloads']);
            
            Resource_Service_Games::deleteResourceLabel($id);
            self::_addLabel($labels, $id);
    
            Resource_Service_Games::deleteResourceDevice(array('game_id'=>$id));
            Resource_Service_Games::mutiInsertResourceDevice($device, $id, $info['status']);
    
            //查找当前线上版本并且更新最后编辑时间
            $online_version = Resource_Service_Games::getIdxGameResourceVersion($version_id);
            if($online_version){
                $verData = array('update_time'=>Common::getTime());
                if($data['changes']) $verData['changes'] = $data['changes'];
                $ret = Resource_Service_Games::updateResourceVersion($verData, intval($version_id));
                if (!$ret) throw new Exception('Update Version fail.', -206);
            }
             
            //事务提交
            if($trans) {
                Common_Service_Base::commit();
                return true;
            }
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
            return false;
        }
    }
    
    /**
     * 更新关联表的状态
     * @param unknown $result
     * @param unknown $gameInfoFromDB
     */
	private static function _setCorrelationTableStatusToOn($result, $gameInfoFromDB){
	    if (!$gameInfoFromDB) return; // 全新游戏不用更新
	    
		$versions = $result['versions'];
		if($versions && !$gameInfoFromDB['status']){ // 如果游戏之前就是上线状态，则不需要更新
		    Resource_Service_Games::changeCorrelationTableStatus($gameInfoFromDB['id'], 1);
		}
	}
	
	/**
	 * 游戏缓存变更通知
	 * @param int $gameId
	 * @param int $status
	 * 
	 */
	private static function _changeDataNotify($gameId ,$status){
		 Resource_Service_Games::notifyCache($gameId ,$status);
	}
	
	/**
	 * 上线联通内容库程序
	 * @param int $gameId
	 */
	private static function _onlineCugdGame($gameId){
		$item = Freedl_Service_Cugd::getBy(array('game_id' => $gameId));
		if($item){
			//执行更新
			self::_upCugdgame($gameId);
		}else{
			//执行添加
			self::_addCugdgame($gameId);
		}
	}
	
	/**
	 * 更新联通免流量内容库数据
	 * @param intval $gameId
	 */
	private static function _upCugdgame($gameId){
		$item = Freedl_Service_Cugd::getBy(array('game_id' => $gameId));
		if($item) {
			//获取游戏内容库最新游戏数据
			$game = Resource_Service_Games::getGameAllInfo(array('id'=> $gameId), false, false);
			//免流量游戏表中（online_flag=1）
			$online= Freedl_Service_Cugd::getBy(array('game_id' => $gameId, 'online_flag' => 1));
			if($online){
				//免流量游戏表中（online_flag=1）versionCode与内容库versionCode判断 游戏状态变更
				if($game['version_code'] == $online['version_code']){
					//内容上线
					Freedl_Service_Cugd::updateCugd(array('game_status' => 1), array('id'=>$online['id']));
				}else{
					//内容下线
					Freedl_Service_Cugd::updateCugd(array('game_status' => 0), array('id'=>$online['id']));
				}
				
			}
			//免流量游戏表中（online_flag=0）
			$wait= Freedl_Service_Cugd::getBy(array('game_id' => $gameId, 'online_flag' => 0));
			//免流量游戏表中（online_flag=0）versionCode与内容库versionCode判断,完成游戏状态变更
			if($wait){
				if($game['version_code'] == $wait['version_code']){
					//内容上线
					Freedl_Service_Cugd::updateCugd(array('game_status' => 1), array('id'=>$wait['id']));
				} else {
					//内容下线
					Freedl_Service_Cugd::updateCugd(array('game_status' => 0), array('id'=>$wait['id']));
				}
			}
			//是否要增加或变更免流量表中游戏数据
			if($online && ($game['version_code'] <= $online['version_code'])) return;
			if($wait && ($game['version_code'] <= $wait['version_code'])) return;
			//防止要增加的审核游戏相同内容重复提交的操作
			$repeat = Freedl_Service_Cugd::getBy(array('game_id' => $gameId, 'version_code'=>$game['version_code'], 'online_flag' => 0));
			if($repeat) return;
			
			//提交高版本的游戏属于正常,即时更新联通内容库中游戏id为上线;
			//构造联通免流量游戏更新游戏数据
			$request = Freedl_Service_Cugd::fillData($game);
			//发送到联通内容更新接口
			$ret = Api_Freedl_Cu_Gd::updatecontent($gameId, $request);
			if($ret['errcode']=='0') {
				//获取该游戏（online_flag=0）的免流量游戏，确保该游戏有且仅有一条处于待审核的更新对应数据
				if($wait){
					//构造更新联通免流量资源库数据
					$upData = array(
							'version' => $game['version'],
							'version_code' => $game['version_code'],
							'cu_status' => 1,
							'game_status' => 1,
							'create_time' => Common::getTime(),
					);
					//更新记录到免流量资源表
					Freedl_Service_Cugd::updateCugd($upData, array('id' => $wait['id']));
				}else{
					//构造一条处于未上线的免流量游戏数据到联通内容库中
					$addData = array(
							'game_id'=> $gameId,
							'app_id' => $game['appid'],
							'version' => $game['version'],
							'version_code' => $game['version_code'],
							'content_id' => $item['content_id'],
							'cu_status' => 1,
							'game_status' => 1,
							'create_time' => Common::getTime(),
					);
					//增加记录到免流量资源表
					Freedl_Service_Cugd::addCugd($addData);
				}
			} else {
				Common::log($ret, date('Y-m-d').'sync-fail.log');
			}
		}
	}
	
	/**
	 * 添加联通内容库
	 * @param intval $gameId
	 */
	private static function _addCugdgame($gameId){
		$game = Resource_Service_Games::getGameAllInfo(array('id' => $gameId), false, false);
		$request = Freedl_Service_Cugd::fillData($game);
		$ret = Api_Freedl_Cu_Gd::addcontent($request);
		if($ret['errcode']=='0') {
			//构造联通免流量资源库数据
			$data = array(
					'game_id'=> $gameId,
					'app_id' => $game['appid'],
					'version' => $game['version'],
					'version_code' => $game['version_code'],
					'content_id' => $ret['resultData']['contentId'],
					'cu_status' => 1,
					'game_status' => 1,
					'create_time' => Common::getTime(),
			);
			//增加记录到免流量资源表
			Freedl_Service_Cugd::addCugd($data);
		}else{
			Common::log($ret, date('Y-m-d').'sync-fail.log');
		}
	}
	
	/**
	 * 下线联通内容库
	 * @param intval $gameId
	 */
	private static function _offCugdgame($gameId){
		//即时更新联通免流量资源的内容库状态为下线
		Freedl_Service_Cugd::updateCugd(array('game_status'=>0), array('game_id'=>$gameId));
	}
	
	/**
	 * 获取单个版本APK所有信息（不分上线下线状态）
	 * @return array
	 */
	private static function _getApkInfo($id,$apkUrl) {
		if(!$id)  exit;
		//获取数据
		$url = $apkUrl . '?apkid='.$id;
		$curl = new Util_Http_Curl($url);
		$result = $curl->get();
		$tmp = array();
		$tmp = json_decode($result,true);
		$result = $tmp['data'];

		//获取数据
		if($result){
			$log['app_id'] =  $result['appid'];
			$base_tmp = self::_getGameBaseInfo($result);
			
			//游戏截图缩略图
			$simg = explode('|',$result['imgs']);
			//游戏分类
			$category = explode('-',$result['category']);
			$category = self::_getData(implode('|',$category), 1);
			//游戏分辨率
			$resolution = explode('-',$result['resolution']);
			$resolution = self::_getData(implode('|',$resolution) ,1);

			//游戏主分类
			$mainCategoryData = Resource_Service_Attribute::getsBy(array('id'=>array('IN', array($result['category_p'],$result['category_p_son']))));
			$mainCategoryData = Common::resetKey($mainCategoryData, 'id');
			$temp['mainCategory'] = array(
					'parent' =>	array(
							'id'=> $result['category_p'],
							'title'=> $mainCategoryData[$result['category_p']]['title']
					),
					'sub'=> array(
							'id'=> $result['category_p_son'],
							'title'=> $mainCategoryData[$result['category_p_son']]['title']
					)
			);
			//游戏次分类
			if($result['category_s']){
				if($result['category_s_son']){
					$lessCategoryData = Resource_Service_Attribute::getsBy(array('id'=>array('IN', array($result['category_s'],$result['category_s_son']))));
					$lessCategoryData = Common::resetKey($lessCategoryData, 'id');
					$temp['lessCategory'] = array(
							'parent' =>	array(
									'id'=> $result['category_s'],
									'title'=> $lessCategoryData[$result['category_s']]['title']
							),
							'sub'=> array(
									'id'=> $result['category_s_son'],
									'title'=> $lessCategoryData[$result['category_s_son']]['title']
							)
					);
				}
				
			}
			
			//游戏标签属性
			/**
			* 
			*network_type    [联网类型]
			*character       [游戏特色]
			*billing_model   [资费方式]
			*detail_category [详细分类]
			*level           [游戏评级]
			*about           [内容题材]
			*style           [画面风格]
			*/

			$network_type = self::_getData($result['network_type']);
			$character = self::_getData($result['character']);
			$detail_category = self::_getData($result['detail_category']);
			$level = self::_getData($result['level']);
			$about = self::_getData($result['about']);
			$style = self::_getData($result['style']);
			//游戏版本
			$temp['base_attribute'] = $base_tmp;
			$temp['simg'] = $simg;
			$temp['category'] = $category;
			$temp['resolution'] = $resolution;
			$temp['network_type'] = $network_type;
			$temp['character'] = $character;
			$temp['detail_category'] = $detail_category;
			$temp['level'] = $level;
			$temp['about'] = $about;
			$temp['style'] = $style;
			$result['versions'][0]['min_sys_version'] = self::_convertSysVersion($result['versions'][0]['min_sys_version']);
			$temp['version'] = $result['versions'];
			return $temp;
		}
	}
	
	private static function _getData($data,$type=false) {
    	$labs = explode('|',$data);
    	$tmp = array();
    	foreach($labs as $key=>$value){
    		if($value){
    			$info = Resource_Service_Label::getLabel ($value);
    			if($type) {
    				$info = Resource_Service_Attribute::getBy(array('id'=>$value));
    			}
				$tmp[] = $info['title'];
    		}
		}
		$attr = implode(' ',$tmp);
		return $attr;
	}
	
	/**
	 * 最低支持版本数据转换
	 * @param $api
	 */
	private static function _convertSysVersion($api){
		$sdkverConfig = Common::getConfig("apiConfig", 'sdkver');
		$sysver = $sdkverConfig[$api];
		return $sysver;
	}
	
	private static function _getLabelData($data, $id) {
	    $tmp = array();
	    $labs = explode('|',$data);
	    foreach($labs as $key=>$value){
	        if($value){
	            $tmp[] = $id.'|'.$value;
	        }
	    }
	    return $tmp;
	}
	
	private static function _getGameInfoFormDevPlatform($id) {
	    $urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
	    $url = $urls[1] . '?appid='.$id;
	    $curl = new Util_Http_Curl($url);
	    $result = $curl->get();
	    $tmp = json_decode($result, true);
	    $result = $tmp['data'];
	    Common::log($result, date('Y-m-d') . '_dev.on.log');
	    return $result;
	}
	
	private static function _getGameLabel($result) {
	    /**
	     *network_type    [联网类型]
	     *character       [游戏特色]
	     *billing_model   [资费方式]
	     *detail_category [详细分类]
	     *level           [游戏评级]
	     *about           [内容题材]
	     *style           [画面风格]
	     */
	    $labelConfig =  Common::getConfig("apiConfig", 'label');
	    $labelConfig = (ENV == 'product') ? $labelConfig['product'] : $labelConfig['test'];
	    $networkType = self::_getLabelData($result['network_type'], $labelConfig['network_type']);
	    $character = self::_getLabelData($result['character'], $labelConfig['character']);
	    $detailCategory = self::_getLabelData($result['detail_category'], $labelConfig['detail_category']);
	    $level = self::_getLabelData($result['level'], $labelConfig['level']);
	    $about = self::_getLabelData($result['about'], $labelConfig['about']);
	    $style = self::_getLabelData($result['style'], $labelConfig['style']);
	
	    //合并处理
	    return array_merge($networkType, $character, $detailCategory, $level, $about, $style);
	}
	
	private static function _getGameBaseInfo($result) {
	    $img = explode('|',$result['icon']);
	    return array(
	            'appid'=> $result['appid'],
	            'apkid'=> $result['apkid'],
	            'name'=> $result['name'],
	            'resume'=> htmlentities($result['resume']),
	            'label'=> htmlentities($result['label']),
	            'img'=> $img[0],
	            'mid_img'=> $img[1],
	            'big_img'=> $img[2],
	            'language'=> self::$language[$result['language']] ,
	            'package'=> $result['package'],
	            'packagecrc'=> crc32(trim($result['package'])),
	            'price'=> $result['price'],
	            'company'=> $result['author_name'],
	            'descrip'=> htmlentities($result['descrip']),
	            'tgcontent'=> htmlentities($result['tgcontent']),
	            'create_time'=> $result['create_time'],
	            'online_time' => $result['online_time'],
	            'status'=> ($result['status'] == 3 ? 1 : 0),//3为已上线，否则未上线
	            'hot'=> $result['hot'],
	            'cooperate'=> $result['cooperate'],
	            'developer'=> $result['developer'],
	            'certificate'=> $result['safeInfos'] ? serialize($result['safeInfos']) : '' ,
	            'secret_key'=> trim($result['secret_key']),
	            'api_key'=> trim($result['api_key']),
	            'agent'=> trim($result['agent']),
	            'webp'=> $result['webp'],
	    );
	}
	
	private static function _getGameCategory($result) {
	    //游戏分类-旧版
	    //$category = explode('-',$result['category']);
	
	    //游戏主分类-v1.5.6
	    $category['mainParent'] = $result['category_p'];
	    $category['mainSub'] = $result['category_p_son'];
	    //游戏次分类-v1.5.6
	    $category['lessParent'] = $result['category_s'];
	    $category['lessSub'] = $result['category_s_son'];
	
	    return $category;
	}

    /**
     * 组装不同尺寸的游戏截图
     * @param $data
     * @return mixed
     */
    private static function getScreenImg($data){
        $imgData =array();
        $imgData[Resource_Service_Img::SCREEN_TYPE_480_800] = explode('|', $data['imgs']);
        if($data['big_imgs']) {
            $imgData[Resource_Service_Img::SCREEN_TYPE_720_1280] = explode('|', $data['big_imgs']);
        }
        return $imgData;
    }


	/**
	 * 更新游戏信息
	 * @param unknown $gameInfo 从开发者平台获取的游戏信息
	 * @return gameId 更新成功返回|false 更新不成功
	 */
	private static function _updateResourceGamesInfo($gameInfo, $gameInfoFromDB) {
	    $data = self::_getGameBaseInfo($gameInfo);       //游戏基本信息
	    if (!is_array($data)) return false;
	    
	    $img = self::getScreenImg($gameInfo);           //游戏截图缩略图
	    $category = self::_getGameCategory($gameInfo);   //游戏分类
	    $resolution = $gameInfo['resolution'];           //游戏分辨率
	    $labels = self::_getGameLabel($gameInfo);        //游戏标签属性
	    $versions = $gameInfo['versions'];               //游戏版本信息
	    $diffpackages = $gameInfo['diffpackages'];       //游戏的查分信息
	    $gameId = false;
	    
	    $trans = Common_Service_Base::beginTransaction();
	    try {
	        if ($gameInfoFromDB) { // 已有游戏更新
	            $gameId = $gameInfoFromDB['id'];
	            
	            $data = Resource_Service_Games::cookData($data);
    	        $data = self::filterData($data);
    	        $ret = Resource_Service_Games::updateResourceGames($data, $gameId);
    	        if (!$ret) throw new Exception("Update Game fail.", -202);
    	        
    	        self::_deleteGameResourceData($gameId);
    	        self::_addCategoryData($category, $gameId, $data['status'], $data['online_time'], -1);
	        }
	        else { // 首次上线的新游戏
	            $gameId = Resource_Service_Games::addResourceGames($data);
	            if (!$gameId) throw new Exception("Add Game fail.", -202);
	            
	            self::_addCategoryData($category, $gameId, 1, $data['online_time'], 0);
	        }
	        
	        self::_addLabel($labels, $gameId);
	        self::_addGameImg($img, $gameId);
	        self::_addResourceResolution($resolution, $gameId);
	        self::_addResouceVersion($versions, $gameId);
	        self::_addResourcePackage($diffpackages, $gameId);
	        
	        if($trans) {
	            Common_Service_Base::commit();
	        }
	    } catch (Exception $e) {
	        Common_Service_Base::rollBack();
	        print_r($e->getMessage());
	        return false;
	    }
	    
	    return $gameId;
	}

    /**
	 * 运营取消
	 * 一句话介绍(简述)，热词，小编八卦，游戏介绍
	 * 开发者平台做平台变更时内容的覆盖
	 * @param array $data
	 * @return array
	 */
	private static function filterData($data){
	    //取消一句话（简述）
	    unset($data['resume']);
	    //热词
	    unset($data['label']);
	    //小编八卦
	    unset($data['tgcontent']);
	    return $data;
	}

    /**
     * 添加游戏截图
     * @param $imgsData
     * @param  $id
     */
    private static function _addGameImg($imgsData, $id) {
        if($imgsData){
            foreach($imgsData as $type => $imgs) {
                if(empty($imgs)){
                    continue;
                }
                foreach ($imgs as $value) {
                    $gimgs[] = array('type' => $type, 'game_id' => $id, 'img' => $value);
                }
            }
	        Resource_Service_Img::addGameImg($gimgs);
	    }
    }

    /**
	 * 添加分类索引
	 * @param unknown $category
	 * @param unknown $id
	 * @param unknown $status
	 * @param unknown $onlineTime
	 * @param unknown $download
	 * @throws Exception
	 */
	private static function _addCategoryData($category, $id, $status, $onlineTime, $download = -1) {
	    //添加分类索引[新版本索引表加上线时间(online_time)，下载量(downloads)2个字段]
	    if($category){
	        if (-1 == $download) {
	            //获取该游戏当前的下载量
	            $downloads = Client_Service_WeekRank::getRankGameId($id);
	            $download = ($downloads) ? $downloads['DL_TIMES'] : 0;
	        }
	        $categorys = self::_cookIdxCategoryData($category, $id, $status, $onlineTime, $download);
	        foreach ($categorys as $value){
	            $ret = Resource_Service_Games::insertResourceCategory($value);
	            if (!$ret) throw new Exception('Add Category fail.', -205);
	        }
	    }
	}
	
	private static function _cookIdxCategoryData($data, $game_id, $status, $online_time, $downloads){
	    $tmp = array();
	    //主分类 一级分类数据
	    $tmp[0]=array(
	            'id'=>'',
	            'status' => 1,
	            'category_id' =>$data['mainSub'],
	            'parent_id' => $data['mainParent'],
	            'level' => 1,
	            'game_id' => $game_id,
	            'sort' => 0,
	            'game_status' => $status,
	            'online_time' => $online_time,
	            'downloads' => $downloads
	    );
	    //次分类数据
	    if($data['lessParent'] && $data['lessSub']){
	        if(($data['lessParent'] == $data['mainParent'])&&($data['lessSub'] == $data['mainSub'])){
	            return $tmp;
	        }else{
	            $tmp[1]=array(
	                    'id'=>'',
	                    'status' => 1,
	                    'category_id' => $data['lessSub'],
	                    'parent_id' => $data['lessParent'],
	                    'level' => 2,
	                    'game_id' => $game_id,
	                    'sort' => 0,
	                    'game_status' => $status,
	                    'online_time' => $online_time,
	                    'downloads' => $downloads
	            );
	        }
	    }
	
	    return $tmp;
	}
	
	/**
	 * 添加标签索引
	 * @param unknown $labels
	 * @param unknown $id
	 * @param unknown $status
	 * @throws Exception
	 */
	private static function _addLabel($labels, $id) {
	    if($labels){
	        $tmp = array();
	        foreach($labels as $key=>$value){
	            $lab = explode('|',$value);
	            $tmp[] = array(
	                    'id'=>'',
	                    'btype'=>$lab[0],
	                    'label_id'=>$lab[1],
	                    'game_id'=>$id,
	                    'status'=>1,
	                    'game_status'=>1, // 没有使用
	            );
	        }
	        $ret = Resource_Service_Games::mutiInsertResourceLabel($tmp);
	        if (!$ret) throw new Exception('Update Label fail.', -205);
	    }
	}
	
	/**
	 * 添加游戏分辨率索引
	 * @param unknown $resolution
	 * @param unknown $id
	 * @throws Exception
	 */
	private static function _addResourceResolution($resolution, $id) {
	    if($resolution){
	        $tmp = array();
	        $resolutions = explode('-',$resolution);
	        foreach($resolutions as $key=>$value){
	            $tmp[] = array(
	                    'id'=>'',
	                    'attribute_id'=>$value,
	                    'game_id'=>$id,
	                    'status'=>1,
	            );
	        }
	        $ret = Resource_Service_Games::mutiInsertResourceResolution($tmp);
	        if (!$ret) throw new Exception('Update Resolution fail.', -206);
	    }
	}
	
	/**
	 * 添加游戏的版本
	 * @param unknown $versions
	 * @param unknown $id
	 * @throws Exception
	 */
	private static function _addResouceVersion($versions, $id) {
	    if($versions){
	        $tmp = array();
	        foreach($versions as $key=>$value){
	            $value['game_id'] = $id;
	            $ret = Resource_Service_Games::addIdxGameResourceVersion($value);
	            if(!$ret) throw new Exception('Update Resolution fail.', -207);
	        }
	    }
	}
	
	/**
	 * 添加游戏的查分包
	 * @param unknown $diffpackages
	 * @param unknown $id
	 * @throws Exception
	 */
	private static function _addResourcePackage($diffpackages, $id) {
	    if($diffpackages){
	        $temp = array();
	        foreach($diffpackages as $k=>$v){
	            $v['game_id'] = $id;
	            $ret = Resource_Service_Games::addIdxGameResourcePackage($v);
	            if(!$ret) throw new Exception('Update Resolution fail.', -208);
	        }
	    }
	}
	
	/**
	 * @param unknown $gameId
	 */
	private static function _deleteGameResourceData($gameId) {
	    Resource_Service_Img::deleteGameImgByGameId(array('game_id'=>$gameId));
	    Resource_Service_Games::deleteResourceLabel($gameId);
	    Resource_Service_Games::deleteResourceResolution(array('game_id'=>$gameId));
	    Resource_Service_Games::deleteResourceCategory(array('game_id'=>$gameId));
	    Resource_Service_Games::deleteIdxVersionByGameId(array('game_id'=>$gameId));
	    Resource_Service_Games::deleteIdxGameResourceDiffByGameId(array('game_id'=>$gameId));
	}
	
	/**
	 * 游戏上线后，触发的逻辑
	 * @param unknown $gameId
	 */
	public static function afterGameOn($gameId) {
	    Util_Log::info("Dev_Service_Sync::afterGameOn", "Async.log", "gameId={$gameId}");
	    
	    self::_changeDataNotify($gameId, 1);

        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteListItem', $gameId);
        Async_Task::execute('Async_Task_Adapter_CategoryList', 'updateIndex', $gameId);

	    if (Util_Environment::isOnline()) {
	        self::_onlineCugdGame($gameId);
	    }
	}
	
	/**
	 * 游戏下线后，触发的逻辑
	 * @param unknown $gameId
	 */
	public static function afterGameOff($gameId) {
	    Util_Log::info("Dev_Service_Sync::afterGameOff", "Async.log", "gameId={$gameId}");
	    
	    self::_changeDataNotify($gameId, 0);
	    Resource_Service_GameExtraCache::delExtraById($gameId);
        Async_Task::execute('Async_Task_Adapter_GameListData', 'removeListItem', $gameId);
        Async_Task::execute('Async_Task_Adapter_CategoryList', 'removeIndex', $gameId);
        Async_Task::execute('Async_Task_Adapter_RankList', 'removeIndex', $gameId);
	    if (Util_Environment::isOnline()) {
	        self::_offCugdgame($gameId);
	    }
	}
	
}

