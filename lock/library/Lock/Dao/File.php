<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_File
 * @author tiansh
 *
 */
class Lock_Dao_File extends Common_Dao_Base{
	protected $_name = 'slock_file';
	protected $_primary = 'id';
	
	/**
	 *
	 * @return multitype:
	 */
	public function getAllFile($ids) {
		$where = '1 AND status = 4 ' ;
		if($ids && is_array($ids)) $where .= ' AND id not in '.Db_Adapter_Pdo::quoteArray($ids);
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY id DESC', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getOnlineFiles() {
		$sql = sprintf('SELECT id FROM %s WHERE status = 4 ORDER BY id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getIndexFile($ids) {
		$where = 1;
		if($ids && is_array($ids)) $where .= ' AND id in '.Db_Adapter_Pdo::quoteArray($ids);
		$sql = sprintf('SELECT * FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, $params = array(), $order_by = '', $sort = 'DESC') {
		if(!$order_by) $order_by = $this->_primary;
		if(!$sort)  $sort = 'DESC';
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY %s %s LIMIT %d,%d', $this->getTableName(), $where, $order_by, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
	
	/**
	 *
	 * @param array $params
	 * @return string
	 */
	public function getCount($params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->_name, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getByFileIds($ids) {
		$sql = sprintf('SELECT id, title, icon, img_png, update_time FROM %s WHERE id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	/**
	 * getPre
	 */
	public function getPre($id) {
		$sql = sprintf('SELECT id FROM %s WHERE id < %s AND status = 4 ORDER BY id DESC LIMIT 0, 1', $this->getTableName(), Db_Adapter_Pdo::quote($id));
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 * getPre
	 */
	public function getNext($id) {
		$sql = sprintf('SELECT id FROM %s WHERE id > %s AND status = 4 ORDER BY id ASC LIMIT 0, 1', $this->getTableName(), Db_Adapter_Pdo::quote($id));
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	
	/**
	 *
	 * @param unknown_type $params
	 * @return Ambigous <number, string>
	 */
	private function _cookSearch($params) {
		$where = 1;
		if($params['title']) $where .= " AND title like '%" . Db_Adapter_Pdo::_filterLike($params['title']) . "%'";
		if($params['file_type']) $where .= ' AND id in (SELECT file_id FROM slock_file_types WHERE type_id = '.Db_Adapter_Pdo::quote($params['file_type']).')';
		if($params['status']) $where .= ' AND status = ' . Db_Adapter_Pdo::quote($params['status']);
		if($params['size_id']) $where .= ' AND id in (SELECT file_id FROM slock_file_size WHERE size_id = '.Db_Adapter_Pdo::quote($params['size_id']).')';
		if($params['user_id']) $where .= ' AND user_id = ' . Db_Adapter_Pdo::quote($params['user_id']);
		if($params['file_ids']) $where .= ' AND id in '.Db_Adapter_Pdo::quoteArray($params['file_ids']);
		if($params['keyword']) {
			$where .= " AND title like '%" . Db_Adapter_Pdo::_filterLike($params['keyword']) . "%'";
			unset($params['keyword']);
		}
		return $where;
	}
}