<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Column {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAll() {
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
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getBy($params);
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
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updates($ids, $data) {
		if (!is_array($data) || !is_array($ids)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updates('id', $ids, $data);
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
	 * @param unknown_type $id
	 */
	public static function deletes($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->deletes('id', $ids);
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
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['ptype'])) $tmp['ptype'] = $data['ptype'];
		if (isset($data['source_id'])) $tmp['source_id'] = $data['source_id'];
		if (isset($data['pptype'])) $tmp['pptype'] = $data['pptype'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Column
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Column");
	}
}
