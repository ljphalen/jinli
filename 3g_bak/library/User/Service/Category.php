<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Category {
	
	public static  function add($params){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize ,$where =array(),$orderBy = array()){
		if(!is_array($where)) return false;
		$data = self::_checkData($where);
		$dataList =  self::_getDao()->getList(($page-1)*$pageSize, $pageSize,$data,$orderBy);
		return array(self::_getDao()->count($where),$dataList);
	}
	
	public  static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function getsBy($params =array(),$groupBy=array()){
		if(!is_array($params) ) return false;
		return self::_getDao()->getsBy($params,$groupBy);
	}
	public static function update($params= array(),$id){
		if(empty($params)|| !is_numeric($id)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function delete($id){
		if(!is_numeric($id)) return false;
		return self::_getDao()->delete($id);
	}
	private static function _checkData($params){
		$temp  =array();
		if(isset($params['name']))       $temp['name'] = $params['name'];
		if(isset($params['status']))       $temp['status'] = $params['status'];
		if(isset($params['sort']))           $temp['sort'] = $params['sort'];
		if(isset($params['group_id']))   $temp['group_id'] = $params['group_id'];
		if(isset($params['add_time']))  $temp['add_time'] = $params['add_time'];
		if(isset($params['add_user']))  $temp['add_user'] = $params['add_user'];
		if(isset($params['edit_time']))  $temp['edit_time'] =$params['edit_time'];
		if(isset($params['edit_user']))  $temp['edit_user'] = $params['edit_user'];
		if(isset($params['img']))				$temp['image']		= $params['img'];
		if(isset($params['score_type'])) $temp['score_type']= $params['score_type'];
		if(isset($params['ext']))				$temp['ext'] 				= 	$params['ext'];
		if(isset($params['max_number'])) $temp['max_number'] = $params['max_number'];
		if(isset($params['link']))					$temp['link']				= $params['link'];
		if(isset($params['description']))  $temp['description'] = $params['description'];
		return $temp;
		
	}

	/**
	 * @return User_Dao_Category
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_Category");
	}
}