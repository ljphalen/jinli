<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_Series {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSeries() {
		return array(self::_getDao()->count(), self::_getDao()->getAllSeries());
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('sort' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSeries($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $name
	 */
	public static function getSeriesByName($name) {
		if (!$name) return false;
		return self::_getDao()->getBy(array('name' => $name));
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSeries($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteSeries($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSeries($data) {
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
		if (isset($data['description'])) $tmp['description'] = $data['description'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Series
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Series");
	}
}
