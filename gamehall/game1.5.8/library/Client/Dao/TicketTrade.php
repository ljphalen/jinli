<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Client_Dao_TicketTrade
 * author ljp
 */
class Client_Dao_TicketTrade extends Common_Dao_Base{
	protected $_name = 'game_client_ticket_trade';
	protected $_primary = 'id';
	
	/**
	 * 总的发放A券
	 * @param array $params
	 */
	public function getCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(denomination) as denomination FROM %s WHERE %s ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 总的发放A券人数
	 * @param array $params
	 */
	public function getNum($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(DISTINCT UUID) FROM %s WHERE %s GROUP BY send_type,sub_send_type', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public function getConsumeRank($page = 1, $limit = 10, $params = array() ){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$start = ($page - 1) * $limit;
		$sql = sprintf('SELECT uuid,SUM(denomination) AS denomination,update_time, consume_time FROM %s WHERE %s GROUP BY UUID  ORDER BY denomination DESC LIMIT %d, %d', $this->getTableName(), $where, intval($start), intval($limit));
		return  $this->fetcthAll($sql);
	}
	
	public function getListByEndTime($uuid, $sDate, $eDate) {
	    $sql = sprintf('SELECT * FROM %s WHERE uuid = "%s" AND (end_time BETWEEN "%s" AND "%s")', $this->getTableName(), $uuid, $sDate, $eDate);
	    return $this->fetcthAll($sql);
	}
	
	
	
	
}