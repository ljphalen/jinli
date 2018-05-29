<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_FileSize
 * @author tiansh
 *
 */
class Lock_Dao_FileSize extends Common_Dao_Base{
	protected $_name = 'slock_file_size';
	protected $_primary = 'id';
	/**
	 *
	 * @param array $ids
	 * @return multisize:
	 */
	public function getByFileId($file_id) {
		$sql = sprintf('SELECT * FROM %s WHERE file_id = %s ', $this->_name, Db_Adapter_Pdo::quote($file_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return multisize:
	 */
	public function getBySizeId($size_id) {
		$sql = sprintf('SELECT * FROM %s WHERE size_id = %s ', $this->_name, Db_Adapter_Pdo::quote($size_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multisize:
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
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multisize:
	 */
	public function getFileIdByFilesizeIds($file_id, $size_ids) {
		$sql = sprintf('SELECT file_id FROM %s WHERE file_id != %s  AND size_id in %s group by file_id order by id DESC', $this->_name, Db_Adapter_Pdo::quote($size_ids), Db_Adapter_Pdo::quoteArray($size_ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

}