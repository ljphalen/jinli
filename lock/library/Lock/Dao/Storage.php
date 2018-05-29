<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Storage
 * @author rainkid
 *
 */
class Lock_Dao_Storage extends Common_Dao_Base{
	protected $_name = 'slock_storage';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return Ambigous <boolean, number>
	 */
	public function updateByKey($key, $value) {
		$sql = sprintf('REPLACE INTO %s VALUES (%s,%s)', $this->getTableName(), Db_Adapter_Pdo::quote($key), Db_Adapter_Pdo::quote($value));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
