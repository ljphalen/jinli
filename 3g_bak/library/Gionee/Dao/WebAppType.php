<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_WebAppType
 * @author tiansh
 *
 */
class Gionee_Dao_WebAppType extends Common_Dao_Base {
	protected $_name = '3g_web_apptype';
	protected $_primary = 'id';

	/**
	 *
	 * @return multitype:
	 */
	public function getWebAppType() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}