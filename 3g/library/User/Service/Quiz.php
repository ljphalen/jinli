<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Quiz {

	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}

	public static function getsBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$sort);
	}
	
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	
	public static function getAll($order){
		return self::_getDao()->getAll($order);
	}
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	public static function edit($params,$id){
		return self::_getDao()->update($params,$id);
	}
	
	public static function getList($page,$pageSize,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList($pageSize*($page-1),$pageSize,$where,$orderBy));
	}
	
	
	private static function  _checkData($params){
		$temp = array();
		if(isset($params['title']))					$temp['title'] = $params['title'];
		if(isset($params['keywords']))			$temp['keywords'] = $params['keywords'];
		if(isset($params['option1']))				$temp['option1'] = $params['option1'];
		if(isset($params['option2']))				$temp['option2'] = $params['option2'];
		if(isset($params['option3']))				$temp['option3'] = $params['option3'];
		if(isset($params['option4']))				$temp['option4'] = $params['option4'];
		if(isset($params['answer']))				$temp['answer'] = $params['answer'];
		if(isset($params['add_time']))			$temp['add_time'] = $params['add_time'];
		return $temp;
	}
	
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_Quiz");
	}
}