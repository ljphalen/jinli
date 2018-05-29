<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_Service_Keyword{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllResourceKeyword() {
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
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 根据时间搜索
	 * @param unknown_type $params
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @return multitype:unknown multitype:
	 */
	public function getByTime($page, $limit, $start_time, $end_time, $params) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getByTime($params, $start, $limit, $start_time, $end_time);
		$total = self::_getDao()->countByTime($params, $start_time, $end_time);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseResourceKeywords($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseResourceKeywords(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseResourceKeywordCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getResourceKeyword($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceKeyword($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateResourceKeywordTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteResourceKeyword($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchKeyword($data) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getDao()->deleteBy(array('id'=>$value));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateBatchKeyword($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchKeyword($ids, $status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceKeyword($data) {
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
		if(isset($data['ktype'])) $tmp['ktype'] = intval($data['ktype']);
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Keyword
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Keyword");
	}
}
