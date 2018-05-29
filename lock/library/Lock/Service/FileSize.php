<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_FileSize{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFileSize($id) {
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
	public static function getBySizeId($size_id) {
		if (!$size_id) return false;
		return self::_getDao()->getBySizeId($size_id);
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
	public static function getFileIdByFilesizeIds($file_id, $size_ids) {
		if (!intval($file_id) || !is_array($size_ids)) return false;
		return self::_getDao()->getFileIdByFilesizeIds($file_id, $size_ids);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['size_id'])) $tmp['size_id'] = $data['size_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_FileSize
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_FileSize");
	}
}
