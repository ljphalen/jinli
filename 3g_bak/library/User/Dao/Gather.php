<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_Gather extends Common_Dao_Base
{
	protected $_name = 'user_gather_info';
	protected $_primary = 'id';
	
	
	public function getSumScoresInfo($params=array()) {
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf("SELECT SUM(total_score) as total_scores,SUM(remained_score) as total_remained_scores ,COUNT(*) as total_users  from user_gather_info WHERE %s  ",$where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	//激活用户中心的用户统计
	public function getIncreUserAmount($where,$groupBy,$orderBy,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$group  = Db_Adapter_Pdo::sqlGroup($groupBy);
		$order = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf("SELECT COUNT(*) as amount_users ,SUM(total_score) as total, SUM(remained_score) AS remained,created_time  FROM %s WHERE %s  %s  %s Limit %d,%d",$this->getTableName(),$where,$group,$order,$page,$pageSize);
		return  Db_Adapter_Pdo::fetchAll($sql);
	}

	public function dayIncreUserAmount($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql  = sprintf("SELECT COUNT(*) as incre_amount FROM %s WHERE %s",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
}