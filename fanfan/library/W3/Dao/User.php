<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * W3_Dao_User
 * @author huwei
 *
 */
class W3_Dao_User extends Common_Dao_Base {
	protected $_name = 'w3_user';
	protected $_primary = 'id';

	/**
	 * 获取数量
	 * @param array $ids
	 */
	public function getGroupByField($val) {
		$sql = sprintf('SELECT COUNT(`id`) AS num, %s as val FROM %s GROUP BY %s;', $val, $this->getTableName(), $val);
		return Db_Adapter_Pdo::fetchAll($sql);
	}


}