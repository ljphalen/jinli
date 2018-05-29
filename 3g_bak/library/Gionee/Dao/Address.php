<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiansh
 *
 */
class Gionee_Dao_Address extends Common_Dao_Base {
	protected $_name = '3g_address';
	protected $_primary = 'id';

	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getAllAddress($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql   = sprintf('SELECT * FROM %s WHERE 1 AND %s ORDER BY sort DESC, id DESC', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql, $params);
	}
}
