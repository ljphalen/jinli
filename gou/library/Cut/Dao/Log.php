<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Cut_Dao_Log
 * @author ryan
 *
 */
class Cut_Dao_Log extends Common_Dao_Base{
	protected $_name = 'cut_log';
	protected $_primary = 'id';
	
	
	public function _cookParams($params) {
	    $sql = ' ';
	    if ($params['start_time']) {
	        $sql.= sprintf(' AND create_time > %d', $params['start_time']);
	        unset($params['start_time']);
	    }
	    if ($params['end_time']) {
	        $sql.= sprintf(' AND create_time < %d', $params['end_time']);
	        unset($params['end_time']);
	    }
	    return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
	
}
