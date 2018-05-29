<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class W3_Service_Cp {

	public static function getAll($orderBy = array()) {
		$list = self::_getDao()->getAll($orderBy);
		return $list;
	}

	public static function all() {
		$ret  = array();
		$list = self::_getDao()->getAll();
		foreach ($list as $val) {
			$ret[$val['id']] = $val;
		}
		return $ret;
	}


	/**
	 *
	 * 列表
	 * @param int $page
	 * @param int $limit
	 * @param int $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('created_at' => 'DESC', 'id' => 'DESC'));
		return $ret;
	}

	/**
	 * 数量
	 * @param array $params
	 * @return string
	 */
	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		$rcKey = Common::KN_W3_CP_INFO . $id;
		$ret   = Common::getCache()->get($rcKey);
		if (!empty($ret)) {
			return $ret;
		}
		$ret = self::_getDao()->get(intval($id));

		if ($ret) {
			Common::getCache()->set($rcKey, $ret, Common::KT_CP_INFO);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		$data  = self::_cookData($data);
		$rcKey = Common::KN_W3_CP_INFO . $id;
		Common::getCache()->delete($rcKey);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * @param int $id
	 */
	public static function del($id) {
		$rcKey = Common::KN_W3_CP_INFO . $id;
		Common::getCache()->delete($rcKey);
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		self::_getDao()->insert($data);
		return self::_getDao()->getLastInsertId();

	}


	/**
	 * 字段过滤
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('id', 'name', 'is_web', 'to_url', 'jmp_text', 'down_text', 'desc', 'tip', 'size', 'icon', 'pic', 'company', 'created_at', 'mark');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 *
	 * @return W3_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("W3_Dao_Cp");
	}
}