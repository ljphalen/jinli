<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_WantLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllWantLog() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * get list
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
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getNomalWants($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getNomalWants(intval($start), intval($limit), $params);
		$total = self::_getDao()->getNomalWantsCount($params);
		return array($total, $ret);
	}
		
	/**
	 * getCount
	 * @param array $params
	 * @return int
	 */
	public static function getCount($params = array()) {
		return self::_getDao()->count($params);
	}
	
	/**
	 * get an goods by uid and goodids
	 * @param int $uid
	 * @param int $goodsid
	 */
	public static function getByUidAndGoodsId($uid, $goods_id, $type) {
		if (!$uid || !$goods_id) return false;
		return self::_getDao()->getby(array('uid'=>intval($uid), 'goods_id'=>intval($goods_id), 'goods_type'=>$type));
	}
	/**
	 * get an goods by goodid
	 * @param int $goodsid
	 */
	public static function getByGoodsId($goods_id, $type) {
		if (!$goods_id) return false;
		$ret = self::_getDao()->getWantLogsByGoodsId($goods_id, $type);
		$total = self::_getDao()->count(array('goods_id'=>intval($goods_id), 'goods_type'=>$type));
		return array($total, $ret);
	}

	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getWantLogsByTime($page = 1, $limit = 10, array $params = array()) {
		$data = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		if ($params['start']) $data['start'] = intval($params['start']);
		if ($params['end']) $data['end'] = intval($params['end']);
		$ret = self::_getDao()->getWantLogsByTime($start, $limit, $data);
		$total = self::_getDao()->countByTime($data);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getWantLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateWantLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteWantLog($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addWantLog($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['uid'])) $tmp['uid'] = intval($data['uid']);
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = intval($data['goods_id']);
		if(isset($data['goods_type'])) $tmp['goods_type'] = intval($data['goods_type']);
		if(isset($data['goods_name'])) $tmp['goods_name'] = $data['goods_name'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_WantLog
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_WantLog");
	}
}
