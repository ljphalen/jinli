<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_WxHelpUser extends Common_Dao_Base {
	protected $_name    = '3g_wx_help_user';
	protected $_primary = 'id';

	public function getIncrByHI($id,$startTime, $endTime) {
		$sql = sprintf("SELECT COUNT(*) as num,FROM_UNIXTIME(`created_at`, '%s')  AS date_hi FROM %s WHERE event_id = %s AND created_at > UNIX_TIMESTAMP('%s') AND created_at < UNIX_TIMESTAMP('%s') GROUP BY date_hi", '%d/%m %H:%i',$this->getTableName(), $id, $startTime, $endTime);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}