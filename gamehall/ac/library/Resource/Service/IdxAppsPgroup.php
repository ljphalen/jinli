<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_IdxAppsPgroup extends Common_Service_Base{


	/**
	 * 根据条件检索
	 */
	public static function getsBy($params,$orderBy=array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateIdxPgroup($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
		
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteIdxPgroup($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIdxPgroup($data) {
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
		$pgroup_id = $data['id'];
		$op_app = empty($data['ids'])? array() : $data['ids'];
		$idx_app = empty($data['idx_app']) ? array() : explode(',', html_entity_decode($data['idx_app']));
		$time = $data['create_time'];
		//公共部分
		$upIdx = array_intersect($op_app, $idx_app);
		//增加
		$addIdx = array_diff($op_app,$upIdx);
		if($addIdx){
			foreach ($addIdx as $key => $value){
				$ret = self::addIdxPgroup(array('pgroup_id'=>$pgroup_id, 'app_id'=>$value, 'status'=>1, 'create_time' => $time));
			}
			if(!$ret) return false;
		}
		//删除
		$delIdx = array_diff($idx_app,$upIdx);
		if($delIdx){
			$ret = self::deleteBy(array('pgroup_id'=>$pgroup_id,'app_id'=>array('IN', $delIdx)));
			if(!$ret) return false;
		}
		return true;
	}
	
	public static function saveSort($data){
		$pgroup_id = $data['id'];
		$op_ids = empty($data['ids'])? array() : $data['ids'];
	    //更新排序数据
		$idxSort= empty($data['sort'])? array() : $data['sort'];
		$time = $data['create_time'];
		if($op_ids){
			foreach($op_ids as $key => $value){
				$ret = self::updateIdxPgroup(array('sort'=> $idxSort[$value], 'create_time' => $time), $value);
			}
			if(!$ret) return false;
		}
		
		return true;
	}
	
	/**
	 * 应用内容发生变更修改索引中包含该APP的时间
	 */
	public static function upIdxTime($appId,$time){
		//更新时间
		$idxApp = self::getsBy(array('app_id'=>$appId));
		if(count($idxApp)){
			self::updateBy(array('create_time' => $time),array('app_id'=>$appId));
			//更新数据版本
			Resource_Service_Config::setValue('Apps_Data_Version', Common::getTime());
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['pgroup_id'])) $tmp['pgroup_id'] = $data['pgroup_id'];
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
		return Common::getDao("Resource_Dao_IdxAppsPgroup");
	}
}
