<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_Service_Pgroup{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllPgroup() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
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
	public static function getPgroup($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addPgroup($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updatePgroup($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//更新机组索引数据
		Resource_Service_IdxAppsPgroup::updateBy(array('status' => $data['status']), array('pgroup_id' => intval($id)));
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 更新机型数据
	 * @param array $data
	 * @param int $id
	 */
	public static function updateModel($data, $id){
	   $params = array();
	   $params['p_id'] = array('FIND', $id);
	   $groups = self::getsBy($params);
	   if($groups){
	   	foreach ($groups as $key => $value){
	   		$tmp = array();
	   		$p_id = explode(',', $value['p_id']);
	   		$p_title = explode(',', $value['p_title']);
	   		$tmp = array_combine($p_id,$p_title);
	   		$tmp[strval($id)] = $data['title'];
	   		$p_title = implode(',', $tmp);
	   		self::updatePgroup(array('p_title' => $p_title), $value['id']);
	   	}
	   }
	   return true;
	}
	
	/**
	 * 删除机型数据
	 * @param array $data
	 * @param int $id
	 */
	public static function delModel($id){
		$params = array();
		$params['p_id'] = array('FIND', $id);
		$groups = self::getsBy($params);
		if($groups){
			foreach ($groups as $key => $value){
				$tmp = array();
				$p_id = explode(',', $value['p_id']);
				$p_title = explode(',', $value['p_title']);
				$tmp = array_combine($p_id,$p_title);
				unset($tmp[strval($id)]);
				$p_id = implode(',', array_keys($tmp));
				$p_title = implode(',', $tmp);
				self::updatePgroup(array( 'p_id' => $p_id, 'p_title' => $p_title), $value['id']);
			}
		}
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deletePgroup($id) {
		//删除机组索引数据
		Resource_Service_IdxAppsPgroup::deleteBy(array('pgroup_id' => intval($id)));
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
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['p_title'])) $tmp['p_title'] = $data['p_title'];
		if(isset($data['p_id'])) $tmp['p_id'] = $data['p_id'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Pgroup
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Pgroup");
	}
}
