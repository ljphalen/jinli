<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 
 * @author lclz1999
 *
 */
class Gionee_Service_Pushtools {


	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('pid' => 'DESC')) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addPushtools($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getPushtools($id) {
		return self::_getDao()->get(intval($id));
	}

	public static function getBy($params =array(),$sort= array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	} 
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy = array());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updatePushtools($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deletePushtools($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desTjTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['text'])) $tmp['text'] = $data['text'];
		if (isset($data['after_open'])) $tmp['after_open'] = $data['after_open'];
		if (isset($data['big_picture_url'])) $tmp['big_picture_url'] = $data['big_picture_url'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['activity'])) $tmp['activity'] = $data['activity'];
		return $tmp;
	}

	/**
	 * @return Gionee_Dao_Pushtools
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Pushtools");
	}
}
