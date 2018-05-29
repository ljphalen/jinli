<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Channel
 * @author rainkid
 *
 */
class Client_Dao_Channel extends Common_Dao_Base{
	protected $_name = 'game_client_channel';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:
	 */
	public function getCanUseChannels($params, $start, $limit) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function getCanUseChannelCount($params) {
		$where = $this->_cookParams($params);
		$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public function updateChannelStatus($game_id,$status,$ctype) {
		$sql = sprintf('UPDATE  %s SET  game_status = %s WHERE game_id = %d AND ctype = %d',$this->getTableName(), $status, $game_id,$ctype);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if (is_array($params['ids']) && count($params['ids'])) {
			$sql .= " AND id IN " . Db_Adapter_Pdo::quoteArray($params['ids']);
		}
		unset($params['ids']);
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}