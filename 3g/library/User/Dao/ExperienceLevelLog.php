<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class  User_Dao_ExperienceLevelLog extends Common_Dao_Base
{
	protected $_name = "user_experience_level_log";
	protected $_primary = "id";
	
	
	public function getPerDayUpgradeUser($params,$group){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup(($group));
		$sql = sprintf("SELECT COUNT(*) as total_number, `date`,new_level,old_level FROM %s WHERE %s %s",$this->getTableName(),$where,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
