<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Glog_Service_TrafficLog
 * @author fanch
 *
 */
class Glog_Service_TrafficLog{

	/**
	 * 按表名查询数据
	 * @param string $tableName 数据表名
	 * @param int $limit 获取的条数
	 * @param array $params 查询参数
	 * 
	 */
	public static function getLimit($tableName, $params = array(), $limit = 10) {
		if(empty($tableName)) return false;
		return self::_getDao()->getLimit($tableName, $params, $limit = 10);
	}
	
	/**
	 * 增加日志方法
	 * @param unknown $data
	 * @return boolean
	 */
	public static function addLog($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}
	

	/**
	 * 获取最后一条的id编号
	 */
	public static function getLastInsertId(){
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['activityId'])) $tmp['activity_id'] = $data['activityId'];
		if(isset($data['imsi'])) $tmp['imsi'] = $data['imsi'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sysVersion'])) $tmp['sys_version'] = $data['sysVersion'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		if(isset($data['operator'])) $tmp['operator'] = $data['operator'];
		if(isset($data['ntype'])) $tmp['ntype'] = $data['ntype'];
		if(isset($data['gameId'])) $tmp['game_id'] = $data['gameId'];
		if(isset($data['gameName'])) $tmp['game_name'] = urldecode(html_entity_decode($data['gameName']));;
		if(isset($data['gameSize'])) $tmp['game_size'] = $data['gameSize'];
		if(isset($data['taskFlag'])) $tmp['task_flag'] = $data['taskFlag'];
		if(isset($data['uploadSize'])) $tmp['upload_size'] = $data['uploadSize'];
		if(isset($data['taskStatus'])) $tmp['task_status'] = $data['taskStatus'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_OriginalLog
	 */
	private static function _getDao() {
		return Common::getDao("Glog_Dao_TrafficLog");
	}
}
