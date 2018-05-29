<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Fate
 * @author rainkid
 *
 */
class Gou_Dao_FateLog extends Common_Dao_Base{
	protected $_name = 'gou_fate_log';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param unknown_type $time
	 * @return mixed
	 */
	public function getFateLogsByTime($start, $end) {
		$sql = sprintf('SELECT * FROM %s WHERE create_time > %d AND create_time < %d AND status > 0', $this->_name, $start, $end);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $mobile
	 */
	public function getFateLogsByMobile($mobile) {
		$sql = sprintf('SELECT * FROM %s WHERE mobile = %s AND status = 1', $this->_name, $mobile);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $mobile
	 * @param unknown_type $start
	 * @param unknown_type $end
	 * @return string
	 */
	public function getCountByMobile($mobile, $start, $end) {
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE mobile = %s AND create_time < %d AND status > 0', $this->_name, $start, $end);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public function getFatedLogs($start = 0, $limit = 20) {
		$sql = sprintf('SELECT * FROM %s WHERE status > 0 ORDER BY create_time DESC LIMIT %d,%d ', $this->getTableName(), intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
