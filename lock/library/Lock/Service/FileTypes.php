<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_FileTypes{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFileTypes($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByFileId($file_id) {
		if (!$file_id) return false;
		return self::_getDao()->deleteByFileId(intval($file_id));
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAdd($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByFileId($file_id) {
		if (!$file_id) return false;
		return self::_getDao()->getByFileId($file_id);
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByTypeId($type_id) {
		if (!$type_id) return false;
		return self::_getDao()->getByTypeId($type_id);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getByFileIds($file_ids) {
		if (!is_array($file_ids)) return false;
		return self::_getDao()->getByFileIds($file_ids);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getFileIdByFiletypeIds($file_id, $type_ids) {
		if (!intval($file_id) || !is_array($type_ids)) return false;
		return self::_getDao()->getFileIdByFiletypeIds($file_id, $type_ids);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_FileTypes
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_FileTypes");
	}
}
