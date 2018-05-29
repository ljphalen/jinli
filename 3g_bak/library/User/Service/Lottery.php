<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户中心抽奖商品类
*/

class User_Service_Lottery{

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

	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}

	private  static function _checkData($params){
		$temp = array();
		if(isset($params['name']))			$temp['name'] 			= $params['name'];
		if(isset($params['val']))				$temp['val']				= $params['val'];
		if(isset($params['level']))			$temp['level']	 		= $params['level'];
		if(isset($params['number']))		$temp['number']	 	= $params['number'];
		if(isset($params['status']))		$temp['status']	 		= $params['status'];
		if(isset($params['ratio']))			$temp['ratio']			= $params['ratio'];
		if(isset($params['image']))		$temp['image']			= $params['image'];
		if(isset($params['type']))			$temp['type']				= $params['type'];
		if(isset($params['sort']))			$temp['sort']				= $params['sort'];	
		if(isset($params['created_time']))$temp['created_time']= $params['created_time'];
		return $temp;
	}

	private static  function _getDao()
	{
		return Common::getDao('User_Dao_Lottery');
	}
}