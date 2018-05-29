<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_Want
 * @author rainkid
 *
 */
class Gc_Dao_WantLog extends Common_Dao_Base{
	protected $_name = 'gc_want_log';
	protected $_primary = 'id';
	
	/**
	 * get want logs by start_time or end_time
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getWantLogsByTime($start, $limit, array $params = array()) {
		list($str, $params) = $this->_cookData($params);
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE %s %s ORDER BY id DESC LIMIT %d,%d',$this->getTableName(), $where, $str, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * get want logs by goods_id
	 * @param int $goods_id
	 * @param array $params
	 */
	public function getWantLogsByGoodsId($goods_id) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id = %s ',$this->getTableName(), $goods_id);
		return $this->fetcthAll($sql);
	}
	
	/**
	 * get number of logs by start_time or end_time
	 * @param array $params
	 */
	public function countByTime($params) {
		list($str, $params) = $this->_cookData($params);
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
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
}
