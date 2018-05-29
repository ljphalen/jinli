<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiger
 *
 */
class Gionee_Service_WebThemeType {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getWebAppType() {
		return array(self::_getDao()->count(), self::_getDao()->getWebAppType());
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('sort' => 'DESC', 'id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 */
	public static function getAll($orderBy=array()) {
		if (!is_array($orderBy)) return false;
		return self::_getDao()->getAll($orderBy);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->getBy($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
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
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_WebThemeType
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_WebThemeType");
	}
}