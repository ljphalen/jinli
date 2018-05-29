<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desGoodsLabeliption here ...
 * @author tiansh
 *
 */
class Gc_Service_GoodsLabel{
	

	/**
	 *
	 * Enter desGoodsLabeliption here ...
	 */
	public static function getAllGoodsLabel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getGoodsLabel($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param array $params
	 * @return multitype:
	 */
	public function getListByParentId($parent_id) {
		if (!intval($parent_id)) return false;
		return self::_getDao()->getListByParentId($parent_id);
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getParentList() {
		return self::_getDao()->getParentList();
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getChildList() {
		return self::_getDao()->getChildList();
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentIds($pids) {
		if (!count($pids)) return false;
		return self::_getDao()->getListByParentIds($pids);
	}
	
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseLabels($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseLabels(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseLabelsCount($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter desGoodsLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGoodsLabel($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateGoodsLabel($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteGoodsLabel($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desGoodsLabeliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Gc_Dao_GoodsLabel
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_GoodsLabel");
	}
}
