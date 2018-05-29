<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Activity_Dao_Share
 * @author rainkid
 *
 */
class Activity_Dao_Share extends Common_Dao_Base{
	protected $_name = 'gou_activity_share';
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
