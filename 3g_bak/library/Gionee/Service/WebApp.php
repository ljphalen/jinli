<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_WebApp {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllApps() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}


	/**
	 *
	 * Enter description here ...
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
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function count($params) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->count($params);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getApp($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
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
	 * @param unknown_type $id
	 */
	public static function deleteApp($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
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
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id' => intval($id)));
	}

	public static function getByTitle($title) {
		if (!$title) return false;
		return self::_getDao()->getBy(array('name' => $title));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['tag'])) $tmp['tag'] = $data['tag'];
		if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if (isset($data['theme_id'])) $tmp['theme_id'] = $data['theme_id'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['icon2'])) $tmp['icon2'] = $data['icon2'];
		if (isset($data['default_icon'])) $tmp['default_icon'] = $data['default_icon'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['sub_time'])) $tmp['sub_time'] = $data['sub_time'];
		if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if (isset($data['is_recommend'])) $tmp['is_recommend'] = $data['is_recommend'];
		if (isset($data['is_must'])) $tmp['is_must'] = $data['is_must'];
		if (isset($data['is_new'])) $tmp['is_new'] = $data['is_new'];
		if (isset($data['star'])) $tmp['star'] = $data['star'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_WebApp
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_WebApp");
	}
}