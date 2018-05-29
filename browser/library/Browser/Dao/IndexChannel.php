<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_IndexChannel
 * @author tiansh
 *
 */
class Browser_Dao_IndexChannel extends Common_Dao_Base{
	protected $_name = 'browser_index_channel';
	protected $_primary = 'id';


	/**
	 * 
	 */
	public function getCanUseChannels() {
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 ORDER BY sort DESC, id ASC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	public function getAllChannel() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}