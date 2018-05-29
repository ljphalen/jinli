<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_ExperienceLog  {

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
	
	public static function isGetExperiencePoints($uid,$sdate,$edate,$type=1,$gid=0){
		$arr = array();
		$arr['uid'] = $uid;
		$arr['add_time']  = array(array(">=",$sdate),array("<=",$edate));
		$arr['type'] = $type;
		$arr['gid'] = $gid;
		return self::count($arr);
	}

	public static function count($params){
		if(!is_array($params)) return false;
		return  self::_getDao()->count($params);
	}
	public static function get($id){
		if(!is_numeric($id)) return false;
		return self::_getDao()->get($id);
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
	
	public static function getEachTypeExperienceData($params){
		$ret = array();
		$data = self::_getDao()->getEachTypeExperienceData($params);
		foreach ($data as $k=>$v){
			$ret[$v['add_date']][$v['type']]['total_users'] = $v['total_users'];
			$ret[$v['add_date']][$v['type']]['total_points'] = $v['total_points'];
		}
		return $ret;
	}
	
	public static function getExperienceSumData($params){
		$data = self::_getDao()->getExperienceSumData($params);
		return $data;
	}
	
	public static function writeLog($params){
		$temp =array();
		$temp['add_time'] 		= time();
		$temp['uid'] 					= $params['uid'];
		$temp['type']  				= $params['type'];
		$temp['gid']					= $params['gid'];
		$temp['points'] 			= $params['points'];
		$params['add_time'] 	= time();
		$data = self::_checkData($temp);
		$ret = self::add($data);
		$config = Common::getConfig('userConfig','innermsg_type_list');
		$msg = $config[$params['msgType']];
		$msgData = array(
			'uid'	=>$params['uid'],
			'status'=>1,
			'classify'=>$params['msgType'],
			'msg'=>$msg,
			'points'=>$params['points'],
		);
		Common_Service_User::sendInnerMsg($msgData,'get_experience_points_tpl');
		
	}
	
	
	private static function _checkData($params){
		$temp = array();
		if(isset($params['uid']))						$temp['uid'] = $params['uid'];
		if(isset($params['type']))					$temp['type'] = $params['type'];
		if(isset($params['points']))				$temp['points'] = $params['points'];
		if(isset($params['gid']))						$temp['gid'] = $params['gid'];
		if(isset($params['add_time']))			$temp['add_time'] = $params['add_time'];
		return $temp;
		
	}
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return  Common::getDao("User_Dao_ExperienceLog");
	}
}