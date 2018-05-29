<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_Models
 * @author tiansh
 *
 */
class Browser_Dao_Models extends Common_Dao_Base{
	protected $_name = 'browser_models';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getAllModels() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 获取单条数据
	 * @param int $value
	 */
	public function getModelsByName($name) {
		$sql = sprintf('SELECT * FROM %s WHERE name = %s', $this->getTableName(), Db_Adapter_Pdo::quote($name));
		return Db_Adapter_Pdo::fetch($sql);
	}
}