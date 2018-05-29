<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Admin_Dao_User
 * @author rainkid
 *
 */
class Gionee_Dao_User extends Common_Dao_Base {
	protected $_name = '3g_user';
	protected $_primary = 'id';

	/**
	 *
	 * Enter description here ...
	 */
	public function getUser($id) {
		return self::get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function getAllUser() {
		return array(self::count(), self::getAll());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $username
	 */
	public function getByUserName($username) {
		if (!$username) return false;
		return self::getBy(array('username' => $username));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $mobile
	 */
	public function getByMobile($mobile) {
		if (!$mobile) return false;
		return self::getBy(array('mobile' => $mobile));
	}

	/**
	 *
	 * @param array $uids
	 * @return multitype:
	 */
	public function getUserByUids($uids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s ', $this->_name, Db_Adapter_Pdo::quoteArray($uids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * @param array $uids
	 * @return array:
	 */
	public function getListByUids($uids) {
		$sql = sprintf('SELECT * FROM %s WHERE id IN %s', $this->_name, Db_Adapter_Pdo::quoteArray($uids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 * 抽奖后批量修改免单数
	 */
	public function updateFreeNumberByIds($ids) {
		$sql = sprintf('UPDATE  %s SET free_num = free_num + 1 WHERE id IN %s', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}

	/**
	 *
	 * 获取分页列表数据
	 * @param int $page
	 * @param int $limit
	 */
	public function getListByTime($start = 0, $limit = 20) {
		$time = Common::getTime();
		$sql  = sprintf('SELECT * FROM %s WHERE taobao_refresh_time > 0 AND taobao_refresh_time <= ' . ($time + 3600) . ' AND taobao_refresh_expires <= ' . $time . ' ORDER BY id DESC LIMIT %d,%d', $this->getTableName(), intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * 根据时间统计总数
	 */
	public function countByTime() {
		$time = Common::getTime();
		$sql  = sprintf('SELECT COUNT(*) FROM %s WHERE taobao_refresh_time > 0 AND taobao_refresh_time <= ' . ($time + 3600) . ' AND taobao_refresh_expires <= ' . $time . '', $this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}

	/*
	 * 统计会员总数
	 * 
	 */
	public function getCount() {
		$sql = sprintf('SELECT COUNT(*) FROM %s ', $this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}

	/**
	 *
	 * 签到总数
	 */
	public function getSignCount() {
		$sql = sprintf('SELECT sum(signin_num) FROM %s', $this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql);
	}

	/**
	 *
	 * 获取分页列表数据
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public function getList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
		if ($params['register_time']) {

		}
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sort  = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql   = sprintf('SELECT * FROM %s WHERE %s %s LIMIT %d,%d', $this->getTableName(), $where, $sort, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}


	/**
	 * 按天计算注册数
	 */
	public  function countByDays($page,$pageSize,$where){
		$where = Db_Adapter_Pdo::sqlWhere($where);
		$sql = "SELECT COUNT(*) as total, FROM_UNIXTIME(register_time,'%Y%m%d') as reg_time  FROM {$this->getTableName()}  WHERE  {$where}  GROUP BY reg_time  ORDER BY reg_time DESC  LIMIT {$page},{$pageSize}";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 按条件得到总注册数
	 */
	
	public function countBy($params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = "SELECT COUNT(*)  total FROM {$this->getTableName()} WHERE {$where} GROUP BY  FROM_UNIXTIME(register_time,'%Y%m%d')";
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['register_date']) {
			$sql .= sprintf(' AND register_time >= %d AND register_time <= %d', strtotime($params['register_date'] . '00:00:00'), strtotime($params['register_date'] . '23:59:59'));
			unset($params['register_date']);
		}
		return Db_Adapter_Pdo::sqlWhere($params) . $sql;
	}


	public function vestList($start = 0, $limit = 20) {
		$sql   = sprintf('SELECT COUNT(id) AS num, imei_id  FROM %s WHERE `imei_id` > 0 GROUP BY imei_id HAVING num > 1 LIMIT %d,%d', $this->getTableName(), intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}
