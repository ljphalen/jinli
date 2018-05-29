<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_ModelType extends  Common_Dao_Base {
	
	protected $_name = '3g_model_type';
	protected $_primary = 'id';
	
	public function getTypeValueData($where,$order,$group){
		$params = Db_Adapter_Pdo::sqlWhere($where);
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("SELECT `value`, type,id,ext  FROM %s WHERE %s %s %s ",$this->getTableName(),$params,$groupBy,$orderBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}