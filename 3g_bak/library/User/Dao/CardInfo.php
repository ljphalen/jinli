<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


//礼品卡类
class User_Dao_CardInfo extends Common_Dao_Base
{
	protected $_name = 'user_card_info';
	protected $_primary = 'id';
	
	
	public function getCategory($params,$group){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql =sprintf( "SELECT * FROM %s  WHERE %s  %s",$this->getTableName(),$where,$groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
		
	}
}