<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Browser_Service_Recmark{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllIndexRecmarks() {
		return array(self::_getDao()->count(), self::_getDao()->getAllRecmarks());
	}
	
	/**
	 *
	 */
	public function getCanUseRecmarks($model_id) {
		if (!$model_id) return false;
		return self::_getDao()->getCanUseRecmarks($model_id);
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
	public static function getRecmark($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRecmark($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRecmark($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRecmark($data) {
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
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['model_id'])) $tmp['model_id'] = $data['model_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Browser_Dao_Recmark
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_Recmark");
	}
}
