<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Gou_Dao_Supplier
 * @author lichanghua
 *
 */
class Gou_Dao_Supplier extends Common_Dao_Base{
	protected $_name = 'gou_supplier';
	protected $_primary = 'id';
	
	/**
	 *
	 *
	 *所有供应商列表
	 */
	public function getAllSupplierSort() {
		$sql = sprintf('SELECT * FROM %s ORDER BY id DESC, sort DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 取出所有供应商数量
	 *
	 */
	public function getAllSupplierSortCount() {
		$sql = sprintf('SELECT COUNT(*) FROM %s ORDER BY sort DESC',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 
	 * @param unknown_type $order_ids
	 */
	public function getSuppliersByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s',$this->getTableName(), Db_Adapter_Pdo::quoteArray($ids));
		return $this->fetcthAll($sql);
	}
}