<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiger
 *
 */
class Gionee_Service_Welcome {

	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy);
	}

	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		return self::_getDao()->getsBy($params, $orderBy = array());
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
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['ver'])) $tmp['ver'] = $data['ver'];
		if (isset($data['text'])) $tmp['text'] = $data['text'];
		if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if (isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Subject
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Welcome");
	}
}