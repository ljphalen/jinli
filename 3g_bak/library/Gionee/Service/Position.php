<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

 class Gionee_Service_Position {
	
 	public static function add($params){
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
 	
 	public static function getsBy($params,$order=array()){
 		if(!is_array($params))return  false;
 		return self::_getDao()->getsBy($params,$order);
 	}
 	public static function delete($id){
 		return self::_getDao()->delete($id);
 	}
 	
 	private  static function _checkData($params){
 		$temp = array();
 		if(isset($params['name'])) 			$temp['name'] = $params['name'];
 		if(isset($params['identifier']))	$temp['identifier'] = $params['identifier'];
 		if(isset($params['status']))		$temp['status']	=$params['status'];
 		if(isset($params['type']))			$temp['type']	=$params['type'];
 		if(isset($params['add_time']))		$temp['add_time']= $params['add_time'];
 		if(isset($params['edit_time']))		$temp['edit_time']=$params['edit_time'];
 		if(isset($params['edit_user']))		$temp['edit_user']=$params['edit_user'];
 		return $temp;
 	}
 	private static  function _getDao(){
 		return Common::getDao('Gionee_Dao_Position');
 	}
}