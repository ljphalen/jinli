<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Service_FateLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFateLog() {
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
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, array('id'=>'DESC'));
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getlogByStatus($status,$activity_id) {
		return self::_getDao()->count(array('status'=>$status,'activity_id'=>$activity_id));
	}
	
	/**
	 * 分组统计该活动各奖项中奖情况
	 * @param unknown $acId
	 */
	public static function groupBy($data){
		if (!is_array($data)) return false;
		return self::_getDao()->groupBy($data);
	}
	
	/**
	 * 
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogsByImei($imeicrc,$activity_id) {
		if (!$imeicrc && !$activity_id) return false;
		$curr_time = date('Y-m-d',Common::getTime());
		$start_time = strtotime($curr_time.' 00:00:00');
		$end_time = strtotime($curr_time.' 23:59:59');
		$params = array(
				'imeicrc' => intval($imeicrc),
				'activity_id' => $activity_id,
				'create_time' => array(
						   array('>=', $start_time), array('<=', $end_time)
						),
				);
		return self::_getDao()->getsBy($params, array('create_time'=>'DESC'));
	}
	
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogsByUname($uname,$activity_id) {
		if (!$uname && !$activity_id) return false;
		$curr_time = date('Y-m-d',Common::getTime());
		$start_time = strtotime($curr_time.' 00:00:00');
		$end_time = strtotime($curr_time.' 23:59:59');
		$params = array(
				'uname' => $uname,
				'activity_id' => $activity_id,
				'create_time' => array(
						array('>=', $start_time), array('<=', $end_time)
				),
		);
		return self::_getDao()->getsBy($params, array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogByImei($imei) {
		if (!$imei) return false;
		return self::_getDao()->getsBy(array('imeicrc'=>$imei), array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogByUname($uname) {
		if (!$uname) return false;
		return self::_getDao()->getsBy(array('uname'=>$uname), array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getFateLogsByLotteryId($activity_id,$lottery_id) {
		if (!$activity_id && !$lottery_id) return false;
		return self::_getDao()->getBy(array('activity_id'=>$activity_id,'lottery_id'=>$lottery_id), array('create_time'=>'DESC'));
	}
	
	/**
	 * 通过活动id，奖项id查找该奖项的中奖数量
	 * @param unknown_type $activity_id
	 * @param unknown_type $lottery_id
	 * @return boolean|string
	 */
	public static function countFateLogsByLotteryId($activity_id,$lottery_id) {
		if (!$activity_id && !$lottery_id) return false;
		return self::_getDao()->count(array('activity_id'=>$activity_id,'lottery_id'=>$lottery_id));
	}
	
	/**
	 * 通过活动id，奖项id查找该奖项的每天抽奖的总的数量
	 * @param unknown_type $activity_id
	 * @param unknown_type $lottery_id
	 * @return boolean|string
	 */
	public static function countFateLogsEveryByLotteryId($activity_id,$lottery_id) {
		if (!$activity_id && !$lottery_id) return false;
		$curr_time = date('Y-m-d',Common::getTime());
		$start_time = strtotime($curr_time.' 00:00:00');
		$end_time = strtotime($curr_time.' 23:59:59');
		$params = array(
				'lottery_id' => intval($lottery_id),
				'activity_id' => intval($activity_id),
				'create_time' => array(
						array('>=', $start_time), array('<=', $end_time)
				),
		);
		return self::_getDao()->count($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFateLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFateLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function updateFateLogGrantStatus($status, $remark, $id) {
		if (!$id) return false;
		$time = Common::getTime();
		if(!$status) $time = '';
		$log = Client_Service_FateLog::getFateLog($id);
		if($log['grant_status']) $time = $log['grant_time'];
		return self::_getDao()->update(array('grant_status'=>$status,'grant_time'=>$time,'remark'=>$remark),$id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByFateLog($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFateLog($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFateLog($data) {
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
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['activity_id'])) $tmp['activity_id'] = $data['activity_id'];
		if(isset($data['lottery_id'])) $tmp['lottery_id'] = $data['lottery_id'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imeicrc'])) $tmp['imeicrc'] = intval($data['imeicrc']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['grant_status'])) $tmp['grant_status'] = intval($data['grant_status']);
		if(isset($data['grant_time'])) $tmp['grant_time'] = $data['grant_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['duijiang_code'])) $tmp['duijiang_code'] = $data['duijiang_code'];
		if(isset($data['remark'])) $tmp['remark'] = $data['remark'];
		if(isset($data['label_status'])) $tmp['label_status'] = intval($data['label_status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_FateLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_FateLog");
	}
}
