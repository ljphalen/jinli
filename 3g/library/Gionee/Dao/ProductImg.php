<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_ProductImg
 * @author tiansh
 *
 */
class Gionee_Dao_ProductImg extends Common_Dao_Base {
	protected $_name = '3g_product_img';
	protected $_primary = 'id';

	public function getImagesByPids($pids) {
		$sql = sprintf('SELECT * FROM %s WHERE pid in %s ORDER BY id DESC', $this->getTableName(), Db_Adapter_Pdo::quoteArray($pids));
		return $this->fetcthAll($sql);
	}

	/**
	 * delete by product_id
	 */
	public function deleteByProductId($product_id) {
		$sql = sprintf('DELETE FROM %s WHERE pid = %d', $this->getTableName(), intval($product_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}

	/**
	 * delete by product_id
	 */
	public function getImagesByPid($pid) {
		$sql = sprintf('SELECT * FROM %s WHERE pid = %d ORDER BY id ASC', $this->getTableName(), intval($pid));
		return $this->fetcthAll($sql);
	}
}