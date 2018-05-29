<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Client_Dao_RankResult
 * author lichanghua
 */
class Client_Dao_RankResult extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_times_result';
	protected $_primary = 'id';
	
	public function getLastDayId() {
		$sql = sprintf('SELECT  DAY_ID FROM %s ORDER BY DAY_ID DESC LIMIT 1',$this->getTableName());
		return $this->fetcthAll($sql);
	}
}