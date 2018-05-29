<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_UMoneyLog{

	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}

	public static function get($id){
		return self::_getDao()->get($id);
	}

	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}

	public static function getList($page,$limit,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$start = (max(intval($page), 1) - 1) * $limit;
		return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
	}


	private static function _checkData($params=array()){
		$temp = array();
		if(isset($params['uid'])) 									$temp['uid'] = $params['uid'];
		if(isset($params['before_money'])) 			$temp['before_money'] = $params['before_money'];
		if(isset($params['after_money'])) 				$temp['after_money'] = $params['after_money'];
		if(isset($params['affected_money'])) 			$temp['affected_money'] = $params['affected_money'];
		if(isset($params['type'])) 								$temp['type'] = $params['type'];
		if(isset($params['goods_id'])) 						$temp['goods_id'] = $params['goods_id'];
		if(isset($params['add_time'])) 						$temp['add_time'] = $params['add_time'];
		if(isset($params['date'])) 								$temp['date'] = $params['date'];
		
		return $temp;
	}

	/**
	 *
	 * @return User_Dao_UMoneyLog
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_UMoneyLog");
	}
}