<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Client
 * @author rainkid
 *
 */
class Client_Dao_Game extends Common_Dao_Base{
	protected $_name = 'game_client_games';
	protected $_primary = 'id';
	protected $_resource_game_id = 'resource_game_id';
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseGames($start, $limit, $params) {
		$tmp = array();
		if($params['id']){
			foreach($params as $key=>$value){
				if($key != 'id')  $tmp[$key] = $value;
			}
		}
		if($params['id']){
			if($params['name']){
				$name = $params['name'];
				unset($tmp['name']);
				$where = Db_Adapter_Pdo::sqlWhere($tmp);
				$sql = sprintf('SELECT * FROM %s WHERE  name  LIKE "%%%s%%" AND %s AND id IN %s  ORDER BY sort DESC,create_time DESC LIMIT %d,%d',$this->getTableName(), $name, $where,Db_Adapter_Pdo::quoteArray($params['id']), $start, $limit);
			} else {
				$where = Db_Adapter_Pdo::sqlWhere($tmp);
				$sql = sprintf('SELECT * FROM %s WHERE  %s AND id IN %s  ORDER BY sort DESC,create_time DESC LIMIT %d,%d',$this->getTableName(), $where,Db_Adapter_Pdo::quoteArray($params['id']), $start, $limit);
			}
		} else {
			unset($params['id']);
			if($params['name']){
				$name = $params['name'];
				unset($params['name']);
				$where = Db_Adapter_Pdo::sqlWhere($params);
				$sql = sprintf('SELECT * FROM %s WHERE  name  LIKE "%%%s%%" AND  %s  ORDER BY sort DESC,create_time DESC LIMIT %d,%d',$this->getTableName(), $name, $where, $start, $limit);
			} else {
				$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
			    $sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY sort DESC,create_time DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
			}
		}
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getCanUseGamesCount($params) {
		$tmp = array();
		if($params['id']){
			foreach($params as $key=>$value){
				if($key != 'id')  $tmp[$key] = $value;
			}
		}
		if($params['id']){
		if($params['name']){
				$name = $params['name'];
				unset($tmp['name']);
				$where = Db_Adapter_Pdo::sqlWhere($tmp);
				$sql = sprintf('SELECT count(*) FROM %s WHERE  name  LIKE "%%%s%%" AND %s AND id IN %s',$this->getTableName(), $name, $where,Db_Adapter_Pdo::quoteArray($params['id']));
			} else {
				$where = Db_Adapter_Pdo::sqlWhere($tmp);
				$sql = sprintf('SELECT count(*) FROM %s WHERE  %s AND id IN %s',$this->getTableName(), $where,Db_Adapter_Pdo::quoteArray($params['id']));
			}
		} else {
		    unset($params['id']);
			if($params['name']){
				$name = $params['name'];
				unset($params['name']);
				$where = Db_Adapter_Pdo::sqlWhere($params);
				$sql = sprintf('SELECT count(*) FROM %s WHERE  name  LIKE "%%%s%%" AND  %s',$this->getTableName(), $name, $where);
			} else {
				$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
			    $sql = sprintf('SELECT count(*) FROM %s WHERE  %s ',$this->getTableName(), $where);
			}
		}
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 *
	 * @return string
	 */
	public function getAllGames() {
		$sql = sprintf('SELECT * FROM %s  WHERE status =1 ORDER BY sort DESC,id DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getCanUseAllGames() {
		$sql = sprintf('SELECT * FROM %s  ORDER BY sort DESC,id DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getCanUseAllGamesCount() {
		$sql = sprintf('SELECT count(*) FROM %s',$this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getNewTopGames($start, $limit, $params) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN (SELECT T.id FROM (SELECT id FROM %s WHERE status = 1 ORDER BY create_time DESC LIMIT %s) AS T)  ORDER BY create_time DESC LIMIT %d,%d', $this->getTableName(), $this->getTableName(), $params['top'], $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getNewTopGamesCount($params,$top) {
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 ORDER BY create_time DESC LIMIT %s',$this->getTableName(), $top);
		if($top >= Db_Adapter_Pdo::fetchCloum($sql, 0)){
			return Db_Adapter_Pdo::fetchCloum($sql, 0);
		} else {
			return intval($top);
		}
		
	}
	
	/**
	 *
	 * @param int $value
	 */
	public function getGameInfo($value) {
		$sql = sprintf('SELECT * FROM %s WHERE resource_game_id = %s', $this->getTableName(), $value);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	
	public function getGameByStatu($statu) {
		$sql = sprintf('SELECT * FROM %s WHERE status = %s ORDER BY sort DESC,id DESC', $this->getTableName(),  $statu);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param int $value
	 */
	public function getGameByResourceId($id) {
		$sql = sprintf('SELECT * FROM %s WHERE %s = %s', $this->getTableName(), $this->_resource_game_id, $id);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	
	/**
	 * 
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
}