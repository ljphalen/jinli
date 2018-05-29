<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Link {
//添加信息
	public static function add($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}

	public static function get($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
	}
	
	public static function update($params = array(), $id) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->update($params, $id);
	}

	public static function updateBy($params = array(), $where = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->updateBy($params, $where);
	}

	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function count($params){
		 return self::_getDao()->count($params);
	}
	
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['uid']))  						$temp['uid'] = $params['uid'];
		if(isset($params['uname']))  					$temp['uname'] = $params['uname'];
		if(isset($params['prize_id']))  				$temp['prize_id'] = $params['prize_id'];
		if(isset($params['prize_tatus']))  			$temp['prize_tatus'] = $params['prize_tatus'];
		if(isset($params['add_time']))  				$temp['add_time'] = $params['add_time'];
		if(isset($params['get_prize_time']))  	$temp['get_prize_time'] = $params['get_prize_time'];
		if(isset($params['prize_level']))			$temp['prize_level'] = $params['prize_level'];
		return $temp;
	}
	/**
	 * @return User_Dao_Earn
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_Link");
	}
	
}