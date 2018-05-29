<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 单击商品配置Service
 * Enter description here ...
 * @author ljphalen@163.com
 *
 */
class Goods_Service_Cofig{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateAdTJ($id) {
		if (!$id) return false;
		return self::getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getById($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params, $orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateById($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteById($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret = self::getDao()->insert($data);
		if(!$ret) return false;
		return self::getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['payid'])) $tmp['payid'] = $data['payid'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['coin'])) $tmp['coin'] = $data['coin'];
		if(isset($data['talentpoint'])) $tmp['talentpoint'] = $data['talentpoint'];
		if(isset($data['desc'])) $tmp['desc'] = $data['desc'];
		if(isset($data['goodstype'])) $tmp['goodstype'] = $data['goodstype'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Payment_Dao_PayFlowLog
	 */
	private static function getDao() {
		return Common::getDao("Goods_Cache_Config");
	}
}
