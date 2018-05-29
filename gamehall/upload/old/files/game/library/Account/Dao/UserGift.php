<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Account_Dao_UserGift
 * @author fanch
 *
*/
class Account_Dao_UserGift extends Common_Dao_Base{
	protected $_name = 'game_user_gift';
	protected $_primary = 'id';
	
	/**
	 * 按年查询数据
	 * @param array $params
	 * @param int $year
	 * @return
	 */
	public function getByYear($params, $year){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$where .= " AND FROM_UNIXTIME(`create_time`, '%Y') = '$year' ";
		$sql = sprintf('SELECT * FROM %s WHERE %s LIMIT 1', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}
}