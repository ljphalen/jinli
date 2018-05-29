<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Event_Dao_LinkUser extends Common_Dao_Base {
	protected $_name    = '3g_event_link_user';
	protected $_primary = 'id';

	public function getUserNumbers(){
		$sql = sprintf(" select count(*) as users ,cur_date from %s  group by cur_date order by cur_date asc" ,$this->getTableName());
		$data = Db_Adapter_Pdo::fetchAll($sql);
		return $data;
	}
}