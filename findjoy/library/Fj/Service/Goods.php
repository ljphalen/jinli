<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Fj_Service_Goods{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGoods() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * get Goods list for page
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * get Goods info by id
	 * @param int $id
	 */
	public static function getGoods($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update Goods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function updateGoods($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 * 
	 * delete Goods
	 * @param int $id
	 */
	public static function deleteGoods($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add Goods
	 * @param array $data
	 */
	public static function addGoods($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function parise($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('praise', array('id'=>intval($id)));
	}
	
	
	/**
	 * 更新库存
	 * @param unknown_type $num
	 * @param unknown_type $id
	 */
	public static function updateQuantity($num, $id) {
	    return self::_getDao()->updateQuantity($num, $id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateSaleNum($num, $id) {
	    if (!$id || !$num) return false;
	    $goods = self::getGoods($id);
	    return self::_getDao()->update(array('sale_num'=>$goods['sale_num'] + $num), $id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateHits($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}

	/**
	 * 获取可用的商品
	 * @return bool|mixed
	 */
	public static function getAvailableGoods() {
		$list = self::_getDao()->getAll(array('id'=>'DESC'));
		return $list;
	}

	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['hk_price'])) $tmp['hk_price'] = $data['hk_price'];
		if(isset($data['start_time'])){
			if(is_array($data['start_time']) && isset($data['start_time'][1])){
				$data['start_time'][1] = intval($data['start_time'][1]);
			}
			$tmp['start_time'] = $data['start_time'];
		}
		if(isset($data['end_time'])){
			if(is_array($data['end_time']) && isset($data['end_time'][1])){
				$data['end_time'][1] = intval($data['end_time'][1]);
			}
			$tmp['end_time'] = $data['end_time'];
		}
		if(isset($data['stock_num'])) $tmp['stock_num'] = $data['stock_num'];
		if(isset($data['limit_num'])) $tmp['limit_num'] = $data['limit_num'];
		if(isset($data['sale_num'])) $tmp['sale_num'] = $data['sale_num'];
		if(isset($data['comment_num'])) $tmp['comment_num'] = $data['comment_num'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['ishot'])) $tmp['ishot'] = intval($data['ishot']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Fj_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Fj_Dao_Goods");
	}
}
