<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_ModelContent  extends Common_Dao_Base
{
	protected $_name = '3g_model_content';
	protected $_primary = 'id';
	
	public function getCount($where){
		$sql = sprintf("SELECT COUNT(*) FROM %s WHERE %s",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
	
	public function getDataList($page,$pageSize,$where,$order){
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$sql = sprintf("SELECT * FROM %s WHERE  %s  %s LIMIT %d,%d",$this->getTableName(),$where,$orderBy,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}