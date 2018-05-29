<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 网游榜
 * Client_Dao_OlgRank
 * @author lichanghua
 *
 */
class Client_Dao_OlgRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_olg';
	protected $_primary = 'day_id';
        
	public function delByBeforDay($date) {
		$sql = sprintf('DELETE FROM %s WHERE day_id <= %s',$this->getTableName(), $date);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}