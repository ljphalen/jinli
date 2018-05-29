<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_Gamesplan extends Common_Dao_Base {
	protected $_name = '3g_worldcup_plan';
	protected $_primary = 'id';
	
	public function getshaudles($field,$where,$group,$order){
		
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sort = Db_Adapter_Pdo::sqlSort($order);
		$sql= sprintf('SELECT %s FROM %s WHERE %s  %s  %s',$field,$this->getTableName(),$where,$groupBy,$sort);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}