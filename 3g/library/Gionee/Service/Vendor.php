<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Gionee_Service_Vendor
 * @author tiger
 */
class Gionee_Service_Vendor {

	/**
	 * Enter desNavTypeiption here ...
	 */
	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy = array());
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 * Enter description here ...
	 * @param string $name
	 */

	public static function getBy($ch) {
		return self::_getDao()->getBy(array('ch' => $ch));
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		self::_getDao()->insert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['ch'])) $tmp['ch'] = $data['ch'];
		return $tmp;
	}

	/**
	 * @return Gionee_Dao_Vendor
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Vendor");
	}
}