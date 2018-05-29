<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Browser_Dao_Click extends Common_Dao_Base{
	protected $_name = 'tj_click';
	protected $_primary = 'id';
		
	/**
	 *
	 * @param unknown_type $url
	 * @param unknown_type $start
	 * @param unknown_type $end
	 * @return string
	 */
	public function getCount($params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->_name, $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getClickList($start = 0, $limit = 20, $params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY click DESC , id DESC LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return multitype:
	 */
	public function searchClickList($params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT sum(click) as total,type_id FROM %s WHERE %s GROUP BY type_id', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 更新数据并返回影响行数
	 * @param array $data
	 * @param int $value
	 */
	public function updateByTypeAndDate($data, $type_id, $date) {
		if (!is_array($data)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE type_id = %s AND dateline = %s', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), Db_Adapter_Pdo::quote($type_id), Db_Adapter_Pdo::quote($date));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return multitype:
	 */
	public function getTotalByTime($params) {
		$where = $this->_cookSearch($params);
		$sql = sprintf('SELECT sum(click) as total FROM %s WHERE %s ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <number, string>
	 */
	private function _cookSearch($params) {
		$where = 1;
		if($params['sdate']) $where .= ' AND dateline >= ' . Db_Adapter_Pdo::quote($params['sdate']);
		if($params['edate']) $where .= ' AND dateline <= '. Db_Adapter_Pdo::quote($params['edate']);
		return $where;
	}
}