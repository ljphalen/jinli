<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_User
 * @author rainkid
 *
 */
class Gou_Dao_User extends Common_Dao_Base{
	protected $_name = 'gou_user';
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
		return self::getby(array('username'=>$username));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $mobile
	 */
	public function getByMobile($mobile) {
		if (!$mobile) return false;
		return self::getby(array('mobile'=>$mobile));
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
	 * 获取
	 * @return multitype:
	 */
	public function getFreezeUser($start, $limit) {
		$sql = sprintf('SELECT * FROM %s WHERE freeze_sliver_coin != 0 LIMIT %d,%d', $this->_name, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/** 获取
	* @return multitype:
	*/
	public function getCountFreezeUser() {
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE freeze_sliver_coin != 0', $this->_name);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
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
		$sql = sprintf('SELECT * FROM %s WHERE taobao_refresh_time > 0 AND taobao_refresh_time <= '.($time + 3600).' AND taobao_refresh_expires <= '.$time.' ORDER BY id DESC LIMIT %d,%d', $this->getTableName(), intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * 根据时间统计总数
	 */
	public function countByTime() {
		$time = Common::getTime();
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE taobao_refresh_time > 0 AND taobao_refresh_time <= '.($time + 3600).' AND taobao_refresh_expires <= '.$time.'', $this->getTableName());
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	

	/**
	 *
	 * 更新用户订单数
	 */
	public function updateOrderNum($out_uid) {
		if (!$out_uid) return false;
		return $this->increment('order_num', array('out_uid'=>$out_uid));
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return string
	 */
	public function _cookParams($params) {
		$sql = ' ';
		if ($params['start_time']) {
			$sql.= sprintf(' AND register_time >= %d', strtotime($params['start_time'].'00:00:00'));
			unset($params['start_time']);
		} 
		if ($params['end_time']) {
			$sql.= sprintf(' AND register_time <= %d', strtotime($params['end_time'].'23:59:59'));
			unset($params['end_time']);
		}
		return Db_Adapter_Pdo::sqlWhere($params).$sql;
	}
}
