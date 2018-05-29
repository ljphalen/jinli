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
		return true;
	}
	
	/**
	 * 关闭游戏
	 * @param unknown_type $info
	 * @param unknown_type $id
	 */
	public static function offGame($info,$id){
	    $ret = Resource_Service_Games::updateIdxGameResourceVersion($info,$id);
	    if(!$ret) return false;
		return true;
	}
	
	public static function getApkInfo($id,$apkUrl){
		return self::_getApkInfo($id, $apkUrl);
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
    				$info = Resource_Service_Attribute::getResourceAttribute ( $value );
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

