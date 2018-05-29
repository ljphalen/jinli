<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_IdxGameResourceVersion
 * @author lichanghua
 *
 */
class Resource_Dao_IdxGameResourceVersion extends Common_Dao_Base{
	protected $_name = 'idx_game_resource_version';
	protected $_primary = 'id';
	
	public function getAllVersions() {
		$sql = sprintf('SELECT *  FROM  %s ORDER BY game_id DESC, update_time DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	public function getIdxVersionByNewVersion() {
		$sql = sprintf('SELECT * FROM  (SELECT * FROM  %s ORDER BY update_time DESC) AS T GROUP BY T.game_id ORDER BY T.update_time DESC',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	public function getIdxVersionByVersionInfo($game_id) {
		$sql = sprintf('SELECT * ,MAX(version_code) AS max_code FROM  %s WHERE game_id = %d AND status = 1 GROUP BY game_id ORDER BY game_id DESC limit 1',$this->getTableName(), $game_id);
		return $this->fetcthAll($sql);
	}
	
	public function getCountResourceVersion() {
		$sql = sprintf('SELECT game_id,count(game_id) AS num FROM %s GROUP BY game_id',$this->getTableName());
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getVersionGames($start, $limit, $params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM %s WHERE  %s AND status =1 ORDER BY game_id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getVersionGamesCount($params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE  %s AND status =1',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getVersionSortGames($start, $limit, $params) {
	    $ids = $params['id']; 
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM %s WHERE  %s AND status =1 ORDER BY FIELD %s LIMIT %d,%d',$this->getTableName(), $where, $this->quoteInArray($ids), $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	public function updateCronVersion($new_id,$id) {
		$set = '';
		$set.=sprintf('min_resolution = %d ', $new_id, $new_id);
		$where = '';
		$where.= sprintf('min_resolution = %d OR max_resolution = %d', $id, $id);
		$sql = sprintf('UPDATE %s SET  %s WHERE %s', $this->getTableName(), $set, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	
	public function _cookParams($params) {
		$sql = ' ';
		if (is_array($params['id']) && count($params['id'])) {
			$sql .= " AND game_id IN " . Db_Adapter_Pdo::quoteArray($params['id']);
		}
		
		unset($params['ids']);
		unset($params['id']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
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
		return '(' .'game_id'.','. implode(', ', $_returns) . ')';
	}
	
}
