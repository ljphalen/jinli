<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Activity{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllActivity() {
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
		$ret = self::_getDao()->getList($start, $limit, $params,array('start_time'=>'DESC', 'id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getOnlineActivityInfo() {
		$params = array('status' => 1);
		$params['start_time'] = array('<=', Common::getTime());
		return self::_getDao()->getBy($params,array('start_time'=>'DESC','id'=>'DESC'));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function activitySearch($params) {
		return self::_getDao()->getsBy($params, array('start_time'=>'DESC','id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addMutiFateRule($data) {
		if (!is_array($data)) return false;
		return self::_getRuleDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateFateRule($data) {
		if (!is_array($data)) return false;
		$ret = array();
		foreach($data as $key=>$val){
			$ret = self::_getRuleDao()->update($val,$key);
		}
		return $ret;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function batchSort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateActivityStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
	    foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('status'=>$status), $value);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateLogGrantStatus($ids, $status) {
		if (!is_array($ids)) return false;
		$time = Common::getTime();
		foreach($ids as $key=>$value) {
			$log = Client_Service_FateLog::getFateLog($value);
			if(!$log['grant_status']){
				self::_getLogDao()->update(array('grant_status'=>$status,'grant_time'=>$time), $value);
			} 
			
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateLogLabelStatus($ids, $status) {
		if (!is_array($ids)) return false;
		foreach($ids as $key=>$value) {
			self::_getLogDao()->update(array('label_status'=>$status), $value);
		}
		return true;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRule($id) {
		if (!intval($id)) return false;
		return self::_getRuleDao()->getsBy(array('activity_id'=>intval($id)),array('lottery_id'=>'ASC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getOneRule($activity_id, $lottery_id) {
		if (!intval($activity_id) && !intval($lottery_id)) return false;
		return self::_getRuleDao()->getBy(array('activity_id'=>intval($activity_id),'lottery_id'=>$lottery_id));
	}


	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['number'])) $tmp['number'] = intval($data['number']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['online_start_time'])) $tmp['online_start_time'] = $data['online_start_time'];
		if(isset($data['online_end_time'])) $tmp['online_end_time'] = $data['online_end_time'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if(isset($data['award_time'])) $tmp['award_time'] = $data['award_time'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['min_version'])) $tmp['min_version'] = $data['min_version'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['message'])) $tmp['message'] = $data['message'];
		if(isset($data['popup_status'])) $tmp['popup_status'] = intval($data['popup_status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Activity
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Activity");
	}
	
	/**
	 * 
	 * @return Client_Dao_FateRule
	 */
	private static function _getRuleDao() {
		return Common::getDao("Client_Dao_FateRule");
	}
	
	/**
	 *
	 * @return Client_Dao_FateLog
	 */
	private static function _getLogDao() {
		return Common::getDao("Client_Dao_FateLog");
	}
}
