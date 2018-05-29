<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Activity
 * @author lichanghua
 *
 */
class Client_Dao_Activity extends Common_Dao_Base{
	protected $_name = 'game_client_activity';
	protected $_primary = 'id';
	
	public function getLastActivity() {
		$sql = sprintf('SELECT *  FROM  %s  ORDER BY id DESC limit 1',$this->getTableName());
		return Db_Adapter_Pdo::fetch($sql);
	}
}