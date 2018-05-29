<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Dho_Dao_Config
 * @author tiansh
 *
 */
class Dhm_Dao_Config extends Common_Dao_Base{
	protected $_name = 'dhm_config';
	
	/**
	 * 
	 * @param string $key
	 * @param mix $value
	 * @return Ambigous <boolean, number>
	 */
	public function updateByKey($key, $value) {
		$sql = sprintf('REPLACE INTO %s VALUES (%s,%s)', $this->getTableName(), Db_Adapter_Pdo::quote($key), Db_Adapter_Pdo::quote($value));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
