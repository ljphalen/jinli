<?php
class Event_Dao_ActivityResult  extends Common_Dao_Base {
	protected $_name = '3g_activity_result';
	protected $_primary = 'id';
	
	
	public function totalSendScoresPerDay($where,$group){
		$whereBy = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy  = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("SELECT COUNT(prize_id) as prize_number ,prize_id,add_date FROM %s WHERE %s %s",$this->getTableName(),$whereBy,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	}