<?php
class Event_Dao_Clicks  extends Common_Dao_Base {
	protected $_name = '3g_activity_clicks';
	protected $_primary = 'id';
	
	public function getClickUsers($where =array(),$groupBy=array()){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$sql = sprintf("SELECT count(DISTINCT(uid))  total_users, event_type FROM %s WHERE %s %s ",$this->getTableName(),$where,$group);
		return Db_Adapter_Pdo::fetchAll($sql);	
	}
	
	}