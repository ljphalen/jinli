<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 月榜(搜索使用)
 * Client_Dao_BINewMonthRank
 * @author lichanghua
 *
 */
class Client_Dao_BINewMonthRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_total_month';
	protected $_primary = '';
	public $adapter = 'BI';
	
	public function delByBeforDay($date) {
		$sql = sprintf('DELETE FROM %s WHERE day_id <= %s',$this->getTableName(), $date);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
