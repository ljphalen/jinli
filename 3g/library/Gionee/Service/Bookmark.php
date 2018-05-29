<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Gionee_Service_Bookmark {

	//运营商
	static $opType = array(
		'OP00' => '普通',
		'OP01' => '移动',
		'OP02' => '联通',
	);

	static $opParam = array(
		'OP00' => 1,
		'OP01' => 2,
		'OP02' => 4,
	);

	static $opName = array(
		1 => '普通',
		2 => '移动',
		4 => '联通',
	);

	const V311 = 1;
	const V312 = 2;
	const V400 = 4;
	static $ver = array(
		self::V311 => '3.1.1',
		self::V312 => '3.1.2',
		self::V400 => '4.0.0',
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
	 *
	 * Enter description here ...
	 *
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
	 *
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id' => intval($id)));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['is_delete'])) $tmp['is_delete'] = $data['is_delete'];
		if (isset($data['backgroud'])) $tmp['backgroud'] = $data['backgroud'];
		if (isset($data['ver'])) $tmp['ver'] = $data['ver'];
		if (isset($data['op_type'])) $tmp['op_type'] = $data['op_type'];
		if (isset($data['operation'])) $tmp['operation'] = $data['operation'];
		if (isset($data['updated_at'])) $tmp['updated_at'] = $data['updated_at'];
		if (isset($data['cp_id']))			$tmp['cp_id'] = $data['cp_id'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Bookmark
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Bookmark");
	}
}