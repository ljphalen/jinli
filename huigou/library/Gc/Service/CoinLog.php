<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gc_Service_CoinLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllCoinLog() {
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
	 * getCount
	 * @param array $params
	 * @return int
	 */
	public static function getCount($params = array()) {
		return self::_getDao()->count($params);
	}
	
	/**
	 * get an goods by goodid
	 * @param int $goodsid
	 */
	public static function getCoinLogsByUid($uid) {
		if (!intval($uid)) return false;
		$ret = self::_getDao()->getCoinLogsByUid($uid);
		$total = self::_getDao()->count(array('uid'=>intval($uid)));
		return array($total, $ret);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCoinLog($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateCoinLog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteCoinLog($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addCoinLog($data) {
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
		if(isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if(isset($data['coin_type'])) $tmp['coin_type'] = $data['coin_type'];
		if(isset($data['unfreeze_type'])) $tmp['unfreeze_type'] = intval($data['unfreeze_type']);
		if(isset($data['coin'])) $tmp['coin'] = $data['coin'];
		if(isset($data['msg'])) $tmp['msg'] = $data['msg'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gc_Dao_CoinLog
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_CoinLog");
	}
}
