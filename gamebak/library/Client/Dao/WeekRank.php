<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅 -- 每个游戏下载量
 * Client_Dao_WeekRank
 * @author lichanghua
 *
 */
class Client_Dao_WeekRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_total_daily';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getMostGames($limit,$date) {
		if(intval($date)){
			$end_time = intval($date);
		} else {
			$end_time = Common::getTime();
		}
		$start_time = date('Ymd', ($end_time - 7 * 24 *3600));
		$end_time = date('Ymd', $end_time);
		$sql = sprintf('SELECT  DAY_ID,GAME_ID,SUM(DL_TIMES) AS dwnload,CRT_TIME FROM (SELECT * FROM %s WHERE DAY_ID >= %s  AND DAY_ID < %s  ORDER BY CRT_TIME DESC) AS T GROUP BY GAME_ID ORDER BY dwnload DESC LIMIT %d',$this->getTableName(), Db_Adapter_Pdo::quote($start_time), Db_Adapter_Pdo::quote($end_time), $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getMostGamescount($limit,$date) {
		if(intval($date)){
			$end_time = intval($date);
		} else {
			$end_time = Common::getTime();
		}
		$start_time = date('Ymd', ($end_time - 7 * 24 *3600));
		$end_time = date('Ymd', $end_time);
		$sql = sprintf('SELECT count(*) FROM (SELECT DAY_ID,GAME_ID,SUM(DL_TIMES)  FROM %s  WHERE DAY_ID >= %s AND DAY_ID < %s GROUP BY GAME_ID ORDER BY DL_TIMES DESC LIMIT %d) AS T',$this->getTableName(), Db_Adapter_Pdo::quote($start_time),Db_Adapter_Pdo::quote($end_time), $limit);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public function turncateRecommend() {
		$sql = sprintf('TRUNCATE %s',$this->getTableName());
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function getLastDayId() {
		$sql = sprintf('SELECT  DAY_ID FROM %s ORDER BY DAY_ID DESC LIMIT 1',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
    public function delByBeforDay($date) {
		$sql = sprintf('DELETE FROM %s WHERE DAY_ID <= %s',$this->getTableName(), $date);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
