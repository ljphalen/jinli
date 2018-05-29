<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 计划任务日志
 * @author huwei
 *
 */
class Gionee_Service_CronLog {
	static $types = array('baidu', 'gen_data', 'nav_news', 'out_news', 'sync_log');
	/**
	 *
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 * @return array
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
	 * @param int $id
	 * @return array
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params, $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getsBy($params, $orderBy);
	}

	static public function total($params) {
		$total = self::_getDao()->count($params);
		return $total;
	}


	/**
	 *
	 * @param array $data
	 * @param int $id
	 * @return array
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *

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
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $id
	 */
	public static function deletes($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->deletes('id', $ids);
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;

		if (empty($data['created_at'])) {
			$data['created_at'] = time();
		}
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['msg'])) $tmp['msg'] = $data['msg'];
		if (isset($data['created_at'])) $tmp['created_at'] = $data['created_at'];

		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_CronLog
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_CronLog");
	}
}
