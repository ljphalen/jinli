<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Tuijian
 * @author lichanghau
 *
 */
class Client_Dao_Tuijian extends Common_Dao_Base{
	protected $_name = 'idx_game_client_news';
	protected $_primary = 'id';
	
	
	public function getAllSortTuijian() {
		$sql = sprintf('SELECT * FROM %s ORDER BY sort DESC,id DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseTuijians($start, $limit, $params) {
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
	public function getCanUseTuijianCount($params) {
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
	public function getCanUseTuijiansByIds($start, $limit, $params) {
		$time = Common::getTime();
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND id IN %s ORDER BY FIELD %s LIMIT %d,%d',$this->getTableName(),$time, $time,  Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']), $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseTuijianByIdsCount($params) {
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
	public function addTuijian($data) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE status = 1  AND %s ORDER BY start_time DESC LIMIT %d,%d',$this->getTableName(),  $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	public function updateTuijianStatus($id,$statu) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id = %d', $this->getTableName(), $statu, $id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function getTuijiansByNId($id) {
		$sql = sprintf('SELECT * FROM %s WHERE n_id = %d', $this->getTableName(), $id);
		return Db_Adapter_Pdo::fetch($sql);
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
