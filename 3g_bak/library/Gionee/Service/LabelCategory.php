<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_LabelCategory {
	
public static function  add($params){
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize,$where,$order){
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1)*$pageSize,$pageSize,$where,$order));
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function edit($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function editBy($params=array(),$where = array()){
		if(!is_array($params) || !is_array($where)) return false;
		return self::_getDao()->updateBy($params, $where);
	}
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	private  static function _checkData($params){
		$data = array();
		if(isset($params['parent_id']))  $data['parent_id']  = $params['parent_id'];
		if(isset($params['name']))  $data['name']  = $params['name'];
		return $data;
	}
	/**
	 * 
	 * @return Gionee_Dao_LabelCategory
	 */
	private static function _getDao(){
		 return Common::getDao("Gionee_Dao_LabelCategory");
	}
}