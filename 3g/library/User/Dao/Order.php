<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_Order extends  Common_Dao_Base {
	protected $_name = 'user_order_info';
	protected $_primary = 'id';
	
	
	public function unHandleOrders($page,$pageSize,$where,$order){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$sql = sprintf("SELECT * FROM %s WHERE id NOT IN  (SELECT id FROM %s where (order_status = -1 AND pay_status = 3) OR (order_status = 2 AND pay_status =1) )  %s LIMIT %s ,  %s",$this->getTableName(),$this->getTableName(),$orderBy,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getUnHandleCount(){
		$sql = sprintf("SELECT count(*) FROM %s WHERE id NOT IN  (SELECT id FROM %s where (order_status = -1 AND pay_status = 3) OR (order_status = 2 AND pay_status =1) ) ",$this->getTableName(),$this->getTableName());
		return  Db_Adapter_Pdo::fetchCloum($sql,0);
	}
	
	//	//每天处理订单统计
	public function getOrderSuccessAmountByDay($where,$orderBy ,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$order = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf("SELECT COUNT(DISTINCE(uid)) AS user_amount ,COUNT(*) AS success_amount , DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as add_date,SUM(total_cost_scores) AS cost_scores  FROM %s WHERE %s  GROUP BY add_date %s LIMIT %d,%d",$this->getTableName(),$where,$order,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql); 
	}
	
	//每天所有订单
	public function getUserExchangeMsg($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf("SELECT * FROM %s WHERE  %s",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function getTotalNumber($params,$num){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$subSql = "SELECT COUNT(*)  as total , recharge_number,DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as add_date FROM  {$this->getTableName() } WHERE {$where}  GROUP BY add_date, recharge_number HAVING total>{$num}";
		$sql = "SELECT COUNT(*) number FROM ({$subSql}) temp_table ;";
		return Db_Adapter_Pdo::fetchCloum($sql,0);
	}
	
	public function getRechargeTimesInfo($params,$num,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT COUNT(*)  as total , recharge_number,DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as add_date FROM  {$this->getTableName() } WHERE {$where}  GROUP BY add_date, recharge_number HAVING total>={$num}  ORDER BY add_date DESC, total DESC LIMIT {$page},{$pageSize}";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function TotalMonthRechargeNum($params,$group,$number){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sub ="SELECT  COUNT(*) as total_times ,recharge_number FROM {$this->getTableName()} WHERE {$where}  {$groupBy} HAVING total_times >= {$number}";
		$sql = "SELECT COUNT(*) as total FROM ({$sub}) tmp_table;";
		return Db_Adapter_Pdo::fetchCloum($sql);
	}
	
	public function getRechargedMsg($params,$group,$number,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = "SELECT  COUNT(*) as total_times ,recharge_number FROM {$this->getTableName()} WHERE {$where}  {$groupBy} HAVING total_times >= {$number} ORDER BY total_times DESC LIMIT {$page},{$pageSize}";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}