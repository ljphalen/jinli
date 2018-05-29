<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gc_Dao_OrderFreeLogNumber
 * @author tiansh
 *
 */
class Gc_Dao_OrderFreeNumber extends Common_Dao_Base{
	protected $_name = 'gc_order_free_number';
	protected $_primary = 'id';
	
	/**
	 *
	 * getLast
	 */
	public function getLast() {
		$sql = sprintf('SELECT max(number) as number FROM %s', $this->_name);
		return Db_Adapter_Pdo::fetch($sql);
	}
}
