<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_IdxGameClientCategory
 * @author lichanghua
 *
 */
class Client_Dao_IdxGameClientCategory extends Common_Dao_Base{
	protected $_name = 'idx_game_client_category';
	protected $_primary = 'id';
	
	
	public function getCanUseCategorysByIds($id) {
		$sql = sprintf('SELECT * FROM %s WHERE category_id = %s AND status = 1 ORDER BY sort DESC',$this->getTableName(),$id);
		return $this->fetcthAll($sql);
	}
	
	public function getCanUseCategorysByGameIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE resource_game_id IN %s AND status = 1 ORDER BY sort DESC',$this->getTableName(),Db_Adapter_Pdo::quoteArray($ids['resource_game_id']));
		return $this->fetcthAll($sql);
	}
}
