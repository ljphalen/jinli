<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_Subject
 * @author rainkid
 *
 */
class Gionee_Dao_Subject extends Common_Dao_Base {
	protected $_name = '3g_subject';
	protected $_primary = 'id';

	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$time  = Common::getTime();
		$where = count($params) ? Db_Subjectapter_Pdo::sqlWhere($params) : 1;
		$sql   = sprintf('SELECT * FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s ORDER BY sort DESC LIMIT %d,%d', $this->getTableName(), $time, $time, $where, $start, $limit);
		return $this->fetcthAll($sql);
	}

	/**
	 *
	 * @param unknown_type $params
	 */
	public function getCanUseSubjectCount($params) {
		$time  = Common::getTime();
		$where = count($params) ? Db_Subjectapter_Pdo::sqlWhere($params) : 1;
		$sql   = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND start_time < %d AND end_time > %d AND %s', $this->getTableName(), $time, $time, $where);
		return Db_Subjectapter_Pdo::fetchCloum($sql, 0);
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