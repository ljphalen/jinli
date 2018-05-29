<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter desNavTypeiption here ...
 * @author tiansh
 *
 */
class Gionee_Service_ElifeServer {

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $name
	 */

	public static function getByName($name) {
		return self::_getDao()->getBy(array('name' => $name));
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentId($parent_id) {
		return self::_getDao()->getsBy(array('parent_id' => $parent_id), array('sort' => 'DESC', 'id' => 'DESC'));
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getsBy() {
		return self::_getDao()->getsBy(array('status' => '1'), array('sort' => 'DESC'));
	}

	/**
	 *
	 * get parentList
	 * @return multitype:
	 */
	public static function getParentList() {
		return self::_getDao()->getParentList();
	}

	/**
	 *
	 * get parentList
	 * @return multitype:
	 */
	public static function getParentByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getParentByIds($ids);
	}

	/**
	 *
	 * get parentList
	 * @return multitype:
	 */
	public static function getAllType() {
		return self::_getDao()->getAllType();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$ret  = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAddNavType($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['function'])) $tmp['function'] = $data['function'];
		if (isset($data['outward'])) $tmp['outward'] = $data['outward'];
		if (isset($data['parameter'])) $tmp['parameter'] = $data['parameter'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_ElifeServer
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ElifeServer");
	}
}
