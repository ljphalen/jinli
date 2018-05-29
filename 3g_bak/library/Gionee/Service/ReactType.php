<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 反馈问题列表类
 */

class Gionee_Service_ReactType {
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function add($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function getList($page,$pageSize,$where,$order){
		if(!is_array($where)) return false;
		return array(self::_getDao()->count($where), self::_getDao()->getList($pageSize *($page-1),$pageSize,$where,$order));
	}
	
	public static function update($params,$id){
		if(!is_array($params) || !$id) return false;
		return self::_getDao()->update($params,$id);
	}
	
	public static function getBy($params =array(),$orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	public static function getsBy($params =array(),$orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$orderBy);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	

	public static function deleteBy($params=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	private static function _checkData($params){
		$temp  = array();
		if(isset($params['id']))  $temp['id'] = $params['id'];
		if(isset($params['parent_id']))	  $temp['parent_id'] = $params['parent_id'];
		if(isset($params['status']))	  $temp['status'] = $params['status'];
		if(isset($params['name']))	  $temp['name'] = $params['name'];
		if(isset($params['sort']))	  $temp['sort'] = $params['sort'];
		if(isset($params['add_time']))	  $temp['add_time'] = $params['add_time'];
		return $temp ;
	
	}
	
	private static function _getDao(){
		return Common::getDao("Gionee_Dao_ReactType");
	}
}