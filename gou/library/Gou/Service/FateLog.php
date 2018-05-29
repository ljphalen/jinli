<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_FateLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFateLog() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * 
	 * @param unknown_type $time
	 * @return mixed
	 */
	public static function getFateLogsByTime($start, $end) {
		if (!$start || !$end) return false;
		return self::_getDao()->getFateLogsByTime($start, $end);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getFatedLogs($page = 1, $limit = 10) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		return self::_getDao()->getFatedLogs($start, $limit);
	}
	
	/**
	 * 
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogsByMobile($mobile) {
		if (!$mobile) return false;
		return self::_getDao()->getFateLogsByMobile($mobile);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public static function getCountByMobile($mobile, $start, $end) {
		if (!$mobile) return false;
		return self::_getDao()->getCountByMobile($mobile, $start, $end);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFateLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFateLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFateLog($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFateLog($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if(isset($data['price'])) $tmp['price'] = intval($data['price']);
		if(isset($data['rule_id'])) $tmp['rule_id'] = intval($data['rule_id']);
		if(isset($data['question'])) $tmp['question'] = $data['question'];
		if(isset($data['answer'])) $tmp['answer'] = $data['answer'];
		if(isset($data['order_id'])) $tmp['order_id'] = $data['order_id'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['confirm_time'])) $tmp['confirm_time'] = $data['confirm_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_FateLog
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_FateLog");
	}
}
