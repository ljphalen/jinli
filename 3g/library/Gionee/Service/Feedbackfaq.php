<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 反馈消息
 */
class Gionee_Service_Feedbackfaq {

	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		return $ret;
	}

	public static function getTotal($params) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function set($data, $id) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->update($data, intval($id));
	}

	public static function getBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getBy($params, $orderBy);
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function sets($ids, $data) {
		if (!is_array($data) || !is_array($ids)) {
			return false;
		}
		return self::_getDao()->updates('id', $ids, $data);
	}

	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	public static function add($data) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->insert($data);
	}

	/**
	 * @return Gionee_Dao_Feedbackfaq
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_Feedbackfaq");
	}
}