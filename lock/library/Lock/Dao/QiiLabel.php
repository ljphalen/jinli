<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_QiiLabel
 * @author tiansh
 *
 */
class Lock_Dao_QiiLabel extends Common_Dao_Base{
	protected $_name = 'qii_label';
	protected $_primary = 'id';	
	
	/**
	 *
	 * 删除所有数据并返回影响行数
	 * @param int $value
	 */
	public function deleteAll() {
		$sql = sprintf('DELETE FROM %s', $this->getTableName());
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE out_id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}