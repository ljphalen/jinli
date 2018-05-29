<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Client_Dao_MoneyTrade
 * author ljp
 */
class Client_Dao_MoneyTrade extends Common_Dao_Base{
	protected $_name = 'game_client_money_trade';
	protected $_primary = 'id';
	
	/**
	 *
	 * 根据uuid计算消费总额
	 * @param array $params
	 */
	public function getCount($params = array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('SELECT SUM(money) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}