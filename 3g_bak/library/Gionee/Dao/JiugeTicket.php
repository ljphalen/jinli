<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

//九歌彩票合作
class Gionee_Dao_JiugeTicket extends Common_Dao_Base {
	
	protected $_name = '3g_jiuge';
	protected $_primary = 'id';
	
	//获得某一时段内总信息数
	public function getPeriodTotal($wheres,$groupBy){
		$where = Db_Adapter_Pdo::sqlWhere($wheres);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$sql= sprintf('SELECT count(*) total FROM %s WHERE %s %s',$this->getTableName(),$where,$group);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	
	public function getAllUsers($where,$groupBy,$field){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$group = Db_Adapter_Pdo::sqlGroup($groupBy);
		$sql= sprintf('SELECT count(*) total, %s FROM %s WHERE %s %s',$field,$this->getTableName(),$where,$group);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}