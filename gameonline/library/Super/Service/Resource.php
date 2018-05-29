<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Super_Service_Resource{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 根据字段判断
	 * @param array $data
	 */
	public static function getBy($data) {
		$params = self::_cookData($data);
		return self::_getDao()->getBy($params);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:string unknown
	 */
	public static function search($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookSql($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, array('sort'=>'DESC'));
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		if(isset($data['company'])) $tmp['company'] = $data['company'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['ptype'])) $tmp['ptype'] = intval($data['ptype']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_Resource
	 */
	private static function _getDao() {
		return Common::getDao("Super_Dao_Resource");
	}
}
