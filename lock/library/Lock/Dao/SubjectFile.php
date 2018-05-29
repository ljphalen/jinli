<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_FileTypes
 * @author tiansh
 *
 */
class Lock_Dao_SubjectFile extends Common_Dao_Base{
	protected $_name = 'slock_subject_file';
	protected $_primary = 'id';	
	
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
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getCanuseSubjectFiles($subject_id, $file_ids) {
		$where = 'subject_id = 1';
		if($file_ids) $where .= ' OR (channel_id = 2 AND file_id in '.Db_Adapter_Pdo::quoteArray($file_ids).')';
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY id ASC', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}