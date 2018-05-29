<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Client_Service_TaskStatisticReport{
     const STEP = 1;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy= array('start_time'=>'DESC', 'id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNum($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getNum($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCount($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getCount($params);
	}

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getBy($params = array() , $orderBy = array('id'=>'ASC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getsBy($params = array(), $orderBy = array('id'=>'ASC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateById($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteById($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @param unknown_type $btype
	 * @return Ambigous <boolean, mixed>
	 */
	public static function mutinsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiInsert($data);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function mutiFieldInsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiFieldInsert($data);
	}
	
	
	public static function updateStatisticReportPoints( $taskType, $subTaskType, $points) {
		if(!$taskType || !$subTaskType || !$points ){
			return false;
		}
		$searchParams['task_type']     = $taskType;
		$searchParams['task_sub_type'] = $subTaskType;
		$result = self::getBy($searchParams);
		$updateData['points'] = $points;
		if($result['task_type']){
			self::updateBy($updateData, $searchParams);
		}else{	
			$updateData['task_type'] = $taskType;
			$updateData['task_sub_type'] = $subTaskType;
			self::insert($updateData);
		}
	}
	
	public static function updateStatisticReporTickets( $taskType, $subTaskType, $tickets) {
		if(!$taskType || !$subTaskType || !$tickets){
			return false;
		}
		$searchParams['task_type'] = $taskType;
		$searchParams['task_sub_type'] = $subTaskType;
		$result = self::getBy($searchParams);
		$updateData['tickets'] = $tickets;
		if($result['task_type']){
			self::updateBy($updateData, $searchParams);
		}else{
			$updateData['task_type'] = $taskType;
			$updateData['task_sub_type'] = $subTaskType;
			self::insert($updateData);
		}
	}
	
	public static function updateStatisticReporPeopleNum( $taskType, $subTaskType, $totalNum = 1) {
		if(!$taskType || !$subTaskType){
			return false;
		}
		$searchParams['task_type'] = $taskType;
		$searchParams['task_sub_type'] = $subTaskType;
		$result = self::getBy($searchParams);
		if($result['task_type']){
			$updateData['people_number'] = $result['people_number'] + (($totalNum == 1) ? self::STEP:$totalNum);
			self::updateBy($updateData, $searchParams);
		}else{
			$insertData['people_number'] =  (($totalNum == 1) ? self::STEP:$totalNum);
			$insertData['task_type'] = $taskType;
			$insertData['task_sub_type'] = $subTaskType;
			self::insert($insertData);
		}
	}
	
	
	
	/*
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['task_type'])) $tmp['task_type'] = $data['task_type'];
		if(isset($data['task_sub_type'])) $tmp['task_sub_type'] = $data['task_sub_type'];
		if(isset($data['tickets'])) $tmp['tickets'] = $data['tickets'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['people_number'])) $tmp['people_number'] = $data['people_number'];
		return $tmp;
	}

	
	/**
	 * 
	 * @return Client_Dao_TaskStatisticReport
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_TaskStatisticReport");
	}
}