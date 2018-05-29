<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 周榜
 * Client_Dao_WeekNewRank
 * @author lichanghua
 *
 */
class Client_Dao_WeekNewRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_week';
	protected $_primary = 'day_id';
	
	public function getLastDayId() {
		$sql = sprintf('SELECT  DAY_ID FROM %s ORDER BY DAY_ID DESC LIMIT 1',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	public function delByBeforDay($date) {
		$sql = sprintf('DELETE FROM %s WHERE DAY_ID <= %s',$this->getTableName(), $date);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
