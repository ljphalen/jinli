<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_ModelType{
	
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
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static function getAll($order){
		return self::_getDao()->getAll($order);
	}
	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	
	public function getTypeValueData($where,$order,$key){
		if(!is_array($where))return  false;
		return self::_getDao()->getTypeValueData($where,$order,$key);
	}
	private static function _checkData($params){
		$temp = array();
		if(isset($params['id']))  $temp['id'] = $params['id'];
		if(isset($params['type'])) $temp['type'] = $params['type'];
		if(isset($params['value']))  $temp['value'] = $params['value'];
		if(isset($params['ext'])) $temp['ext'] = $params['ext'];
		return $temp;
	}
	/**
	 * @return  Gionee_Dao_ModelType
	 */
	
	private static  function _getDao(){
		return Common::getDao('Gionee_Dao_ModelType');
	}
}