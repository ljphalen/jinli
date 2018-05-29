<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_ExperienceLevelLog {
	
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
	
	public static function count($params){
		return self::_getDao()->count($params);
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
	
	public static function addFirstLevelData($uid,$experience_level){
		$levelLog = User_Service_ExperienceLevelLog::count(array('uid'=>$uid,'new_level'=>$experience_level));
		if(empty($levelLog)){
			User_Service_ExperienceLevelLog::add(array('uid'=>$uid,'date'=>date('Y-m-d',time()),'add_time'=>time()));
		}
	}
	
	public static function getUpdradeLevelMsg($uid,$newLevel,$isPoped){
		$key = "USER:UPGRADE:LEVEL:{$uid}:{$newLevel}:";
		$data = Common::getCache()->get($key);
		if(empty($data)){
			$data = self::getBy(array('uid'=>$uid,'new_level'=>$newLevel,'is_popup'=>$isPoped));
			Common::getCache()->set($key,$data,300);
		}
		return $data;
	}
	
	
	public static function getPerDayUpgradeUser($params,$group){
		$data = self::_getDao()->getPerDayUpgradeUser($params,$group);
		$ret = array();
		foreach ($data as $k=>$v){
			$ret[date('Ymd',strtotime($v['date']))][$v['new_level']] = $v['total_number'];
		}
		return $ret;
	}
	
	public static function getEachLevelTotalUser($params,$group){
	}
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['uid']))						$temp['uid'] 	= $params['uid'];
		if(isset($params['old_level']))			$temp['old_level'] 	= $params['old_level'];
		if(isset($params['new_level']))			$temp['new_level'] 	= $params['new_level'];
		if(isset($params['add_time']))			$temp['add_time'] 	= $params['add_time'];
		if(isset($params['date']))					$temp['date'] 	= $params['date'];
		if(isset($params['is_popup']))			$temp['is_popup'] 	= $params['is_popup'];
		return $temp;
		}
	/**
	 * 
	 * @return object
	 */
	private  static function _getDao(){
		return  Common::getDao("User_Dao_ExperienceLevelLog");
	}
}