<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Account_Dao_UserInfo
 * @author fanch
 *
*/
class Account_Dao_UserInfo extends Common_Dao_Base{
	protected $_name = 'game_user_info';
	protected $_primary = 'id';
	
	/**
	 * 减少用户积分专用方法
	 * @param int $points
	 * @param array $params
	 * @return 
	 */
	public function subtractPoints($points, $params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$where .= " AND `points` >= $points";
		$sql = sprintf('UPDATE %s SET `points`= points-%d WHERE %s', $this->getTableName(), $points, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 增加用户积分专用方法
	 * @param int $points
	 * @param array $params
	 * @return
	 */
	public function addPoints($points, $params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('UPDATE %s SET `points`= points+%d WHERE %s', $this->getTableName(), $points, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}