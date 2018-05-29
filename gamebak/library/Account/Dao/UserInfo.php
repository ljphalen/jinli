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
	
	/**
	 * 获取指定日期生日的用户
	 * @param int $start
	 * @param int $limit
	 * @param string today
	 * @return 
	 */
	public function getListByBirthday($start = 0, $limit = 10, $today){
		$where =" DATE_FORMAT(birthday, '%m-%d') = '$today' ";
		$sql = sprintf('SELECT * FROM %s WHERE %s LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 统计指定日期生日的用户数量
	 * @param string today
	 * @return
	 */
	public function getCountByBirthday($today){
		$where =" DATE_FORMAT(birthday, '%m-%d') = '$today' ";
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}