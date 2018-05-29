<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_OrderFreeNumber{

	/**
	 * 
	 * 取所有数据
	 */
	public static function getAllOrderFreeNumber() {
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
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseOrderFreeNumber($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseOrderFreeNumber(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseOrderFreeNumberCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * 取单条记录
	 * @param int $id
	 */
	public static function getOrderFreeNumber($id) {
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
	 * 插入
	 * @param array $data
	 */
	public static function addOrderFreeNumber($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * 修改
	 * @param array $data
	 * @param int $id
	 */
	public static function updateOrderFreeNumber($data, $id) {
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
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_OrderFreeNumber
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_OrderFreeNumber");
	}
}
