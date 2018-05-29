<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_AdminLog
 * @author tiansh
 *
 */
class Admin_Dao_AdminLog extends Common_Dao_Base {
	protected $_name = 'admin_log';
	protected $_primary = 'id';	

	
	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, $params = array(), $order_by = '', $sort = 'DESC') {
		if(!$order_by) $order_by = $this->_primary;
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT * FROM %s WHERE 1 AND %s ORDER BY %s %s LIMIT %d,%d', $this->getTableName(), $where, $order_by, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return Ambigous <number, string>
	 */
	private function _cookSearch($params) {
		$where = 1;
		if($params['message']) $where .= " AND message like '%" . Db_Adapter_Pdo::_filterLike($params['message']) . "%'";
		if($params['username']) $where .= ' AND username = ' . Db_Adapter_Pdo::quote($params['username']);
		if($params['file_id']) $where .= ' AND file_id = ' . Db_Adapter_Pdo::quote($params['file_id']);
		return $where;
	}
}