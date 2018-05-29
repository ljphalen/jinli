<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter desNavTypeiption here ...
 * @author tiansh
 *
 */
class Gionee_Service_Blackurl {


	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id' => 'DESC')) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBlackurl($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBlackurl($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy = array());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updateBlackurl($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteBlackurl($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desTjTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}

	/**
	 * @return Gionee_Dao_Blackurl
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Blackurl");
	}
}
