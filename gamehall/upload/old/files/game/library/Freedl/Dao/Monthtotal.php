<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Monthtotal
 * @author lichanghua
 *
 */
class Freedl_Dao_Monthtotal extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_monthtotal';
	protected $_primary = 'id';
	
	/**
	 *
	 * 根据参数统计流量消耗总数
	 * @param array $params
	 */
	public function getCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(month_consume) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
