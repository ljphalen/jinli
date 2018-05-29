<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Attribute extends Common_Service_Base{


	/**
	 * 根据条件检索
	 */
	public static function getsBy($params){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAttribute($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateAttribute($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
		
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAttribute($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAttribute($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * @param unknown_type $ids
	 * @param unknown_type $status
	 * @throws Exception
	 * @return boolean
	 */
	public static function updateReleation($data, $id) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新属性
			self::updateAttribute($data, $id);
			//更新分类索引
			$type = $data['at_type'];
			if($type == 1){
			   Resource_Service_IdxAppsCategory::updateBy(array('status' => $data['status']), array('category_id' => $id));
			}
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['at_type'])) $tmp['at_type'] = $data['at_type'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Resource_Dao_Attribute
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Attribute");
	}
}
