<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_ExperienceLog extends Common_Dao_Base {
	protected $_name = 'user_experience_log';
	protected $_primary = 'id';
	
	
	public function getEachTypeExperienceData($params){
		$where =  Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT count(DISTINCT(uid)) as total_users , sum(points) as total_points , type,FROM_UNIXTIME(add_time,'%Y%m%d') as add_date   FROM {$this->getTableName()} WHERE {$where}  GROUP BY  type,add_date ";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getExperienceSumData($params){
		$where =  Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT count(DISTINCT(uid)) as total_users , sum(points) as total_points ,FROM_UNIXTIME(add_time,'%Y%m%d') as add_date   FROM {$this->getTableName()} WHERE {$where}  GROUP BY  add_date ";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
?>