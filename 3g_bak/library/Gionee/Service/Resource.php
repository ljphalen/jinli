<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Resource {
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getResource($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public static function getCanUseResources() {
		return self::_getDao()->getCanUseResources();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort' => 'DESC', 'id' => 'DESC'));
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown_type $ids
	 * @return boolean
	 */
	public static function getListByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getListByIds($ids);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResource($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$ret  = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateResource($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteResource($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['down_url'])) $tmp['down_url'] = $data['down_url'];
		if (isset($data['company'])) $tmp['company'] = $data['company'];
		if (isset($data['star'])) $tmp['star'] = intval($data['star']);
		if (isset($data['size'])) $tmp['size'] = $data['size'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['summary'])) $tmp['summary'] = $data['summary'];
		if (isset($data['description'])) $tmp['description'] = $data['description'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}


	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Resource");
	}
}
