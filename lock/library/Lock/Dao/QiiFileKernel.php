<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_QiiFileKernel
 * @author tiansh
 *
 */
class Lock_Dao_QiiFileKernel extends Common_Dao_Base{
	protected $_name = 'qii_kernel';
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
}