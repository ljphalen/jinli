<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化导航
 */
class Gionee_Service_LocalNavColumn {
	public static function getList($page = 1, $limit = 10, $params = array(), $orderby = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderby);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static function getInfo($id, $sync = false) {
		$rcKey = 'LOCALNAV_COLUMN:' . $id;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret == false || $sync) {
			$info = self::get($id);
			Common::getCache()->set($rcKey, $info, 3600);
		}
		return $ret;
	}

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getsBy($where = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($where, $orderBy);
	}

	public static function getBy($where, $orderBy = array()) {
		return self::_getDao()->getBy($where, $orderBy);
	}

	/**
	 *
	 * @param array $data
	 * @param int   $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) {
			return false;
		}
		$ret = self::_getDao()->update($data, intval($id));
		self::getInfo($id, true);
		return $ret;
	}

	/**
	 *
	 * @param array $ids
	 * @param array $data
	 */
	public static function sets($ids, $data) {
		if (!is_array($data) || !is_array($ids)) {
			return false;
		}
		return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 *
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->insert($data);
	}

	public static function options() {
		return self::_getDao()->getsBy(array(), array('type_id' => 'asc', 'sort' => 'asc', 'id' => 'desc'));
	}

	/**
	 *
	 * @return Gionee_Dao_LocalNavColumn
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_LocalNavColumn");
	}
}