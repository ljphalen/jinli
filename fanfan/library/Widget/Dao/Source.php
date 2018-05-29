<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Widget_Dao_Source
 * @author rainkid
 *
 */
class Widget_Dao_Source extends Common_Dao_Base {
	protected $_name = 'widget_source';
	protected $_primary = 'id';

	public function getIds($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort  = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql   = sprintf('SELECT id FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}