<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_FateLog
 * @author rainkid
 *
 */
class Client_Dao_FateLog extends Common_Dao_Base{
	protected $_name = 'game_client_lottery_log';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND create_time >= %d', $params['start_time']);
			unset($params['start_time']);
		}
		if ($params['end_time']) {
			$sql.= sprintf(' AND create_time <= %d', $params['end_time']);
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
	/**
	 * 分组统计该活动中奖情况
	 * @param unknown $acId
	 */
	public function groupBy($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT `lottery_id`, COUNT(`lottery_id`) AS num FROM %s WHERE %s GROUP BY `lottery_id`', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
