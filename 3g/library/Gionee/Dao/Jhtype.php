<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_Jhtype extends Common_Dao_Base {
	protected $_name = '3g_jhnews_type';
	protected $_primary = 'id';

	/**
	 * 指修改新闻显示状态
	 * @param array $ids
	 */
	public function updateStatusByIds($ids, $status) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id in %s', $this->getTableName(), $status, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::execute($sql);
	}

	/**
	 *
	 * 按条件取内容
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getWidget($nowtime) {
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND sub_time <= %s ORDER BY sub_time DESC LIMIT 0,1', $this->getTableName(), $nowtime);
		return Db_Adapter_Pdo::fetch($sql, $params);
	}

	/**
	 *
	 * 按条件取内容
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getPrevId($nowtime, $sub_time) {
		$sql = sprintf('SELECT id FROM %s WHERE status = 1 AND sub_time <= %s AND sub_time < %s ORDER BY sub_time DESC LIMIT 0,1', $this->getTableName(), $nowtime, $sub_time);
		return Db_Adapter_Pdo::fetch($sql, $params);
	}

	/**
	 *
	 * 按条件取内容
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getPrevWidget($prev_id) {
		$sql = sprintf('SELECT * FROM %s WHERE id = %s', $this->getTableName(), $prev_id);
		return Db_Adapter_Pdo::fetch($sql, $params);
	}
}
