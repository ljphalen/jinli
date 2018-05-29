<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_VoIPUser extends Common_Dao_Base {
	protected $_name = '3g_voip_user';
	protected $_primary = 'id';
	
	
	//按天统计信息
	public function getCountByDate($page,$pageSize,$params,$groupBy,$sort){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$orderBy = Db_Adapter_Pdo::sqlSort($sort);
		$sql =sprintf( "SELECT COUNT(*) as total,date  FROM %s WHERE %s %s %s LIMIT %s,%s",$this->getTableName(),$where,$group,$orderBy,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql); 
	}
	
	public function getTotalActivatedNumber($params,$groupBy){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$sql =sprintf( "SELECT COUNT(*) as total  FROM %s WHERE %s %s ",$this->getTableName(),$where,$group);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function computeEveryDayData(){
		$sql= sprintf("SELECT COUNT(*) as v__total_user ,  SUM(total_seconds) AS v_total_seconds , SUM(exchange_sec) as  v_exchange_total_seconds FROM %s WHERE total_seconds >0" ,$this->getTableName());
		return Db_Adapter_Pdo::fetch($sql);
	}
	
}