<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_Label  extends  Common_Dao_Base {
	protected $_name = '3g_label';
	protected $_primary = 'id';
	
	
	public function getCatIdList($params,$group){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql =sprintf( "SELECT parent_id  FROM %s  WHERE  %s %s",$this->getTableName(),$where,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
