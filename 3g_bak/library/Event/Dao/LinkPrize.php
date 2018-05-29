<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Event_Dao_LinkPrize extends Common_Dao_Base {
	protected $_name    = '3g_event_link_prize';
	protected $_primary = 'id';
	
	
	public function getScoreUsers($where,$group){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("select  date,count(uid) as total_times ,  count(distinct(uid)) total_users, prize_level ,prize_status  from %s where %s %s ",$this->getTableName(),$where,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	
	public function getPrizeUsers($where,$group){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("select count(distinct(uid)) as prize_users , count(uid) as prize_times ,date,prize_status  from %s WHERE  %s %s",$this->getTableName(),$where,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}