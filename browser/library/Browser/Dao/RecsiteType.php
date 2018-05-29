<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_RecsiteType
 * @author tiansh
 *
 */
class Browser_Dao_RecsiteType extends Common_Dao_Base{
	protected $_name = 'browser_recsite_type';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getAllRecsiteType() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}