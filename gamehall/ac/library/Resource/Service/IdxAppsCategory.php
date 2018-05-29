<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_IdxAppsCategory extends Common_Service_Base{


	/**
	 * 根据条件检索
	 */
	public static function getsBy($params, $orderBy=array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params ,$orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order = array('id'=>'DESC')) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxCategory($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateIdxCategory($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
		
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteIdxCategory($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIdxCategory($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * @param unknown $data
	 * @return boolean
	 */
	public static function updateBy($data, $params){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * @param unknown $data
	 */
	public static function deleteBy($params){
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return boolean
	 */
	public static function saveData($data){
		$category_id = $data['id'];
		$op_app = empty($data['ids'])? array() : $data['ids'];
		$idx_app = empty($data['idx_app']) ? array() : explode(',', html_entity_decode($data['idx_app']));
		//公共部分
		$upIdx = array_intersect($op_app, $idx_app);
		//增加
		$addIdx = array_diff($op_app,$upIdx);
		if($addIdx){
			foreach ($addIdx as $key => $value){
				$ret = self::addIdxCategory(array('category_id'=>$category_id, 'app_id'=>$value, 'status'=>1, 'create_time' => $time));
			}
			if(!$ret) return false;
		}
		//删除
		$delIdx = array_diff($idx_app,$upIdx);
		if($delIdx){
			$ret = self::deleteBy(array('category_id'=>$category_id,'app_id'=>array('IN', $delIdx)));
			if(!$ret) return false;
		}
		return true;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return boolean
	 */
	public static function saveSort($data){
		$pgroup_id = $data['id'];
		$op_ids = empty($data['ids'])? array() : $data['ids'];
		//更新排序数据
		$idxSort= empty($data['sort'])? array() : $data['sort'];
	
		if($op_ids){
			foreach($op_ids as $key => $value){
				$ret = self::updateIdxCategory(array('sort'=> $idxSort[$value], 'create_time' => $time), $value);
			}
			if(!$ret) return false;
		}
	
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
		if(isset($data['app_id'])) $tmp['app_id'] = $data['app_id'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Resource_Dao_Category
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_IdxAppsCategory");
	}
}
