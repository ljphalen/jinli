<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Gionee_Service_Browserurl {	
	
	const Browser = 1;
	const Widget = 2;
	
	static $app = array(
			self::Browser => 'browser',
			self::Widget  => 'widget'
	);

	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy);
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
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['show_url'])) $tmp['show_url'] = $data['show_url'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['created_at'])) $tmp['created_at'] = $data['created_at'];
		if (isset($data['updated_at'])) $tmp['updated_at'] = $data['updated_at'];
		if (isset($data['operation'])) $tmp['operation'] = $data['operation'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['app'])) $tmp['app'] = $data['app'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Browserurl
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Browserurl");
	}
}