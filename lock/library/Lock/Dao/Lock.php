<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_Lock
 * @author tiansh
 *
 */
class Lock_Dao_Lock extends Common_Dao_Base{
	protected $_name = 'slock_lock';
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
	public function getByChannelId($channel_id) {
		$sql = sprintf('SELECT * FROM %s WHERE channel_id = %s ', $this->_name, Db_Adapter_Pdo::quote($channel_id));
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
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id in %s ORDER BY FIELD %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids), $this->quoteInArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getCanuseFiles($start = 0, $limit = 20, $ids,  array $orderBy = array()) {
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$where = 'channel_id = 1';
		if($ids) $where .= ' OR (channel_id = 2 AND file_id in '.Db_Adapter_Pdo::quoteArray($ids).')';
		$sql = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param array $params
	 * @return string
	 */
	public function getCanuseFilesCount($ids) {
		$where = 'channel_id = 1';
		if($ids) $where .= ' OR (channel_id = 2 AND file_id in '.Db_Adapter_Pdo::quoteArray($ids).')';
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->_name, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
	
}