<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_Uprivilege extends Common_Dao_Base
{
	protected $_name = 'user_level_privilege';
	protected $_primary = 'id';
	
	/**
	 * 返回商品的用户等级信息
	 * @param int $page 页码
	 * @param int $pageSize 每页显示数
	 * @param array $where  查询条件
	 * @param array $group 聚合条件
	 * @param array $orderBy 排序
	 */
	
	public function getLevelGoods($page,$pageSize,$where,$group ,$orderBy){
			$whereBy =  Db_Adapter_Pdo::sqlWhere($where);
			$groupBy  = Db_Adapter_Pdo::sqlGroup($group);
			$order       = Db_Adapter_Pdo::sqlSort($orderBy);
			$sql = sprintf("SELECT  * FROM %s  WHERE %s %s  %s  LIMIT %s , %s", $this->getTableName(),$whereBy,$groupBy,$order,$page,$pageSize);
			return  Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//聚合子查询
	public function countByParams($where,$group){
		$wheres = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		if($group){
			$field  =  implode(' ,', $group);
		}else{
			$field = '*';
		}
		$subSql = sprintf("SELECT  %s  FROM %s  WHERE  %s %s  ",$field,$this->getTableName(),$wheres,$groupBy);
		$sql = sprintf("SELECT COUNT(*) FROM (%s)  AS t",$subSql);
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
	
	//
	public function countBy($where=array()){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sql = sprintf("SELECT COUNT(*) FROM %s WHERE %s", $this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
}