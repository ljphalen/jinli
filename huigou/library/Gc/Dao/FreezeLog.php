<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * User_Dao_FreezeLog
 * @author tiansh
 *
 */
class Gc_Dao_FreezeLog extends Common_Dao_Base{
	protected $_name = 'gc_freeze_log';
	protected $_primary = 'id';
	
	/**
	 * get want logs by goods_id
	 * @param int $goods_id
	 * @param array $params
	 */
	public function getFreezeLogsByUid($uid) {
		$sql = sprintf('SELECT * FROM %s WHERE out_uid = %s ',$this->getTableName(), $uid);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * 根据标识修改冻结状态
	 * @param array $data
	 * @param int $value
	 */
	public function updateStatus($status, $mark) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE mark = %s', $this->getTableName(), Db_Adapter_Pdo::quote($status), Db_Adapter_Pdo::quote($mark));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
}
