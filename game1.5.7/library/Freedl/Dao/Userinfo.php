<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Userinfo
 * @author lichanghua
 *
 */
class Freedl_Dao_Userinfo extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_userinfo';
	protected $_primary = 'id';
	
	
	/**
	 *
	 * 根据参数统计流量消耗总数
	 * @param array $params
	 */
	public function getCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(consume) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 根据活动id获取当前的下载量
	 */
	public function countByDlTimes($activityId){
		$sql = sprintf("SELECT COUNT(`task_flag`) AS dltimes FROM %s WHERE `activity_id`=%s GROUP BY `activity_id`", $this->getTableName(), $activityId);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 查询同一个imsi同一个活动当天每个游戏的下载次数
	 * @param string $imsi imsi
	 * @param string $activityId 活动id
	 */
	public function getDlTimes($imsi, $activityId, $startTime = 0){
		$sql = sprintf("SELECT `game_id`, COUNT(`task_flag`) AS dltimes FROM %s WHERE `imsi`='%s' AND `activity_id`=%s AND TO_DAYS(FROM_UNIXTIME(`create_time`)) = TO_DAYS(now())", $this->getTableName(), $imsi, $activityId);
		//if($startTime) $sql.=' AND create_time > ' . $startTime;
		$sql.=' GROUP BY `game_id`';
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 查询特定imsi用户指定活动中流量记录
	 * @param string $imsi
	 * @param string $activityId
	 * @param number $type
	 * @return string
	 */
	public function getTraffic($imsi, $activityId, $type, $startTime = 0){
		switch ($type){
			case 1://当前小时
				$where = 'FROM_UNIXTIME(`create_time`, "%Y%m%d%H") = FROM_UNIXTIME(UNIX_TIMESTAMP(), "%Y%m%d%H")';
				break;
			case 2://当天
				$where = 'FROM_UNIXTIME(`create_time`, "%Y%m%d") = FROM_UNIXTIME(UNIX_TIMESTAMP(), "%Y%m%d")';
				break;
			case 3://当月
				$where = 'FROM_UNIXTIME(`create_time`, "%Y%m") = FROM_UNIXTIME(UNIX_TIMESTAMP(), "%Y%m")';
				break;
		}
		if($startTime) $where.=' AND create_time > ' . $startTime;
		
		$sql = sprintf("SELECT SUM(`consume`) FROM %s WHERE `imsi`='%s' AND `activity_id`=%s AND %s", $this->getTableName(), $imsi, $activityId, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
