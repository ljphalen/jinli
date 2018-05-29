<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_ResourceAssign extends Common_Dao_Base {
	protected $_name = '3g_resource_assign';
	protected $_primary = 'id';

	/**
	 *
	 */
	public function getByModelId($model_id) {
		$sql = sprintf('SELECT * FROM %s WHERE model_id = %s ', $this->getTableName(), $model_id);
		return $this->fetcthAll($sql);
	}

	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getListGroupByModelId($start = 0, $limit = 20, $params = array(), $order_by = '', $sort = 'DESC') {
		if (!$order_by) $order_by = $this->_primary;
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE 1 AND %s  GROUP BY model_id ORDER BY %s %s LIMIT %d,%d', $this->getTableName(), $where, $order_by, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}

	/**
	 *
	 * 根据机型删除所有数据
	 */
	public function deleteByModel($model_id) {
		$sql = sprintf('DELETE FROM %s WHERE model_id =  %s', $this->getTableName(), Db_Adapter_Pdo::quote($model_id));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}

	/**
	 *
	 * 根据参数统计总数
	 * @param array $params
	 */
	public function countGroupByModelId($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT model_id FROM %s WHERE %s GROUP BY model_id', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
