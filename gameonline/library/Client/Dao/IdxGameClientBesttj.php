<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_IdxGameClientBesttj
 * @author lichanghua
 *
 */
class Client_Dao_IdxGameClientBesttj extends Common_Dao_Base{
	protected $_name = 'idx_game_client_besttj';
	protected $_primary = 'id';
	
	public function getAllByGameStatus(){
		$sql = sprintf('SELECT * FROM %s WHERE game_status = 1', $this->getTableName(), $this->_primary);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
    public function updateIdxBesttjStatus($game_id,$status) {
		$sql = sprintf('UPDATE  %s SET  game_status = %s WHERE game_id = %d ',$this->getTableName(), $status, $game_id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function updateBesttjByBesttjId($besttj_id,$status) {
		$sql = sprintf('UPDATE  %s SET  status = %s WHERE besttj_id = %d ',$this->getTableName(), $status, $besttj_id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
