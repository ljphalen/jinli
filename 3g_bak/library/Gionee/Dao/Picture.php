<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_Picture extends Common_Dao_Base {
	protected $_name = '3g_picture';
	protected $_primary = 'id';

	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, $params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE 1 AND %s ORDER BY istop DESC, status DESC, sort DESC, pub_time DESC LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}

	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getCanUseList($start = 0, $limit = 20, $params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$time  = Common::getTime();
		$sql   = sprintf('SELECT * FROM %s WHERE 1 AND start_time <= ' . $time . ' AND  %s ORDER BY istop DESC, status DESC, sort DESC, pub_time DESC LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}

	/**
	 *
	 * 根据参数统计总数
	 * @param array $params
	 */
	public function countCanUse($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$where .= ' AND start_time <= ' . Common::getTime();
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}

	/**
	 *
	 * 根据分类删除所有数据
	 */
	public function deleteByType($type) {
		$sql = sprintf('DELETE FROM %s WHERE type_id =  %s AND istop = 0', $this->getTableName(), Db_Adapter_Pdo::quote($type));
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}

	/**
	 * 根据分类取图片
	 */
	public function getListByType($type) {
		$sql = sprintf('SELECT * FROM %s WHERE 1 AND type_id = %s ORDER BY pub_time DESC', $this->getTableName(), $type);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 * 指修改显示状态
	 * @param array $ids
	 */
	public function updateStatusByIds($ids, $status) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id in %s', $this->getTableName(), $status, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::execute($sql);
	}
}
