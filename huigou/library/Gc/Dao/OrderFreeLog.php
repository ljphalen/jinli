<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_OrderFreeLog
 * @author tiansh
 *
 */
class Gc_Dao_OrderFreeLog extends Common_Dao_Base{
	protected $_name = 'gc_order_free_log';
	protected $_primary = 'id';
	
	/**
	 *
	 * getLast
	 */
	public function getLast() {
		$sql = sprintf('SELECT * FROM %s ORDER BY id DESC LIMIT 0, 1', $this->_name);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	
	public function getListByGoodsId($goods_id) {
		$sql = sprintf('SELECT * FROM %s WHERE goods_id = %s', $this->_name, Db_Adapter_Pdo::quote($goods_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getOrderFreeLogByNumber($number) {
		$sql = sprintf('SELECT * FROM %s WHERE number = %s ORDER BY id DESC LIMIT 1', $this->_name, Db_Adapter_Pdo::quote($number));
		return Db_Adapter_Pdo::fetch($sql);
	}
}
