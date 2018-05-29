<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Service_FateRule{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFateRule() {
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
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFateRule($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFateRuleByActivityId($activity_id) {
		if (!intval($activity_id)) return false;
		return self::_getDao()->getsBy(array('activity_id'=>$activity_id),array('lottery_id'=>'ASC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFateRule($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFateRule($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFateRule($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addMutiFateRule($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->mutiInsert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['award_name'])) $tmp['award_name'] = $data['award_name'];
		if(isset($data['probability'])) $tmp['probability'] = intval($data['probability']);
		if(isset($data['lottery_id'])) $tmp['lottery_id'] = intval($data['lottery_id']);
		if(isset($data['activity_id'])) $tmp['activity_id'] = intval($data['activity_id']);
		if(isset($data['num'])) $tmp['num'] = intval($data['num']);
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['space_time'])) $tmp['space_time'] = intval($data['space_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_FateRule
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_FateRule");
	}
}
