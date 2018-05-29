<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Subject
 * @author lichanghau
 *
 */
class Client_Dao_Subject extends Common_Dao_Base{
	protected $_name = 'game_client_subject';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d  AND %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(),  $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjectCount($params) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s',$this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjectByIdsCount($params) {
		$time = Common::getTime();
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND id IN %s ORDER BY FIELD %s',$this->getTableName(),$time, $time,  Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']));
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function addSubject($data) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1  AND %s ORDER BY start_time DESC LIMIT %d,%d',$this->getTableName(),  $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	
	public function batchUpdate($data,$statu) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id IN %s', $this->getTableName(), $statu, Db_Adapter_Pdo::quoteArray($data));
		return Db_Adapter_Pdo::execute($sql, array(), false);
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
