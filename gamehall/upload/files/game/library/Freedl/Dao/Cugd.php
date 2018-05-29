<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Cugd
 * @author fanch
 *
 */
class Freedl_Dao_Cugd extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_cugd';
	protected $_primary = 'id';
	
	public function getList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
		if($params['query']){
			$query = $params['query'];
			unset($params['query']);
		}
		$where = Db_Adapter_Pdo::sqlWhere($params);
		if($query)	$where .=  ' AND '. $query;
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function count($params = array()) {
		if($params['query']){
			$query = $params['query'];
			unset($params['query']);
		}
		$where = Db_Adapter_Pdo::sqlWhere($params);
		if($query)	$where .=  ' AND '. $query;
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	
}