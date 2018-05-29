<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Want
 * @author rainkid
 *
 */
class Gou_Dao_WantLog extends Common_Dao_Base{
	protected $_name = 'gou_want_log';
	protected $_primary = 'id';
	
	/**
	 * get want logs by start_time or end_time
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getWantLogsByTime($start, $limit, array $params = array()) {
		list($str, $params) = $this->_cookData($params);
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s %s ORDER BY id DESC LIMIT %d,%d',$this->getTableName(), $where, $str, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * get want logs by goods_id
	 * @param int $goods_id
	 * @param array $params
	 */
	public function getWantLogsByGoodsId($goods_id, $type) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id = %s AND goods_type = %d',$this->getTableName(), $goods_id, Db_Adapter_Pdo::quote($type));
		return $this->fetcthAll($sql);
	}
	
	/**
	 * get number of logs by start_time or end_time
	 * @param array $params
	 */
	public function countByTime($params) {
		list($str, $params) = $this->_cookData($params);
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s %s', $this->getTableName(), $where, $str);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	/**
	 * cook parmas
	 * @return string
	 */
	private function _cookData($params) {
		$str = '';
		if ($params['start']) {
			$str.= sprintf(' AND create_time > %d', $params['start']);
			unset($params['start']);
		}
		if ($params['end']) {
			$str.= sprintf(' AND create_time < %d', $params['end']);
			unset($params['end']);
		}
		return array($str, $params);
	}

	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalWants($start, $limit, $params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY create_time DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getNomalWantsCount($params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}
