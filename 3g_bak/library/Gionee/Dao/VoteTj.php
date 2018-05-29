<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_VoteTj extends Common_Dao_Base {
	protected $_name = '3g_worldcup_record';
	protected $_primary = 'id';

	
	public  function  getUserRecord($page,$size,$where=array(),$group=array(),$order=array()){
		$wheres = Db_Adapter_Pdo::sqlWhere($where);
		$group = Db_Adapter_Pdo::sqlGroup($group);
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$sql =sprintf("SELECT sum(score) scores,count(*) total,uid,phone,utype FROM %s WHERE %s  %s  %s LIMIT %d,%d ",$this->getTableName(),$wheres,$group,$orderBy,intval($page),intval($size));
		return  Db_Adapter_Pdo::fetchAll($sql);
		
	}
	
	//根据用户ID获得所有信息
	public function getRecordData($page,$size,$where=array(),$order=array()){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$order = Db_Adapter_Pdo::sqlSort($order);
		$sql = sprintf("SELECT * FROM  %s WHERE  %s  %s  LIMIT  %d , %d",$this->getTableName(),$where,$order,intval($page),intval($size));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//
	public function getCount($param=array() ,$group=array()){
		$where = Db_Adapter_Pdo::sqlWhere($param);
		$group = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("SELECT count(*) total  FROM %s WHERE %s  %s",$this->getTableName(),$where,$group);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}