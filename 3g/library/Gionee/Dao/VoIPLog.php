<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Dao_VoIPLog extends Common_Dao_Base {
	protected $_name = '3g_voip_log';
	protected $_primary = 'id';

	public function getCensusDataByDate($page, $pageSize, $where, $group, $sort) {
		$where   = Db_Adapter_Pdo::sqlWhere($where);
		$group   = Db_Adapter_Pdo::sqlGroup($group);
		$orderBy = Db_Adapter_Pdo::sqlSort($sort);
		$sql     = sprintf("SELECT COUNT(*) as total, COUNT(DISTINCT(caller_phone)) as t1, date FROM %s WHERE %s %s %s  LIMIT %s, %s", $this->getTableName(), $where, $group, $orderBy, $page, $pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	//获取通话列表数据
	public function getCommutniateList($page, $pageSize, $where, $group, $sort) {
		$where   = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$orderBy = Db_Adapter_Pdo::sqlSort($sort);
		$sql     = sprintf("SELECT *,COUNT(called_phone) AS total  FROM (SELECT id,called_time, caller_phone,called_phone,area FROM %s WHERE %s  %s ) AS b  %s %s  LIMIT %s,%s", $this->getTableName(), $where, $orderBy, $groupBy, $orderBy, $page, $pageSize);
	
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	//获得去重后的点击用户数
	public function getindependentUserAmount($page, $pageSize, $where, $group, $sort) {
		$where   = Db_Adapter_Pdo::sqlWhere($where);
		$group   = Db_Adapter_Pdo::sqlGroup($group);
		$orderBy = Db_Adapter_Pdo::sqlSort($sort);
		$sql     = sprintf("SELECT COUNT(DISTINCT(caller_phone)) as amount FROM %s WHERE %s %s %s LIMIT %s,%s", $this->getTableName(), $where, $group, $orderBy, $page, $pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/////获得单个用户拔打次数信息
	public function getPerUserCallTimesInfo($page, $pageSize, $where, $group, $order) {
		$where   = Db_Adapter_Pdo::sqlWhere($where);
		$orderBy = Db_Adapter_Pdo::sqlSort($order);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql     = sprintf("SELECT  COUNT(caller_phone) as times ,caller_phone  FROM %s WHERE %s %s  %s LIMIT %s,%s", $this->getTableName(), $where, $groupBy, $orderBy, $page, $pageSize);
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function countBy($where, $group) {
		$where   = Db_Adapter_Pdo::sqlWhere($where);
		$groupBy = Db_Adapter_Pdo::sqlGroup($group);
		$sql     = sprintf("SELECT COUNT(*) FROM %s WHERE %s %s", $this->getTableName(), $where, $groupBy);
		return Db_Adapter_Pdo::fetchAll($sql);
	}


	public function getTotalTimeByMonth($mobile, $m = '') {
		if (empty($m)) {
			$m = date('Ym');
		}
		$where = array(
			'caller_phone' => $mobile,
			'date'         => array(array('>=', "{$m}01"), array('<=', "{$m}31")),
		);

		if ($m == '-1') {
			unset($where['date']);
		}
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sql   = sprintf("SELECT sum(`duration`) as num FROM %s WHERE %s", $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetch($sql);
	}

	public function getCalledStat($m, $called=false) {
		if (empty($m)) {
			$m = date('Ym');
		}
		$where = array(
			'date'           => array(array('>=', "{$m}01"), array('<=', "{$m}31")),
		);

		if ($called) {
			$where['connected_time'] = array('>', 0);
		}
		$where = Db_Adapter_Pdo::sqlWhere($where);

		$sql = sprintf("SELECT  `date`,SUM(`duration`) AS totalTime, COUNT(`id`) AS totalTimes,COUNT(DISTINCT(caller_phone)) AS totalPerson  FROM %s WHERE %s GROUP BY `date` ORDER BY `date` DESC, `called_time` DESC ", $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchAll($sql);
	}


	public function getDurationTimeByHour($st,$et) {
		$sql = sprintf("SELECT  count(`id`) as num, SUM(`duration`) as total FROM %s WHERE duration> 0 AND called_time > %d AND called_time <= %d", $this->getTableName(), $st, $et);
		return Db_Adapter_Pdo::fetch($sql);
	}


}