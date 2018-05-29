<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Lock_Dao_QiiFile
 * @author tiansh
 *
 */
class Lock_Dao_QiiFile extends Common_Dao_Base{
	protected $_name = 'qii_file';
	protected $_primary = 'id';
	
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
		$sql = sprintf('SELECT id, zh_title, icon, icon_micro, out_id, update_time FROM %s WHERE out_id in %s ORDER BY FIELD %s', $this->_name, Db_Adapter_Pdo::quoteArray($ids), $this->quoteInArray($ids));
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
		if($params['keyword']) {
			$where .= " AND zh_title like '%" . Db_Adapter_Pdo::_filterLike($params['keyword']) . "%'";
			unset($params['keyword']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$where;
		//return $where;
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