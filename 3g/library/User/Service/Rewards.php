<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//单个用户对等级商品操作记录
class User_Service_Rewards {
	
	public static function getBy($params =array(),$sort = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return  self::_getDao()->insert($params);
	}
	
	public static function edit($params=array(),$id){
		if(!is_array($params)) return false;
		return self::_getDao()->update($params,$id);
	}
	
	public static  function getRwardGoodsInfo($params = array()){
			if(!is_array($params)) return false;
			$res = self::getBy($params);
			if(empty($res) && isset($params['goods_id'])){
				unset($params['goods_id']);
				$res = self::getBy($params);
			}
			return $res ;
	} 
	private static function _getDao(){
		return Common::getDao("User_Dao_Rewards");
	}
}