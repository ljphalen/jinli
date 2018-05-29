<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Dao_OrderGoods extends Common_Dao_Base {
	protected $_name = "user_order_goods";
	protected $_primary = "id";

	public function getTimesByGoodsId($where) {
		$sql = "SELECT count(uoi.id) as num ,FROM_UNIXTIME(uoi.add_time, '%Y-%m-%d') as `day` FROM user_order_info AS uoi         LEFT JOIN user_order_goods AS uog ON uoi.id = uog.order_id WHERE uog.goods_id={$where['id']} AND uoi.add_time >= {$where['sdate']} AND  uoi.add_time <= {$where['edate']}  GROUP BY `day`";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getPeoplesByGoodsId($where) {
		$sql = "SELECT count(DISTINCT uoi.uid) as num ,FROM_UNIXTIME(uoi.add_time, '%Y-%m-%d') as `day` FROM user_order_info AS uoi         LEFT JOIN user_order_goods AS uog ON uoi.id = uog.order_id WHERE uog.goods_id={$where['id']} AND uoi.add_time >= {$where['sdate']} AND  uoi.add_time <= {$where['edate']} GROUP BY `day`";
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	public function getTotalByGoodsId($where) {
		$sql = "SELECT FROM_UNIXTIME(uoi.add_time, '%Y-%m-%d') as `day`,SUM(uoi.ordercrach) as `cost_money`,SUM(uoi.total_cost_scores) as `cost_score`   FROM user_order_info AS uoi         LEFT JOIN user_order_goods AS uog ON uoi.id = uog.order_id WHERE uog.goods_id={$where['id']} AND uoi.add_time >= {$where['sdate']} AND  uoi.add_time <= {$where['edate']}  GROUP BY `day`";
		return Db_Adapter_Pdo::fetchAll($sql);
	}


}