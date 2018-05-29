<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter desCategoryiption here ...
 * @author tiansh
 *
 */
class Dhm_Service_Category {


	/**
	 *
	 * Enter desCategoryiption here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
	 * Enter desCategoryiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);

		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params);
	}
	
	
	/**
	 *
	 * @param array $params
	 * @param array $sort
	 * @return array
	 */
    public static function getsBy($params, $sort = array())
    {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getSubCategory($parent_id) {
	    if (!$parent_id) return false;
	    return  self::_getDao()->getSubCategory($parent_id);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsData($params, $sort) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $list = self::_getDao()->getsBy($params, $sort);
	    return $list;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
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
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteBy($params=array()) {
	    if(empty($params))return false;
	    return self::_getDao()->deleteBy($params);
	}

	/**
	 *
	 * Enter desCategoryiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['image'])) $tmp['image'] = $data['image'];
		if (isset($data['root_id'])) $tmp['root_id'] = intval($data['root_id']);
		if (isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		return $tmp;
	}

	/**
	 *
	 * @return Dhm_Dao_Category
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Category");
	}
}
