<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅 -- 每个游戏下载量
 * Client_Dao_BIWeekRank
 * @author lichanghua
 *
 */
class Client_Dao_BIWeekRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_total_daily';
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
