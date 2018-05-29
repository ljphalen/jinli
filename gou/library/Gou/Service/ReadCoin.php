<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desReadCoiniption here ...
 * @author tiansh
 *
 */
class Gou_Service_ReadCoin{
	

	/**
	 *
	 * Enter desReadCoiniption here ...
	 */
	public static function getAllReadCoin() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}	
	
	public static function getReadCoin($id) {
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter desReadCoiniption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter desReadCoiniption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getCanUseReadcoin($goods_id, $limit = 1) {
		if(!intval($goods_id)) return false;
		$ret =  self::_getDao()->getCanUseReadcoin($goods_id, $limit);
		$total = self::_getDao()->getCanUseReadcoinCount($goods_id);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addReadCoin($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateReadCoin($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByIds($data, $ids) {
		if (!is_array($data) || !is_array($ids)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateByIds($data, $ids);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteReadCoin($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchaddReadCoin($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter desReadCoiniption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['card_number'])) $tmp['card_number'] = $data['card_number'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = intval($data['goods_id']);
		if(isset($data['order_id'])) $tmp['order_id'] = $data['order_id'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_ReadCoin
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ReadCoin");
	}
}
