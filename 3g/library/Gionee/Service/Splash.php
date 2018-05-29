<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Splash {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSplash() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseSplashs($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if (intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getCanUseSplashs(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseSplashCount($params);
		return array($total, $ret);
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSplash($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSplash($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteSplash($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSplash($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['img_url'])) $tmp['img_url'] = $data['img_url'];
		if (isset($data['version'])) $tmp['version'] = $data['version'];
		if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if (isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Splash
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Splash");
	}
}
