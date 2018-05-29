<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_App {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllApps() {
		return array(self::_getDao()->count(), self::_getDao()->getAllApps());
	}


	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param int $id
	 */
	public static function getApp($id, $sync=false) {
		if (!intval($id)) return false;
		$rcKey = 'WEBAPP:' . $id;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false || $sync) {
			$ret = self::_getDao()->get(intval($id));
			Common::getCache()->set($rcKey, $ret, 3600);
		}
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateApp($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $id
	 */
	public static function deleteApp($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	public static function addApp($data) {
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
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if (isset($data['is_recommend'])) $tmp['is_recommend'] = $data['is_recommend'];
		if (isset($data['star'])) $tmp['star'] = $data['star'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_App
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_App");
	}
}
