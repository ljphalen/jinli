<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiger
 *
 */
class Gionee_Service_Page {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAll($orderBy = array()) {
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
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id' => intval($id)));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['page_type'])) $tmp['page_type'] = $data['page_type'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['url_package'])) $tmp['url_package'] = $data['url_package'];
		if (isset($data['is_default'])) $tmp['is_default'] = $data['is_default'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_App
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Page");
	}
}