<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_FileType{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFileType() {
		return array(self::_getDao()->count(), self::_getDao()->getAllFileType());
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
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFileType($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRandTypes($type_id) {
		if (!intval($type_id)) return false;
		return self::_getDao()->getRandTypes(intval($type_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param string $name
	 */
	public static function getFileTypeByName($name) {
		if (!$name) return false;
		return self::_getDao()->where(array('name'=>$name));
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFileType($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFileType($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFileType($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getByIds($ids);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_FileType
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_FileType");
	}
}
