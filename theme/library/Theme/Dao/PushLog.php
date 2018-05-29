<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Theme_Dao_Pushlog
 * @author tiansh
 *
 */
class Theme_Dao_Pushlog extends Common_Dao_Base{
	protected $_name = 'theme_push_log';
	protected $_primary = 'id';
	
	/**
	 * 指量更新
	 * @param array $data
	 * @param array $params
	 * @return boolean
	 */
	public function updateByRids($data, $rids, $msg_id) {
		if (!is_array($data) || !is_array($rids) || !$msg_id) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE rid in %s AND msg_id = %d', $this->getTableName(), Db_Adapter_Pdo::sqlSingle($data), Db_Adapter_Pdo::quoteArray($rids), $msg_id);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	
}
