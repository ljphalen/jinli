<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_Size
 * @author tiansh
 *
 */
class Lock_Dao_Size extends Common_Dao_Base{
	protected $_name = 'slock_size';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getAllSize() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 获取单条数据
	 * @param int $value
	 */
	public function getSizeByName($name) {
		$sql = sprintf('SELECT * FROM %s WHERE size = %s', $this->getTableName(), Db_Adapter_Pdo::quote($name));
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, $params = array()) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE 1 AND %s ORDER BY sort DESC, id DESC LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
	

	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}