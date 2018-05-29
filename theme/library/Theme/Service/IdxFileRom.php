<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_IdxFileRom{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxFileRom($id) {
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
		return self::_getDao()->deleteBy(array('file_id'=>$file_id));
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
		return self::_getDao()->getsBy(array('file_id'=>$file_id),  array('id'=>'ASC'));
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByRomId($rom_id) {
		if (!$rom_id) return false;
		return self::_getDao()->getsBy(array('rom_id'=>$rom_id),  array('id'=>'ASC'));
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
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['rom_id'])) $tmp['rom_id'] = $data['rom_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Theme_Dao_IdxFileRom
	 */
	private static function _getDao() {
		return Common::getDao("Theme_Dao_IdxFileRom");
	}
}
