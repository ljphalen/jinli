<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Dao_Installe
 * @author lichanghau
 *
 */
class Client_Dao_Installe extends Common_Dao_Base{
	protected $_name = 'game_client_installe';
	protected $_primary = 'id';
	
	public function getInstalleByGtype($installe_id) {
		$sql = sprintf('SELECT * FROM (SELECT * FROM  %s  WHERE status = 1 ) AS T  WHERE T.status = 1 AND T.gtype = %d ORDER BY T.update_time DESC,id DESC LIMIT 1',$this->getTableName(), $installe_id);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseInstallesByIds($start, $limit, $params) {
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND id IN %s ORDER BY FIELD %s LIMIT %d,%d',$this->getTableName(), Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']), $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseInstalleByIdsCount($params) {
		$time = Common::getTime();
		$sql = sprintf('SELECT count(*) FROM %s WHERE status = 1 AND id IN %s ORDER BY FIELD %s',$this->getTableName(), Db_Adapter_Pdo::quoteArray($params['id']), $this->quoteInArray($params['id']));
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	

	
	public function updateInstalleStatus($id,$statu) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id = %d', $this->getTableName(), $statu, $id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	public function updateInstalleDate($id) {
		$time = Common::getTime();
		$sql = sprintf('UPDATE %s SET update_time = %d WHERE id = %d', $this->getTableName(), $time, $id);
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
		if ($params['st'] == '1') {
			$sql .= " AND id = 0 ";
		}
		unset($params['ids']);
		unset($params['st']);
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
		return '(' .'id'.','. implode(', ', $_returns) . ')';
	}
}
