<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_Earn extends Common_Dao_Base {
	protected $_name = 'user_earn_score_log';
	protected $_primary = 'id';


	public function getAllActivateUserIds($params = array()) {
		$group = Db_Adapter_Pdo::sqlGroup($params);
		$sql   = sprintf("SELECT uid FROM %s %s", $this->getTableName(), $group);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function statUserSignin($params = array(), $params2 = array(), $orderby = array(), $limit = array()) {

		$where1   = Db_Adapter_Pdo::sqlWhere($params);
		$where2   = Db_Adapter_Pdo::sqlWhere($params2);
		$order    = Db_Adapter_Pdo::sqlSort($orderby);
		$page     = intval($limit['page']);
		$pagesize = intval($limit['pagesize']);
		$start    = ($page - 1) * $pagesize;

		$sql = sprintf('select count(*) from (SELECT `uid`,count(*) cnt FROM %s WHERE %s GROUP BY `uid`) t where %s', $this->getTableName(), $where1, $where2);

		$total = Db_Adapter_Pdo::fetchCloum($sql, 0);

		$sql = sprintf('select t.uid,t.cnt from (SELECT `uid`,count(*) cnt FROM %s WHERE %s GROUP BY `uid`) t where %s %s LIMIT %d,%d', $this->getTableName(), $where1, $where2, $order, $start, $pagesize);

		$data = Db_Adapter_Pdo::fetchAll($sql);
		return array($total,$data);

	}

	public function getActiviteUsersNumber($where){
		$whereBy = Db_Adapter_Pdo::sqlWhere($where);
		$sql  = sprintf( "select count(distinct(uid)) as users FROM %s WHERE %s",$this->getTableName(),$whereBy);
		$ret = Db_Adapter_Pdo::fetchAll($sql);
		return $ret;
	}
	
	//同一IP下所有用户ID
	public function getAllUserIdsFromSameIP($where=array()){
		$whereBy  = Db_Adapter_Pdo::sqlWhere($where);
		$sql  = sprintf("select distinct(uid) as uid,add_date,user_ip FROM %s  WHERE %s ",$this->getTableName(),$whereBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//得到可疑用户IP
	public function getDubiousIpData($date,$num=5){
		$sql = sprintf("select user_ip , count(distinct(uid)) user_amount FROM %s where add_date = %s group by user_ip having user_amount> %d  ",$this->getTableName(),$date,$num);
		$ret = Db_Adapter_Pdo::fetchAll($sql);
		return $ret;
	}

	
	//获得用户每天获得积分的统计数据
	public function getUserEarnData($where,$order,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sort = Db_Adapter_Pdo::sqlSort($order);
		$sql = sprintf(" SELECT  COUNT(DISTINCT uid) as user_number ,sum(score) as scores,group_id,DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as dates  from %s  WHERE %s GROUP  BY group_id,dates  %s LIMIT %d,%d ",$this->getTableName(),$where,$order,$page,$pageSize);
		return  Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//每天完成任务的统计数据
	public function getUserDoneTasksMsg($where,$orderBy,$page,$pageSize){
		$where =  Db_Adapter_Pdo::sqlWhere($where);
		$order = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf("SELECT COUNT(DISTINCE(uid)) AS user_number , COUNT(cat_id) times, group_id,cat_id, DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as `date` FROM %s WHERE %s GROUP BY `date`,cat_id %s LIMIT %d,%d ",$this->getTableName(),$where,$order,$page,$pageSize);
		 return  Db_Adapter_Pdo::fetchAll($sql);
		 "conent:{$page}:{$type}";
	}

	public function getPerDayUserAmount($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf("SELECT  COUNT(DISTINCT uid) as user_number ,sum(score) as earn_scores,group_id  from %s  WHERE  %s  GROUP  BY group_id",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//前一天完成任务数
	public function getDoneTasksData($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf("SELECT COUNT(DISTINCT(uid)) AS user_number , COUNT(cat_id) times,cat_id FROM %s WHERE  %s ",$this->getTableName(),$where);
		return Db_Adapter_Pdo::fetch($sql);
	}
	
	// 按天积分排名
	public function  getDayScoreRank($params,$group,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("SELECT SUM(score) as total_scores,uid FROM %s WHERE %s %s ORDER By total_scores DESC LIMIT %d,%d ",$this->getTableName(),$where,$groupBy,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	//
	public function getDayTaskNumber($params,$group,$page,$pageSize){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql = sprintf("SELECT COUNT(DISTINCT(cat_id))  as tasks_number,uid FROM %s WHERE %s %s  LIMIT %d,%d ",$this->getTableName(),$where,$groupBy,$page,$pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	//用户累积金币信息
	public function getSumScoresByUserRank($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT SUM(score) as deadline_scores, DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as  add_date  FROM {$this->getTableName()} WHERE {$where} GROUP BY add_date";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	//用户任务累计信息
	public function getSumTasksByUserRank($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT COUNT(DISTINCT(cat_id)) as deadline_tasks, DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') as  add_date  FROM {$this->getTableName()} WHERE {$where} GROUP BY add_date";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getTaskLogList($where) {
		$sql = "SELECT goods_id, FROM_UNIXTIME(add_time, '%Y-%m-%d') as `day`,count(DISTINCT uid) as peoples, count(`id`) as times, sum(`score`) as scores FROM {$this->_name}  WHERE goods_id IN (".implode(',',$where['goods_id']).") AND add_time >= {$where['sdate']} AND  add_time <= {$where['edate']}  GROUP BY `day`,`goods_id` ORDER BY `goods_id` ASC";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getTaskLogTotal($where) {
		$sql = "SELECT count(`id`) as num, FROM_UNIXTIME(add_time, '%Y-%m-%d') as `day` FROM {$this->_name}  WHERE goods_id IN (".implode(',',$where['goods_id']).") AND add_time >= {$where['sdate']} AND  add_time <= {$where['edate']} GROUP BY `day`";
		$row =  Db_Adapter_Pdo::fetch($sql);
		return isset($row['num'])?intval($row['num']):0;
	}
	
	public function resetUserDays(){
		$sql = "update user_reward_list set continus_days = 0 where DATEDIFF(CURDATE(),DATE_FORMAT(FROM_UNIXTIME(last_time),'%Y-%m-%d'))>1";
		return Db_Adapter_Pdo::execute($sql);
	}
}