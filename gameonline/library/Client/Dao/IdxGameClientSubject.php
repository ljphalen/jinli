<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_IdxGameClientSubject
 * @author lichanghua
 *
 */
class Client_Dao_IdxGameClientSubject extends Common_Dao_Base{
	protected $_name = 'idx_game_client_subject';
	protected $_primary = 'id';
	
    public function getCanUseSubjectsByGameIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE resource_game_id IN %s AND status = 1 ORDER BY sort DESC',$this->getTableName(),Db_Adapter_Pdo::quoteArray($tmp));
		return $this->fetcthAll($sql);
	}
	
    public function updateSubjectsByGameIds($id,$statu) {
		$sql = sprintf('UPDATE  %s SET game_status = %d WHERE resource_game_id =  %d', $this->getTableName(), $statu,  $id);
		return Db_Adapter_Pdo::execute($sql,array(), false);
	}
	
	public function updateSubjectsBySubjectIds($subject_id,$status) {
		$sql = sprintf('UPDATE  %s SET status = %d WHERE subject_id =  %d', $this->getTableName(), $status,  $subject_id);
		return Db_Adapter_Pdo::execute($sql,array(), false);
	}
}
