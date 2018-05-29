<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_IdxFileLabel
 * @author tiansh
 *
 */
class Lock_Dao_IdxFileLabel extends Common_Dao_Base{
	protected $_name = 'idx_file_label';
	protected $_primary = 'id';
	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByFileId($file_id) {
		$sql = sprintf('SELECT * FROM %s WHERE file_id = %s ', $this->_name, Db_Adapter_Pdo::quote($file_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByLabelId($label_id) {
		$sql = sprintf('SELECT * FROM %s WHERE label_id = %s ', $this->_name, Db_Adapter_Pdo::quote($label_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public function getByFileIds($file_ids) {
		$sql = sprintf('SELECT * FROM %s WHERE file_id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($file_ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	/**
	 *
	 * 根据文件id删除所有数据
	 */
	public function deleteByFileId($file_id) {
		$sql = sprintf('DELETE FROM %s WHERE file_id =  %s ', $this->getTableName(), Db_Adapter_Pdo::quote($file_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public function getFileIdByFiletypeIds($file_id, $type_ids) {
		$sql = sprintf('SELECT file_id FROM %s WHERE file_id != %s  AND type_id in %s group by file_id order by id DESC', $this->_name, Db_Adapter_Pdo::quote($type_ids), Db_Adapter_Pdo::quoteArray($type_ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
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