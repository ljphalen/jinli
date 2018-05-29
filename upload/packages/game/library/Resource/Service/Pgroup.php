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
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @return multitype:unknown multitype:
	 */
	public function getSortPgroup($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getSortPgroup($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->countSortPgroup($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
	
		$sort = array('id'=>'DESC');
		if (count($params['ids'])) $sort = array('FIELD'=>self::_getDao()->quoteInArray($params['ids']));
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $sort);
		$total = self::_getDao()->searchCount($sqlWhere);
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
	 * @param unknown_type $id
	 */
	public static function getPgroupByOutId($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getPgroupByOutId(intval($id));
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
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updatePgroupTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deletePgroup($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchPgroup($data) {
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
	public static function updateBatchPgroup($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchPgroup($ids, $status);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getPgroupBymodel($model) {
		if (!$model) return false;
		return self::_getDao()->getPgroupBymodel($model);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getPgroupBymodels($model) {
		if (!$model) return false;
		return self::_getDao()->getPgroupBymodels($model);
	}
	
	/**
	 * 
	 * @param unknown_type $model_id
	 * @return boolean|Ambigous <multitype:, mixed>
	 */
	public static function getPgroupBymodelId($model_id) {
		if (!$model_id) return false;
		return self::_getDao()->getPgroupBymodelId($model_id);
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
	
	public static function batchAddByPgroup($data,$title) {
		if (!is_array($data)) return false;
		$temp = $tmp = array();
		foreach($data as $key=>$value){
			$info = Resource_Service_Type::getResourceType($value);
			$temp[] = $info['title'];
		}
		$tmp['id'] = '';
		$tmp['title'] = $title;
		$tmp['p_title'] = implode(",",$temp);
		$tmp['p_id'] = implode(",",$data);
		$tmp['create_time'] = Common::getTime();
		return self::_getDao()->insert($tmp);
	}
	
	public static function batchUpdateByPgroup($data,$id,$title) {
		if (!is_array($data)) return false;
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$temp = $tmp =  $ids = array();
		if($info['p_id']){
			$tmp = explode(',',$info['p_id']);
		} else {
			$tmp = array();
		}
		
		
	    foreach($data as $key=>$value){
				array_push($tmp,$value);
		}
		$ids = $tmp;
		foreach($ids as $k=>$val){
		    $t_info = Resource_Service_Type::getResourceType($val);
			$temp[] = $t_info['title'];
		}
		
		$tmp['id'] = $id;
		$tmp['title'] = $title;
		$tmp['p_title'] = ($ids ? implode(",",$temp ) : '');
		$tmp['p_id'] = ($ids ? implode(",",$ids) : '');
		$tmp['create_time'] = Common::getTime();
		return self::updatePgroup($tmp,$id);
	}
	
	public static function batchDeleteByPgroup($data,$id,$title) {
		if (!is_array($data)) return false;
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$temp = $tmp =  $ids = array();
		$tmp = explode(',',$info['p_id']);
		$ids = array_diff($tmp,$data);
		if($ids){
			foreach($ids as $key=>$value){
				$t_info = Resource_Service_Type::getResourceType($value);
				$temp[] = $t_info['title'];
			}
				
		}
		
		$tmp['id'] = $id;
		$tmp['title'] = $title;
		$tmp['p_title'] = ($ids ? implode(",",$temp ) : '');
		$tmp['p_id'] = ($ids ? implode(",",$ids) : '');
		$tmp['create_time'] = Common::getTime();
		return self::updatePgroup($tmp,$id);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['p_title'])) $tmp['p_title'] = $data['p_title'];
		if(isset($data['p_id'])) $tmp['p_id'] = $data['p_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['ids'])) $tmp['ids'] = $data['ids'];
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
