<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_ResourceImg {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllResourceImg() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, array('id' => 'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getResourceImg($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceImg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteResourceImg($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByResourceId($resource_id) {
		if (!intval($resource_id)) return false;
		return self::_getDao()->deleteByResourceId(intval($resource_id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceImg($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach ($data as $key => $value) {
			$temp[] = array('id'  => '',
			                'rid' => intval($value['rid']),
			                'img' => $value['img']
			);
		}
		$ret = self::_getDao()->mutiInsert($temp);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['rid'])) $tmp['rid'] = $data['rid'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_ResourceImg
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ResourceImg");
	}
}
