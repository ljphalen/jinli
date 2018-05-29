<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Freedl_Service_Userinfo{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	
	public static function getBy($params, $orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	public static function updateBy($data, $params){
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	public static function insert($data){
		if(!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 查询同一个imsi同一个活动中当天每个游戏的下载次数
	 * @param string $imsi
	 * @param string $activityId
	 */
	public static function getDlTimes($log, $startTime){
		if(!$log) return  false;
		$imsi = $log['imsi'];
		$activityId =  $log['activity_id'];
		return self::_getDao()->getDlTimes($imsi, $activityId, $startTime);
	}
	
	/**
	 * 根据活动获取对应的下载量
	 * @param int $activityId
	 */
	public static function countByDlTimes($activityId){
		if(!$activityId) return false;
		return self::_getDao()->countByDlTimes($activityId);
	}
	
	/**
	 * 获取特定imsi在指定活动中流量数据
	 * @param array $log
	 * @param number $type [1:一小时, 2:一天, 3:一月]
	 * @return int
	 */
	public static function getTraffic($log, $type, $startTime){
		if(!$log|| !$type) return  false;
		$imsi = $log['imsi'];
		$activityId =  $log['activity_id'];
		return self::_getDao()->getTraffic($imsi, $activityId, $type, $startTime);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC','create_time'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getCount($params = array()) {
		return self::_getDao()->getCount($params);
	}


	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['activity_id'])) $tmp['activity_id'] = $data['activity_id'];
		if(isset($data['imsi'])) $tmp['imsi'] = $data['imsi'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		if(isset($data['receive_time'])) $tmp['receive_time'] = $data['receive_time'];
		if(isset($data['year_month'])) $tmp['year_month'] = $data['year_month'];
		if(isset($data['activity_name'])) $tmp['activity_name'] = $data['activity_name'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_name'])) $tmp['game_name'] = $data['game_name'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['task_flag'])) $tmp['task_flag'] = $data['task_flag'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['consume'])) $tmp['consume'] = $data['consume'];
		if(isset($data['refresh_time'])) $tmp['refresh_time'] = $data['refresh_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_Userinfo
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Userinfo");
	}
}
