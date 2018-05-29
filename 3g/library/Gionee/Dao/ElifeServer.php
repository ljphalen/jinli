<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_RecsiteType
 * @author tiansh
 *
 */
class Gionee_Dao_ElifeServer extends Common_Dao_Base {
	protected $_name = '3g_elife_server';
	protected $_primary = 'id';

	/**
	 *
	 * @return multitype:
	 */
	public function getAllAppType() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}