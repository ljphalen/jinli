<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_IdxGameClientInstalle
 * @author lichanghua
 *
 */
class Client_Dao_IdxGameClientInstalle extends Common_Dao_Base{
	protected $_name = 'idx_game_client_installe';
	protected $_primary = 'id';
	
	public function getAllByGameStatus(){
	   $sql = sprintf('SELECT * FROM %s WHERE game_status = 1', $this->getTableName(), $this->_primary);
	   return Db_Adapter_Pdo::fetchAll($sql);
	}
	
    public function updateIdxInstalleStatus($game_id,$status) {
		$sql = sprintf('UPDATE  %s SET  game_status = %s WHERE game_id = %d ',$this->getTableName(), $status, $game_id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function updateInstalleStatus($id,$statu) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE installe_id = %d', $this->getTableName(), $statu, $id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
