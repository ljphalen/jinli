<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Fanli_Dao_WithdrawLog
 * @author tiansh
 *
 */
class Fanli_Dao_WithdrawLog extends Common_Dao_Base{
	protected $_name = 'fanli_withdraw_log';
	protected $_primary = 'id';	
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND create_time >= %d', $params['start_time']);
			unset($params['start_time']);
		}
		if ($params['end_time']) {
			$sql.= sprintf(' AND create_time <= %d', $params['end_time']);
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
