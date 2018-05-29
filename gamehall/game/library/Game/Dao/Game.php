<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Game
 * @author rainkid
 *
 */
class Game_Dao_Game extends Common_Dao_Base{
	protected $_name = 'games';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getSearchGames($start, $limit, $params) {
		$where = self::_cookSql($params);
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND %s ORDER BY sort DESC,create_time DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getSearchGamesCount($params) {
		$where = self::_cookSql($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public static function _cookSql($params) {
		$where = '';
	    if($params && !empty($params)){
	    	$where.= " binary name like '%".$params['name']."%'";
	    } else {
	    	$where.= " name  = ''";
	    }
		return $where;
	}
	
	/**
	 *
	 * @param int $value
	 */
	public function getGameInfo($value) {
		$sql = sprintf('SELECT * FROM %s WHERE %s = %s AND status =1', $this->getTableName(), $this->_primary, $value);
		return Db_Adapter_Pdo::fetch($sql);
	}
}