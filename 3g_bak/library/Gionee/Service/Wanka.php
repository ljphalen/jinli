<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 
 * @author lclz1999
 *
 */
class Gionee_Service_Wanka {


	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id' => 'DESC')) {
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
	public static function addWanka($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getWanka($id) {
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

	public static function updateWanka($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteWanka($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desTjTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['app_ver'])) $tmp['app_ver'] = $data['app_ver'];
		if (isset($data['wk_main_switch'])) $tmp['wk_main_switch'] = intval($data['wk_main_switch']);
		if (isset($data['wk_searchEngines_switch'])) $tmp['wk_searchEngines_switch'] = intval($data['wk_searchEngines_switch']);
		if (isset($data['wk_hotKeyword_switch'])) $tmp['wk_hotKeyword_switch'] = intval($data['wk_hotKeyword_switch']);
		if (isset($data['wk_suggested_switch'])) $tmp['wk_suggested_switch'] = intval($data['wk_suggested_switch']);
		return $tmp;
	}

	/**
	 * @return Gionee_Dao_Wanka
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Wanka");
	}
}
