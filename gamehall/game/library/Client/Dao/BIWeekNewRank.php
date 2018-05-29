<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 周榜
 * Client_Dao_BIWeekNewRank
 * @author lichanghua
 *
 */
class Client_Dao_BIWeekNewRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_week';
	protected $_primary = '';
	public $adapter = 'BI';
	
	
	public function getTopBIWeek() {
		$sql = sprintf('SELECT * FROM %s LIMIT 100000',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getTopBIWeekCount() {
		$sql = sprintf('SELECT count(*) FROM %s LIMIT 100000',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
