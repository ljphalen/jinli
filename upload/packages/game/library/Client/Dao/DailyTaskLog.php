<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Client_Dao_DailyTaskLog
 * @author lichanghua
 */
class Client_Dao_DailyTaskLog extends Common_Dao_Base{
	protected $_name = 'game_client_daily_task_log';
	protected $_primary = 'id';
	
	
	public function getByUuidList($start = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC', 'create_time'=>'DESC')){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT uuid,SUM(denomination) AS denomination, task_id, send_object, create_time FROM %s WHERE %s   GROUP BY uuid,send_object  %s  LIMIT %d, %d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return  $this->fetcthAll($sql);
	}
	
	public function getByUuidCount($params = array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s GROUP BY uuid,send_object ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	

	/**
	 * 总的发放总额
	 * @param array $params
	 */
	public function getTotal($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(denomination) FROM %s WHERE %s ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 总的发放人数
	 * @param array $params
	 */
	public function getNum($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(DISTINCT uuid) FROM %s WHERE %s GROUP BY task_id', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}