<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_QuizResult extends Common_Dao_Base {
	protected $_name = 'user_quiz_result';
	protected $_primary = 'id';
	
	
	public function getAnswerUserData($where,$group=array()){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = "SELECT COUNT(DISTINCT(uid)) AS quiz_users,COUNT(selected) as quiz_times , FROM_UNIXTIME(add_time,'%Y%m%d') as add_date FROM {$this->getTableName()} WHERE {$where} GROUP BY add_date ORDER BY add_date DESC";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getDayAnswerData($where,$group){
		 $where = Db_Adapter_Pdo::sqlWhere($where);
		 $groupBy = Db_Adapter_Pdo::sqlGroup($group);
		 $sql =sprintf( "SELECT uid,count(is_right) as total,is_right  FROM %s WHERE %s %s ",$this->getTableName(),$where,$groupBy);
		 return Db_Adapter_Pdo::fetchAll($sql);
	}
	
}
