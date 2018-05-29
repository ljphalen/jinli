<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class W3_Service_Topic {

	public static function getAll($orderBy = array()) {
		return array(self::_getDao()->count(), self::_getDao()->getAll($orderBy));
	}

	/**
	 *
	 * @param int $page
	 * @param int $params
	 * @param array $page
	 * @param array $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		$page   = max($page, 1);
		$start  = ($page - 1) * $limit;
		$ret    = self::_getDao()->getList($start, $limit, $params, $orderBy);
		return $ret;
	}

	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		$ret = false;
		$id  = intval($id);
		if ($id) {
			$ret = self::_getDao()->get($id);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $params
	 */
	public static function getsBy($params) {
		$ret = false;
		if (is_array($params)) {
			$ret = self::_getDao()->getsBy($params);
		}
		return $ret;
	}


	public static function getBy($params) {
		if (!is_array($params)) {
			return false;
		}
		$ret = self::_getDao()->getBy($params);
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		$ret = false;
		$id  = intval($id);
		if (is_array($data) && $id > 0) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->update($data, $id);
		}
		return $ret;
	}


	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|int
	 */
	public static function updates($ids, $data) {
		$ret = false;
		if (is_array($data) && is_array($ids)) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->updates("id", $ids, $data);
		}
		return $ret;
	}

	/**
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $ids
	 * @return boolean
	 */
	public static function deletes($ids) {
		$ret = false;
		if (is_array($ids)) {
			$ret = self::_getDao()->deletes("id", $ids);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function deleteBy($params) {
		$ret = false;
		if (is_array($params)) {
			$ret = self::_getDao()->deleteBy($params);
		}
		return $ret;
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		$ret = false;
		if (is_array($data)) {
			$data = self::_cookData($data);
			$bUp  = self::_getDao()->insert($data);
			if ($bUp) {
				$ret = self::_getDao()->getLastInsertId();
			}
		}
		return $ret;
	}

	public static function edit($data) {
		$ret = false;
		if (is_array($data)) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->update($data, $data['id']);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $data
	 */
	public static function multiAdd($data) {
		$ret = false;
		if (is_array($data)) {
			$ret = self::_getDao()->mutiInsert($data);
		}
		return $ret;
	}

	/**
	 * @param array $data
	 */
	private static function _cookData($data) {
		$arr = array('id', 'title', 'content', 'create_time',);
		$tmp    = Common::cookData($data, $arr);
		return $tmp;
	}

	/**
	 *
	 * @return W3_Dao_Topic
	 */
	private static function _getDao() {
		return Common::getDao("W3_Dao_Topic");
	}

}
