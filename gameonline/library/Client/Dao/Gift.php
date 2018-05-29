<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Gift
 * @author lichanghua
 *
 */
class Client_Dao_Gift extends Common_Dao_Base{
	protected $_name = 'game_client_gift';
	protected $_primary = 'id';
	
	public function getGiftByGameIds($params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM  %s WHERE %s  ORDER BY sort DESC,id DESC',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
}