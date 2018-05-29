<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_Service_Subject{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllResourceSubject() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getCanUseSubjects($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseSubjects(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseSubjectCount($params);
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getResourceSubject($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceSubject($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateResourceSubjectTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteResourceSubject($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceSubject($data) {
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
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Subject
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Subject");
	}
	
	/**
	 *
	 * @return Game_Dao_IdxGameSubject
	 */
	private static function _getIdxGameResourceSubjectDao() {
		return Common::getDao("Game_Dao_IdxGameResourceSubject");
	}
}
