<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_Lock extends Common_Service_Base{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter desCrcategoryiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order_by = array()) {
		$data = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $data, $order_by);
		$total = self::_getDao()->count($data);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter desCrcategoryiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getCanuseFiles($page = 1, $limit = 10, $ids, $order_by = array(), $index = false) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		
		if($index) {
			if ($page == 1) {
				$limit = $limit -1;
			} else {
				$start = $start -1;
			}
		}
		
		$ret = self::_getDao()->getCanuseFiles($start, $limit, $ids, $order_by);
		$total = self::_getDao()->getCanuseFilesCount($ids);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getLock($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
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
	public static function deleteBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
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
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getByIds($ids);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLock($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getLastInsertId(){
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateLock($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFile($id) {
		return self::_getDao()->delete(intval($id));
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['keyword'])) $tmp['keyword'] = $data['keyword'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_Lock
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_Lock");
	}
}
