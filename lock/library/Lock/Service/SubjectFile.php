<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_SubjectFile{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSubjectFile($id) {
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
	 * Enter description here ...
	 * @param int $subject_id
	 */
	public static function deleteBySubjectId($subject_id) {
		if (!$subject_id) return false;
		return self::_getDao()->deleteBy(array('subject_id'=>$subject_id));
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
	 * @param int $file_id
	 * @return multitype:
	 */
	public static function getBySubjectId($subject_id) {
		if (!$subject_id) return false;
		return self::_getDao()->getsBy(array('subject_id'=>$subject_id),  array('id'=>'ASC'));
	}
	
	/**
	 *
	 * @param int $file_id
	 * @return multitype:
	 */
	public static function getByFileId($file_id) {
		if (!$file_id) return false;
		return self::_getDao()->getsBy(array('file_id'=>$file_id), array('id'=>'ASC'));
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
	 * Enter desCrcategoryiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getCanuseSubjectFiles($subject_id, $file_ids) {
		if (!intval($subject_id)) return false;
		return self::_getDao()->getCanuseSubjectFiles($subject_id, $file_ids);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params) {
		if (!is_array($params)) return false;
		$ret = self::_getDao()->getsBy($params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
		if(isset($data['subject_id'])) $tmp['subject_id'] = $data['subject_id'];
		if(isset($data['lock_id'])) $tmp['lock_id'] = $data['lock_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_SubjectFile
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_SubjectFile");
	}
}
