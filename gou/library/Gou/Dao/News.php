<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Dao_News
 * @author fanch
 *
 */
class Gou_Dao_News extends Common_Dao_Base {
	
	protected $_name = 'gou_news';
	protected $_primary = 'id';

	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseNews($start, $limit, $params, $orderBy) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND %s %s LIMIT %d,%d',$this->getTableName(), $time, $where, $sort, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseNewsCount($params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND %s',$this->getTableName(), $time, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}