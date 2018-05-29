<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_ProductAttribute {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllProductAttribute() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getProductAttribute($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $ids
	 * @return array
	 */
	public static function getAttributeByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getAttributeByIds($ids);
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
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('id' => 'DESC'));
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addProductAttribute($data) {
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
	public static function updateProductAttribute($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteProductAttribute($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['icon_url'])) $tmp['icon_url'] = $data['icon_url'];
		return $tmp;
	}


	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ProductAttribute");
	}
}