<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_OrderFreeLogNumber
 * @author tiansh
 *
 */
class Gou_Dao_OrderFreeNumber extends Common_Dao_Base{
	protected $_name = 'gou_order_free_number';
	protected $_primary = 'id';
	
	/**
	 *
	 * getLast
	 */
	public function getLast() {
		$sql = sprintf('SELECT max(number) as number FROM %s', $this->_name);
		return Db_Adapter_Pdo::fetch($sql);
	}
	

	public function getCanUseOrderFreeNumber($start, $limit, $params) {
		$sql = sprintf('SELECT * FROM %s  ORDER BY id DESC LIMIT %d,%d',$this->getTableName(),  $start, $limit);
		return $this->fetcthAll($sql);
	}
	

	public function getCanUseOrderFreeNumberCount($params) {
		$sql = sprintf('SELECT count(*) FROM %s ORDER BY id DESC',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
