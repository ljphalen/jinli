<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_QuizResult {

	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->getBy($params,$sort);
	}

	public static function add($params = array()){
		if(!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function edit($params,$id){
		if(!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->update($params,$id);
	}
	
	public static function getsBy($params=array(),$order=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	public static function getList($page,$limit,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$start = (max(intval($page), 1) - 1) * $limit;
		return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
	}
	
	public static function getAnswerUserData($where){
		$data = self::_getDao()->getAnswerUserData($where);
		$ret = array();
		foreach($data as $k=>$v){
			$ret[$v['add_date']]['quiz_users'] = $v['quiz_users'];
			$ret[$v['add_date']]['quiz_times']	 = $v['quiz_times'];
		}
		return $ret;
	}

	public static function getDayAnswerData($params=array(),$groupBy){
		$data = self::_getDao()->getDayAnswerData($params,$groupBy);
		$ret = array();
		foreach ($data as $k=>$v){
			$ret[$v['uid']][$v['is_right']] = $v['total'];
		}
		return $ret;
	}
	
	public static function getPerdayAnswerData($where){
		if(!is_array($where)) return false;
		$data = self::_getDao()->getPerdayAnswerData($where);
	}
	
	private static function _checkData($params){
		$temp = array();
		if(isset($params['uid'])) 								$temp['uid'] = $params['uid'];
		if(isset($params['quiz_id'])) 						$temp['quiz_id'] = $params['quiz_id'];
		if(isset($params['selected'])) 					$temp['selected'] = $params['selected'];
		if(isset($params['is_right'])) 						$temp['is_right'] = $params['is_right'];
		if(isset($params['add_time'])) 					$temp['add_time'] = $params['add_time'];
		if(isset($params['answer_time'])) 			$temp['answer_time'] = $params['answer_time']; 
		return $temp;
	}
	
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_QuizResult");
	}
}