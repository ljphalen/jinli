<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_Subject
 * @author lichanghua
 *
 */
class Gc_Dao_Subject extends Common_Dao_Base{
	protected $_name = 'gc_subject';
	protected $_primary = 'id';
	
	
	/**
	 *
	 * get all data list
	 */
	public function getAllSubjectSort() {
		$sql = sprintf('SELECT * FROM %s ORDER BY sort DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * get all data count
	 */
	public function getAllSubjectSortCount() {
		$sql = sprintf('SELECT count(*) FROM %s ORDER BY sort DESC',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY start_time DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
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
}
