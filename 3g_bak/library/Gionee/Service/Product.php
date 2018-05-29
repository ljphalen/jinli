<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Product {
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getProduct($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param int $model_id
	 */

	public static function getProductByModelId($model_id) {
		return self::_getDao()->getProductByModelId(intval($model_id));
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
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addProduct($data) {
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
	public static function updateProduct($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteProduct($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param int $series
	 * @return multitype:
	 */
	public static function getListBySeries($series) {
		if (!$series) return false;
		return self::_getDao()->getListBySeries($series);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['price'])) $tmp['price'] = $data['price'];
		if (isset($data['series_id'])) $tmp['series_id'] = $data['series_id'];
		if (isset($data['model_id'])) $tmp['model_id'] = $data['model_id'];
		if (isset($data['buy_url'])) $tmp['buy_url'] = $data['buy_url'];
		if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['attribute_id'])) $tmp['attribute_id'] = $data['attribute_id'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}


	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Product");
	}
}
