<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Keywords
 * @author tiansh
 *
 */
class Client_Dao_Favorite extends Common_Dao_Base{
	protected $_name = 'client_favorite';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $sqlWhere
	 */
	public function searchBy($start, $limit, $sqlWhere = 1 ) {
		$sql = sprintf('SELECT  goods_id, count(id) as num FROM %s WHERE %s GROUP BY goods_id ORDER BY num DESC LIMIT %d,%d',$this->getTableName(), $sqlWhere, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param string $sqlWhere
	 * @return string
	 */
	public function searchCount($sqlWhere) {
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s GROUP BY goods_id', $this->getTableName(), $sqlWhere);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND create_time > %d', $params['start_time']);
			unset($params['start_time']);
		}
		if ($params['end_time']) {
			$sql.= sprintf(' AND create_time < %d', $params['end_time']);
			unset($params['end_time']);
		}
		if($params['goods_id']) {
			$where .= " AND goods_id = " . Db_Adapter_Pdo::quote($params['goods_id']);
		}
		if($params['goods_title']) {
			$where .= " AND goods_title like '%" . Db_Adapter_Pdo::_filterLike($params['goods_title']) . "%'";
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
