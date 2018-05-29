<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Models
 * @author tiansh
 *
 */
class Gionee_Dao_Models extends Common_Dao_Base {
	protected $_name = '3g_models';
	protected $_primary = 'id';

	/**
	 *
	 * @return multitype:
	 */
	public function getListBySeriesId($series_id) {
		$sql = sprintf('SELECT * FROM %s WHERE series_id = %s ORDER BY sort DESC, id DESC', $this->getTableName(), $series_id);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * 获取单条数据
	 * @param int $value
	 */
	public function getModelsByName($name) {
		$sql = sprintf('SELECT * FROM %s WHERE name = %s', $this->getTableName(), Db_Adapter_Pdo::quote($name));
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
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE 1 AND %s ORDER BY sort DESC, id DESC LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
}