<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Users
 * @author lichanghua
 *
 */
class Freedl_Dao_Users extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_users';
	protected $_primary = 'id';
	
	/**
	 *
	 * 根据参数统计流量消耗总数
	 * @param array $params
	 */
	public function getUsersList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT *,count(region) AS num FROM %s WHERE %s GROUP BY region %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getUsersCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM (SELECT *,count(region) AS num FROM %s WHERE %s GROUP BY region) AS a', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
