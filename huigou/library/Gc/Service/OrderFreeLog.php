<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gc_Service_OrderFreeLog{

	/**
	 * 
	 * 取所有数据
	 */
	public static function getAllOrderFreeLog() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * 列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 * 
	 * 取单条记录
	 * @param int $id
	 */
	public static function getOrderFreeLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * 取最近的一条记录
	 */
	public static function getLast() {
		return self::_getDao()->getLast();
	}
	
	/**
	 *
	 * 根据商品id取免单记录
	 * @param int $goods_id
	 */
	public static function getOrderFreeLogByGoodsId($goods_id) {
		if (!intval($goods_id)) return false;
		return self::_getDao()->getListByGoodsId(intval($goods_id));
	}
	
	/**
	 *
	 * 根据期号取免单商品ID
	 * @param int $number
	 */
	public static function getOrderFreeLogByNumber($number) {
		if (!intval($number)) return false;
		return self::_getDao()->getOrderFreeLogByNumber(intval($number));
	}
	
	/**
	 * 
	 * 插入
	 * @param array $data
	 */
	public static function addOrderFreeLog($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		$data['status'] = 1;
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAddOrderFreeLog($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}
	
	/**
	 *
	 * 修改
	 * @param array $data
	 * @param int $id
	 */
	public static function updateOrderFreeLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['number'])) $tmp['number'] = $data['number'];
		if(isset($data['user_id'])) $tmp['user_id'] = $data['user_id'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['goods_title'])) $tmp['goods_title'] = $data['goods_title'];
		if(isset($data['goods_price'])) $tmp['goods_price'] = $data['goods_price'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['remark'])) $tmp['remark'] = $data['remark'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gc_Dao_OrderFreeLog
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_OrderFreeLog");
	}
}
