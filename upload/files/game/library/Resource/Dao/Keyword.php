<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_Keyword
 * @author lichanghua
 *
 */
class Resource_Dao_Keyword extends Common_Dao_Base{
	protected $_name = 'game_resource_keyword';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceKeywords($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time <= %d AND end_time >= %d AND %s ORDER BY sort DESC ,id DESC LIMIT %d,%d',$this->getTableName(),$time, $time, $where ,$start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceKeywordCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time <= %d AND end_time >= %d AND %s',$this->getTableName(),$time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @return multitype:
	 */
	public function getByTime($params, $start, $limit, $start_time=0, $end_time=0) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		if ($start_time && $end_time) $timeSql = sprintf('AND start_time >= %d AND end_time <= %d ', $start_time, $end_time);
		$sql = sprintf('SELECT * FROM %s WHERE 1 %s AND %s ORDER BY sort DESC ,id DESC LIMIT %d,%d',$this->getTableName(), $timeSql, $where ,$start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @return string
	 */
	public function countByTime($params, $start_time=0, $end_time=0) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		if ($start_time && $end_time) $timeSql = sprintf('AND start_time >= %d AND end_time <= %d ', $start_time, $end_time);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE 1 %s AND %s',$this->getTableName(), $timeSql, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $statu
	 * @return mixed
	 */
	public function updateBatchKeyword($params,$status) {
		$sql = sprintf('UPDATE  %s SET status = %s WHERE id IN  %s', $this->getTableName(), $status,  Db_Adapter_Pdo::quoteArray($params));
		return Db_Adapter_Pdo::execute($sql,array(), false);
	}
	
	/**
	 *
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
}
