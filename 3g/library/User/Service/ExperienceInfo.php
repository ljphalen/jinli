<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_ExperienceInfo  {

	public static  function add($params){
		if(empty($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}


	public static function getList($page,$pageSize,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList($pageSize*($page-1),$pageSize,$where,$orderBy));
	}

	public static function get($id){
		if(!is_numeric($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params =array()){
		if(!is_array($params)) return false;
		return  self::_getDao()->getBy($params);
	}
	public static function getsBy($params=array(),$order= array()){
		if(!is_array($params)) return false;
		return  self::_getDao()->getsBy($params,$order);
	}
	public static function update($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}

	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	public static function getVoipLevelSeconds($level,$minutes){
		$preMinutes = 0;
		if($level> 1){
			$preLevelData = User_Service_ExperienceInfo::getInitLevelData($level -1 );
			foreach ($preLevelData as $m=>$n){
				if($n['reward_type'] == 3){
					$preMinutes = $n['num'];
				}
			}	
		}
		return ($minutes - $preMinutes) *60;
	}
	
	/**
	 * 经验等级赠送信息
	 */
	public static function getLevelRewardsData($curLevel=1,$rewardType){
		$key = "USER:LEVEL:REWARDS:NUM:{$curLevel}:{$rewardType}";
		$num = Common::getCache()->get($key);
		if(empty($num)){
			$data = User_Service_ExperienceInfo::getBy(array('status'=>1,'level'=>$curLevel));
			if(!$data) return false;
			$levelData = json_decode($data['level_msg'],true);
			foreach ($levelData as $k=>$v){
				if($v['reward_type'] == $rewardType){
					$num  = $levelData[$k]['num'];
					break;
				}
			}
			Common::getCache()->set($key,$num,60);
		} 
		return  $num?$num:0;
	}
	
	public static function getInitLevelData($level){
		$data = User_Service_ExperienceInfo::getBy(array('level'=>$level));
		$levelMsg = json_decode($data['level_msg'],true);
		return $levelMsg;
	}
	
	public static function getLevelMsg($level=1){
		$data = self::getBy(array('level'=>$level));
		$levelMsg = json_decode($data['level_msg'],true);
		$msg ='';
		foreach ($levelMsg  as $k=>$v){
			$catData = User_Service_ExperienceType::get($v['cat_id']);
			$msg .= sprintf($catData['name'],$levelMsg['num']);
		}
		return $msg;
	}
	
	public static function getLevelPrivilegeDetailData($level){
		$key = "USER:LEVEL:REWARD:DETAIL:{$level}";
		$data = Common::getCache()->get($key);
		if(empty($data)){
			$ret  = self::getBy(array('level'=>$level));
			$msg = json_decode($ret['level_msg'],true);
			foreach ($msg as $k=>$v){
				$category = User_Service_ExperienceType::get($v['cat_id']);
				$data[] = array(
					'image'	=>$category['image'],
					'num'		=>$v['num'],
					'name'	=>sprintf($category['name'],$v['num']),
					'link'		=>$category['link'],
				);
			}
			Common::getCache()->set($key,$data,30);
		}
		return $data;
		
	}
	
	private static function _checkData($params){
		$temp = array();
		if(isset($params['name']))  $temp['name'] = $params['name'];
		if(isset($params['level']))  $temp['level'] = $params['level'];
		if(isset($params['level_msg']))  $temp['level_msg'] = $params['level_msg'];
		if(isset($params['status']))  $temp['status'] = $params['status'];
		if(isset($params['sort']))  $temp['sort'] = $params['sort'];
		if(isset($params['add_time']))  $temp['add_time'] = $params['add_time'];
		return $temp;
	}
	
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return  Common::getDao("User_Dao_ExperienceInfo");
	}
}