<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Gionee_Service_Browsermodel {

	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy);
	}

	public static function allArr() {
		$tmp = array();
		$list = self::_getDao()->getAll();
		foreach($list as $val) {
			$tmp[$val['id']] = $val['name'];
		}
		return $tmp;
	}

	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('id', 'name', 'dpi', 'series_id', 'created_at');
		return Common::cookData($data, $fields);
	}

	/**
	 *
	 * @return Gionee_Dao_Browsermodel
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Browsermodel");
	}
}