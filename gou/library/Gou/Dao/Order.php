<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Gou_Dao_Order extends Common_Dao_Base{
	protected $_name = 'gou_order';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getOrderList($start, $limit, $params) {
		$time = Common::getTime();
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d',$this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND create_time > %d', $params['start_time']);
			unset($params['start_time']);
		} 
		if ($params['end_time']) {
			$sql.= sprintf(' AND create_time < %d', $params['end_time']);
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $out_trade_no
	 * @return boolean|Ambigous <boolean, number>
	 */
	public function updateByOutTradeNo($data, $out_trade_no) {
		if (!is_array($data)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE out_trade_no = %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), Db_Adapter_Pdo::quote($out_trade_no));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function userOrderCount($params) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(number) FROM %s WHERE %s AND status < 6', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}
