<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_Address {
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getAddress($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort' => 'DESC'));
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getAllAddress($params = array()) {
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getAllAddress($params);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAddress($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$ret  = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}


	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchaddAddress($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateAddress($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteAddress($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['province'])) $tmp['province'] = $data['province'];
		if (isset($data['city'])) $tmp['city'] = $data['city'];
		if (isset($data['address_type'])) $tmp['address_type'] = $data['address_type'];
		if (isset($data['service_type'])) $tmp['service_type'] = $data['service_type'];
		if (isset($data['address'])) $tmp['address'] = $data['address'];
		if (isset($data['tel'])) $tmp['tel'] = $data['tel'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		return $tmp;
	}


	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Address");
	}
}
