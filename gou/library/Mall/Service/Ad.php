<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Mall_Service_Ad{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllMallad() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllMalladSort() {
		return array(self::_getDao()->getAllMalladCount(), self::_getDao()->getAllMalladSort());
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $guideTypes
	 * @return boolean|multitype:
	 */
	public static function getByGuideTypes($guideTypes) {
		if (!is_array($guideTypes)) return false;
		return self::_getDao()->getByGuideTypes($guideTypes);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseMallads($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseMallads(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseMalladCount($params);
		return array($total, $ret); 
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getMallad($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateMallad($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateMalladTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(11, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteMallad($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addMallad($data) {
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
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['ad_type'])) $tmp['ad_type'] = intval($data['ad_type']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['area_id'])) $tmp['area_id'] = intval($data['area_id']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Mall_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Mall_Dao_Ad");
	}
}
