<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_OutNews
 * @author rainkid
 *
 */
class Gionee_Dao_OutNews extends Common_Dao_Base {
	protected $_name    = '3g_out_news';
	protected $_primary = 'id';

	public function getCountBy($timestamp) {
		if (!empty($timestamp)) {
			$where = "WHERE timestamp > {$timestamp}";
		}
		$sql = sprintf('SELECT source_id, COUNT(*) as total FROM %s %s GROUP BY source_id', $this->getTableName(), $where);
		return $this->fetcthAll($sql);
	}

	public function _cookParams($params) {
		$sql = ' ';
		if ($params['title']) {
			$sql .= " AND title like '%" . Db_Adapter_Pdo::filterLike($params['title']) . "%'";
		}
		unset($params['title']);

		return Db_Adapter_Pdo::sqlWhere($params) . $sql;
	}
}