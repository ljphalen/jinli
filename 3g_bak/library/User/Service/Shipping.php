<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Shipping {
	
	public static  function add($params){
		if(empty($params)) return false;
		$data = self::_checkData($params);
		$res =  self::_getDao()->insert($data);
		return $res?self::_getDao()->getLastInsertId():0;
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
	
	private static  function  _checkData($params){
		$temp = array();
		if(isset($params['receiver_name']))				$temp['receiver_name'] 			= $params['receiver_name'];
		if(isset($params['uid']))									$temp['uid']								= $params['uid'];
		if(isset($params['address']))							$temp['address']	 					= $params['address'];
		if(isset($params['province_id']))					$temp['province_id']	 			= $params['province_id'];
		if(isset($params['city_id']))							$temp['city_id']	 						= $params['city_id'];
		if(isset($params['distinct_id']))						$temp['distinct_id']					= $params['distinct_id'];
		if(isset($params['mobile']))							$temp['mobile']						= $params['mobile'];
		if(isset($params['telephone']))						$temp['telephone']					= $params['telephone'];
		if(isset($params['email']))								$temp['email']							= $params['email'];	
		if(isset($params['zipcode']))							$temp['zipcode']						= $params['zipcode'];
		if(isset($params['add_time']))						$temp['add_time'	]					= $params['add_time'];
		return $temp;
	} 
	
	private static function _getDao(){
		return Common::getDao("User_Dao_Shipping");
	}
}

?>