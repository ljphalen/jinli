<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_ProductAttribute extends Common_Dao_Base {
	protected $_name = '3g_product_attribute';
	protected $_primary = 'id';

	/**
	 *
	 * @return multitype:
	 */
	public function getAttributeByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id in %s  ORDER BY id DESC', $this->getTableName(), Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * 查询所有数据
	 */
	public function getAll() {
		$sql = sprintf('SELECT * FROM %s ORDER BY id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}

}