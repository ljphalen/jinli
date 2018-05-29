<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Splash
 * @author rainkid
 *
 */
class Gionee_Dao_Splash extends Common_Dao_Base {
	protected $_name = '3g_splash';
	protected $_primary = 'id';

	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSplashs($start, $limit, $params) {
		$time  = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s LIMIT %d,%d', $this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}

	/**
	 *
	 * @param unknown_type $params
	 */
	public function getCanUseSplashCount($params) {
		$time  = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s', $this->getTableName(), $time, $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}