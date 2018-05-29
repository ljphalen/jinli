<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class  User_Dao_Punch extends Common_Dao_Base
{
	protected $_name = "user_punch_log";
	protected $_primary = "id";
	
	//检测用户当天是否打卡
	public function checkSign($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql= sprintf(" SELECT EXISTS(*) FROM %s WHERE %s",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
}