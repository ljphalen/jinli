<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Fj_Service_Cart{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 * 
	 * get Goods info by id
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update Goods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 * 
	 * delete Goods
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteBy($params=array()) {
	    if(empty($params))return false;
	    return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * add Goods
	 * @param array $data
	 */
	public static function add($data) {
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
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params, array('id'=>'DESC'));
	}
	
	
	/**
	 * 计算购物车信息，价格和商品数量
	 * @param unknown_type $cart_ids
	 */
	public static function getCartInfo($cart_ids) {
	    if(!is_array($cart_ids)) return false;
	    //计算商品价格
	    list(,$carts) = self::getsBy(array('id'=>array('IN', $cart_ids)), array('id'=>'DESC'));
	    
	    $total_price = $cart_num = 0;
	    foreach ($carts as $key=>$value) {
	        $total_price += $value['price'] * $value['goods_num'];
	        $cart_num += $value['goods_num'];
	    }
	    return array($cart_num, Common::money($total_price));
	}
	
	
	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['uid'])) $tmp['uid'] = intval($data['uid']);
		if(isset($data['open_id'])) $tmp['open_id'] = $data['open_id'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['goods_num'])) $tmp['goods_num'] = $data['goods_num'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Fj_Dao_Cart
	 */
	private static function _getDao() {
		return Common::getDao("Fj_Dao_Cart");
	}
}
