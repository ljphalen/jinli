<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Games_Dao_Ad
 * @author rainkid
 *
 */
class Games_Dao_Package extends Common_Dao_Base{
	protected $_name = 'games_package';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getPackageByTime($start, $limit, $time) {
		$sql = sprintf('SELECT * FROM %s WHERE update_time > %d LIMIT %d,%d',$this->getTableName(), $time, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getCountByTime($time) {
		$sql = sprintf('SELECT count(*) FROM %s WHERE update_time > %d',$this->getTableName(), $time);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}