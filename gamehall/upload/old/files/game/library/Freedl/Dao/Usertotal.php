<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Dao_Usertotal
 * @author lichanghua
 *
 */
class Freedl_Dao_Usertotal extends Common_Dao_Base{
	protected $_name = 'game_client_freedl_usertotal';
	protected $_primary = 'id';
	
	/**
	 *
	 * 根据参数统计流量消耗总数
	 * @param array $params
	 */
	public function getCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(total_consume) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
