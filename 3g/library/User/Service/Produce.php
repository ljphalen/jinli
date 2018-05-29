<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Produce {
	
	const PRODUCE_OPEN_STATUS  = 1;
	const PRODUCE_CLOSE_STATUS = 0;
	
	public static  function add($params){
		if(empty($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	
	public static function getList($page,$limit,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$start = (max(intval($page), 1) - 1) * $limit;
		return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
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
	
	private static  function _checkData($params){
		$temp = array();
		if(isset($params['cat_id'])) $temp['cat_id'] = $params['cat_id'];
		if(isset($params['name']))  $temp['name'] = $params['name'];
		if(isset($params['link']))	$temp['link'] = $params['link'];
		if(isset($params['scores']))	$temp['scores']= $params['scores'];
		if(isset($params['start_time'])) $temp['start_time'] = $params['start_time'];
		if(isset($params['end_time']))	 $temp['end_time']	 = $params['end_time'];
		if(isset($params['status'])) $temp['status'] = $params['status'];
		if(isset($params['sort']))	 $temp['sort'] = $params['sort'];
		if(isset($params['image'])) $temp['image'] = $params['image'];
		if(isset($params['add_time']))	$temp['add_time'] = $params['add_time'];
		if(isset($params['add_user'])) $temp['add_user'] = $params['add_user'];
		if(isset($params['edit_time'])) $temp['edit_time'] = $params['edit_time'];
		if(isset($params['edit_user']))$temp['edit_user']= $params['edit_user'];
		if(isset($params['is_special'])) $temp['is_special'] = $params['is_special'];
		if(isset($params['out_ad_id'])) $temp['out_ad_id'] = $params['out_ad_id'];
		return $temp;
	}
	
	private  static  function _getDao(){
		return Common::getDao('User_Dao_Produce');
	}
}