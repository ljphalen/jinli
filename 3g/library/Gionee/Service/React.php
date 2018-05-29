<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lichanghua
 *
 */
class Gionee_Service_React {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllReact() {
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getReact($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateReact($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteReact($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addReact($data) {
		if (!is_array($data)) return false;
		$data['status']      = 1;
		$data                = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['contact'])) $tmp['contact'] = $data['contact'];
		if (isset($data['react'])) $tmp['react'] = $data['react'];
		if (isset($data['status'])) $tmp['status'] = intval($data['status']);
		if (isset($data['reply'])) $tmp['reply'] = $data['reply'];
		if(isset($data['checked_list'])) $tmp['checked_list'] = $data['checked_list'];
		if(isset($data['menu_id'])) $tmp['menu_id'] = $data['menu_id'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_React
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_React");
	}
}
