<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class User_Service_BookCoupon {

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params = array(), $sort = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $sort);
	}

	public static function getList($page, $pageSize = 20, $where = array(), $order = array()) {
		if (!is_array($where)) return false;
		$page = max($page, 1);
		return array(self::count($where), self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $order));
	}

	public static function add($params = array()) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	public static function delete($id){
		if(!intval($id)) return false;
		return self::_getDao()->delete($id);
	}

	public static function count($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function update($id, $params = array()) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data, $id);
	}

	public static function getAll() {
		return self::_getDao()->getAll();
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function giveBookCouponToUser($params){
		
	}

	private function _checkData($params){
		$temp = array();
		if(isset($params['card_num'])) $temp['card_num'] = $params['card_num'];
		if(isset($params['uid'])) $temp['uid'] = $params['uid'];
		if(isset($params['is_used'])) $temp['is_used'] = $params['is_used'];
		if(isset($params['get_time'])) $temp['get_time'] = $params['get_time'];
		if(isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		return $temp;
	}
	/**
	 * return User_Dao_Blacklist
	 */
	private static function _getDao(){
		return Common::getDao('User_Dao_BookCoupon');
	}
}