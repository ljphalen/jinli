<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Dao_SubjectGames
 * @author wupeng
 */
class Client_Dao_SubjectGames extends Common_Dao_Base {
	protected $_name = 'idx_game_client_subject';
	protected $_primary = 'id';
	
	public function getDistinctGames($start, $limit, $params, $orderBy) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sort = Db_Adapter_Pdo::sqlSort($orderBy);
	    $sql = sprintf('SELECT * FROM %s WHERE %s GROUP BY `game_id` %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
	    return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getDistinctCount($params) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sql = sprintf('SELECT COUNT(DISTINCT(`game_id`)) FROM %s WHERE %s', $this->getTableName(), $where);
	    return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}