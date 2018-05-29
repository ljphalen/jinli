<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_IdxGameClientNews
 * @author lichanghua
 *
 */
class Client_Dao_IdxGameClientNews extends Common_Dao_Base{
	protected $_name = 'idx_game_client_news';
	protected $_primary = 'id';
	
	public function updateGameClientNewsByOutId($ntype,$id,$out_id) {
		$sql = sprintf('UPDATE  %s SET  ntype = %d,n_id = %d WHERE out_id = %d ',$this->getTableName(), $ntype, $id,$out_id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
