<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Fanli_Dao_FanxianLog
 * @author tiansh
 *
 */
class Fanli_Dao_Order extends Common_Dao_Base{
	protected $_name = 'fanli_order';
	protected $_primary = 'id';	
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['create_start_time'] && $params['create_end_time']) {
			$sql.= sprintf(' AND create_time >= %d AND create_time <= %d', $params['create_start_time'], $params['create_end_time']);
			unset($params['create_start_time']);
			unset($params['create_end_time']);
		}
		if ($params['order_start_time'] && $params['order_end_time']) {
			$sql.= sprintf(' AND pay_time <= %d AND pay_time <= %d', $params['order_start_time'], $params['order_end_time']);
			unset($params['order_start_time']);
			unset($params['order_end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
