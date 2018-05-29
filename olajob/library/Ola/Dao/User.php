<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Ola_Dao_User
 * @author tiansh
 *
 */
class Ola_Dao_User extends Common_Dao_Base{
	protected $_name = 'ola_user';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND register_time >= %d', strtotime($params['start_time'].'00:00:00'));
			unset($params['start_time']);
		} 
		if ($params['end_time']) {
			$sql.= sprintf(' AND register_time <= %d', strtotime($params['end_time'].'23:59:59'));
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
