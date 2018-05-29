<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Level {

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
}