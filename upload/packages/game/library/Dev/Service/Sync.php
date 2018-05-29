<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class Dev_Service_Sync{
	
	/**
	 * 同步更新老游戏数据
	 * @param unknown $base_tmp
	 * @param unknown $simg
	 * @param unknown $game_id
	 * @param unknown $category
	 * @param unknown $labels
	 * @param unknown $device
	 * @param unknown $versions
	 * @param unknown $diffpackages
	 * @return boolean
	 */
	public static function updateGame($base_tmp, $simg, $game_id, $category, $labels, $versions, $diffpackages,$resolution){
		
		//更新存在的游戏的状态
		$devlop = 1;
		$upimg = array();
		$ret = Resource_Service_Games::updateResourceGamesIdx($base_tmp,$upimg, $simg, $game_id, $category, $labels, $resolution, $devlop);
		
		//删除该游戏的版本
		$check_versions = Resource_Service_Games::deleteIdxVersionByGameId(array('game_id'=>$game_id));
		//添加游戏的版本
		if($versions){
			$tmp = array();
			foreach($versions as $key=>$value){
				$value['game_id'] = $game_id;
				$ret = Resource_Service_Games::addIdxGameResourceVersion($value);
				if(!$ret) return false;
			}
		}
		
		//删除该游戏的拆分包
		$check_diffs = Resource_Service_Games::deleteIdxGameResourceDiffByGameId(array('game_id'=>$game_id));
		//添加游戏的查分包
		if($diffpackages){
			$temp = array();
			foreach($diffpackages as $k=>$v){
				$v['game_id'] = $game_id;
				$ret = Resource_Service_Games::addIdxGameResourcePackage($v);
				if(!$ret) return false;
			}
		}
		
		//同步运营中游戏的状态
		if($versions){
			foreach($versions as $key=>$value){
				$value['game_id'] = $game_id;
				$ret = Resource_Service_Games::updateIdxGameResourceVersion($value, $value['id']);
				if(!$ret) return false;
			}
		}
		//游戏缓存同步更新
		self::_changeDataNotify($game_id, 1);
		
		//上线联通免流量游戏内容操作
		self::_onlineCugdGame($game_id);
		
		return true;
	}
	
	/**
	 * 添加新的游戏
	 * @param unknown_type $info
	 * @param unknown_type $simg
	 * @param unknown_type $category
	 * @param unknown_type $labels
	 * @param unknown_type $device
	 * @return boolean
	 */
	public static function addNewGame($info, $simg,$category,$labels,$versions,$diffpackages,$resolution){
		if (!is_array($info)) return false;
		//添加游戏基本信息
		$game_id = Resource_Service_Games::addResourceGamesIdx($info, $simg,$category,$labels,$resolution);
		if(!$game_id) return false;
		//添加游戏的版本
		if($versions){
			$tmp = array();
			foreach($versions as $key=>$value){
				$value['game_id'] = $game_id;
				$ret = Resource_Service_Games::addIdxGameResourceVersion($value);
				if(!$ret) return false;
			}
		}
		//添加游戏的查分包
		if($diffpackages){
			$temp = array();
			foreach($diffpackages as $k=>$v){
				$v['game_id'] = $game_id;
				$ret = Resource_Service_Games::addIdxGameResourcePackage($v);
				if(!$ret) return false;
			}
		}
		
		//游戏缓存同步更新
		self::_changeDataNotify($game_id, 1);
		
		//上线联通免流量内容库操作
		self::_onlineCugdGame($game_id);	
			
		return $game_id;
	}
	
	/**
	 * 关闭游戏
	 * @param unknown_type $info
	 * @param unknown_type $id
	 */
	public static function offGame($info,$id){
	    $ret = Resource_Service_Games::updateIdxGameResourceVersion($info,$id);
	    if(!$ret) return false;
	    
	    //游戏缓存同步更新
	    self::_changeDataNotify($info['game_id'], 0);
	    
	    //同步下线联通免流量游戏操作
	  	self::_offCugdgame($info['game_id']);
	  	
	    return true;
	}
	
	public static function getApkInfo($id,$apkUrl){
		return self::_getApkInfo($id, $apkUrl);
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
		$language = array(
				'1' => "中文",
				'2' => "英文",
				'3' => "其他",
		);
		
		$url = $apkUrl . '?apkid='.$id;
		$curl = new Util_Http_Curl($url);
		$result = $curl->get();
		$tmp = array();
		$tmp = json_decode($result,true);
		$result = $tmp['data'];

		//获取数据
		if($result){
			$log['app_id'] =  $result['appid'];
			//游戏基本信息
			$img = explode('|',$result['icon']);
			$base_tmp = array(
					'appid'=> $result['appid'],
					'apkid'=> $result['apkid'],
					'name'=> $result['name'],
					'resume'=> htmlentities($result['resume']),
					'label'=> $result['label'],
					'img'=> $img[0],
					'mid_img'=> $img[1],
					'big_img'=> $img[2],
					'language'=> $language[$result['language']] ,
					'package'=> $result['package'],
					'packagecrc'=> crc32(trim($result['package'])),
					'price'=> self::_getData($result['price'] , 1),
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
			);
			

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
	
	private  function _getData($data,$type) {
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
	private function _convertSysVersion($api){
		$sdkverConfig = Common::getConfig("apiConfig", 'sdkver');
		$sysver = $sdkverConfig[$api];
		return $sysver;
	}
}

