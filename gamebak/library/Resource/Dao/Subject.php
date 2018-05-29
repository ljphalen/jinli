<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_Subject
 * @author lichanghau
 *
 */
class Resource_Dao_Subject extends Common_Dao_Base{
	protected $_name = 'game_resource_subject';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
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
		$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function addSubject($data) {
		$time = Common::getTime();
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY start_time DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
}
