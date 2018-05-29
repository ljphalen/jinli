<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_OrderGoods {

	public static function add($params) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	public static function getsBy($params = array(), $order = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $order);
	}


	public static function getBy($params = array(), $order = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $order);
	}
	
	
	static public function getTimesByGoodsId($where) {
		return self::_getDao()->getTimesByGoodsId($where);
	}

	static public function getPeoplesByGoodsId($where) {
		return self::_getDao()->getPeoplesByGoodsId($where);
	}

	static public function getTotalByGoodsId($where) {
		return self::_getDao()->getTotalByGoodsId($where);
	}

	public static function addOrderGoodsInfo($orderId,$number,$goods){
		$params     = array(
				'order_id'         => $orderId,
				'goods_id'         => $goods['id'],
				'goods_name'       => $goods['name'],
				'goods_type'       => $goods['goods_type'],
				'goods_price'      => $goods['price'],
				'goods_number'     => $number,
				'goods_scores'     => $goods['scores'],
				'is_special'       => $goods['is_special'],
				'real_cost_scores' => $goods['scores'],
				'created_time'     => time(),
		);
		
		return self::add($params);
	}
	private static function _checkData($data=array()) {
		if (!is_array($data)) return false;
		$tmp = array();
		if (isset($data['order_id'])) 			$tmp['order_id'] = $data['order_id'];
		if (isset($data['goods_id'])) 			$tmp['goods_id'] = $data['goods_id'];
		if (isset($data['goods_name'])) 	$tmp['goods_name'] = $data['goods_name'];
		if (isset($data['goods_type'])) 		$tmp['goods_type'] = $data['goods_type'];
		if (isset($data['goods_price']))	 	$tmp['goods_price'] = $data['goods_price'];
		if (isset($data['goods_number'])) $tmp['goods_number'] = $data['goods_number'];
		if (isset($data['goods_scores'])) 	$tmp['goods_scores'] = $data['goods_scores'];
		if (isset($data['real_cost_scores'])) $tmp['real_cost_scores'] = $data['real_cost_scores'];
		if (isset($data['created_time'])) 	$tmp['created_time'] = $data['created_time'];
		return $tmp;
	}

	/**
	 * @return User_Dao_OrderGoods
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_OrderGoods");
	}
}