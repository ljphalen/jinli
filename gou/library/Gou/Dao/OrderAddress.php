<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_OrderAddress
 * @author rainkid
 *
 */
class Gou_Dao_OrderAddress extends Common_Dao_Base{
	protected $_name = 'gou_order_address';
	protected $_primary = 'id';

	public function getAddressByOrderIds($order_ids) {
		$sql = sprintf('SELECT * FROM %s WHERE order_id IN %s',
				$this->getTableName(), Db_Adapter_Pdo::quoteArray($order_ids));
		return $this->fetcthAll($sql);
	}
}
