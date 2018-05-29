<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desTypeiption here ...
 * @author tiansh
 *
 */
class Type_Service_Ptype{
	

	/**
	 *
	 * Enter desTypeiption here ...
	 */
	public static function getAllType() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getType($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter desTypeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addType($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	public static function lastInsertId() {
		return self::_getDao()->getLastInsertId();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateType($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteType($id) {
		return self::_getDao()->delete(intval($id));
	}
	public static function deletes($field,$data) {
		return self::_getDao()->deletes($field,$data);
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
	 * @return array
	 */
	public static function getsBy($params, $sort=array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
	    if (!$id) return false;
	    Gou_Service_ClickStat::increment(26, $id);
	    return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter desTypeiption here ...
	 * @param unknown_type $data
	 */
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['pid'])) $tmp['pid'] = $data['pid'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['recommend'])) $tmp['recommend'] = $data['recommend'];
		if(isset($data['recommend_status'])) $tmp['recommend_status'] = $data['recommend_status'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Type_Dao_Ptype
	 */
	private static function _getDao() {
		return Common::getDao("Type_Dao_Ptype");
	}
}
