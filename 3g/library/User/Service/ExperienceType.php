<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_ExperienceType  {

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
	
	
	private static function _checkData($params){
		$temp = array();
		if(isset($params['type']))	 $temp['type'] = $params['type'];
		if(isset($params['name'])) $temp['name'] = $params['name'];
		if(isset($params['status'])) $temp['status'] = $params['status'];
		if(isset($params['image'])) $temp['image'] = $params['image'];
		if(isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		if(isset($params['link'])) 	$temp['link'] = $params['link'];
		return $temp;
	}
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return  Common::getDao("User_Dao_ExperienceType");
	}
}