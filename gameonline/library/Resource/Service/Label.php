<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desLabeliption here ...
 * @author lichanghau
 *
 */
class Resource_Service_Label{

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllLabel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSortLabel() {
		return array(self::_getDao()->count(), self::_getDao()->getSortAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getLabel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
    public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getUseLabel($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getUseLabel($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->getUseLabelcount($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLabel($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateLabel($data, $id){
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteLabel($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['btype'])) $tmp['btype'] = intval($data['btype']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];		
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Resource_Dao_Label
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Label");
	}
}
