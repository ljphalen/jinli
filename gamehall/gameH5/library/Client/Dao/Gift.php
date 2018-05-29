<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Gift
 * @author lichanghua
 *
 */
class Client_Dao_Gift extends Common_Dao_Base{
	protected $_name = 'game_client_gift';
	protected $_primary = 'id';
	
	public function getGiftByGameIds($params) {
		$where =  Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT *  FROM  %s WHERE %s  ORDER BY sort DESC,id DESC',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	

	public function getGameList($start = 1, $limit = 10, $params = array(), $orderBy= array('game_sort'=>'DESC', 'game_id'=>'DESC', 'sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT  * ,  COUNT(*) AS giftNum FROM %s WHERE %s   GROUP BY game_id  %s  LIMIT %d, %d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return  $this->fetcthAll($sql);
	}
	
	public function getGameListCount($params = array()){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT COUNT(DISTINCT game_id) FROM %s WHERE %s ', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}