<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */

class Fj_Service_Order extends Common_Service_Base{
	
	public static $logFile = "order.log";


	/**
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $sort
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	public static function getAddressByOrderIds($order_ids) {
		if (!is_array($order_ids)) return false;
		return self::_getAddressDao()->getAddressByOrderIds($order_ids);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $id
	 */
	public static function getOrder($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $out_trade_no
	 */
	public static function getByOutTradeNo($out_trade_no) {
		if (!$out_trade_no) return false;
		return self::_getDao()->getBy(array('out_trade_no'=>$out_trade_no));
	}
	
	/**
	 * 
	 * @param unknown_type $trade_no
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getByTradeNo($trade_no) {
		if (!$trade_no) return false;
		return self::_getDao()->getBy(array('trade_no'=>$trade_no));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, array('id'=>'DESC'));
	}

	/**
	 * get by
	 */
	public static function get($id) {
		if(!$id)return false;
		return self::_getDao()->getBy(array('id'=>$id), array('id'=>'DESC'));
	}

	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort=array()) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 */
	public static function updateOrder($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $out_trade_no
	 */
	public static function updateByOutTradeNo($data, $out_trade_no) {
		if(!is_array($data) || !$out_trade_no) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateByOutTradeNo($data, $out_trade_no);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $id
	 */
	public static function deleteOrder($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param int $uid
	 */
	public static function getOrderCountByUid($uid) {
		return self::_getDao()->count(array('uid'=>$uid));
	}
	
	/**
	 * 
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function create($params) {
	    //购物车商品
	    list(, $carts) = Fj_Service_Cart::getsBy(array('id'=>array('IN', $params['cart_ids'])), array('id'=>'DESC'));
	    $cart_list = Common::resetKey($carts, 'goods_id');
	    
		list($orderInfo, $orderDetail) = self::_cookOrderData($params, $cart_list);
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.");
			
			//更新库存和购买数量
			foreach ($cart_list as $key=>$value) {
			    /* $qret = Fj_Service_Goods::updateQuantity($value['goods_num'], $value['goods_id']);
			    if (!$qret) throw new Exception("update Quantity failed.", -220); */
			    
			    $sret = Fj_Service_Goods::updateSaleNum($value['goods_num'], $value['goods_id']);
			    if (!$sret) throw new Exception("update salenum failed.", -223);
			}
			
			//create order
			$order_id = Fj_Service_Order::addOrder($orderInfo);
			if (!$order_id) throw new Exception("create order failed.", -201);
			
			//create order_detail
			$detail_ret = Fj_Service_Order::batchAddOrderDetail($orderDetail);
			if (!$detail_ret) throw new Exception("create order detail failed.", -201);
			
			//清空购物车
			$del_ret = Fj_Service_Cart::deleteBy(array('id'=>array('IN', $params['cart_ids'])));
			if (!$del_ret) throw new Exception("del cart failed.", -205);
			
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -204);
			return $order_id;
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($params['uid'], $e->getCode(), $e->getMessage()), self::$logFile);
			return false;
		}
	}
	
	
	/**
	 * 获取用户订单数量
	 * @param int $uid
	 */
	public static function userOrderCount($uid, $goods_id) {
		return self::_getDao()->userOrderCount(array('uid'=>intval($uid), 'goods_id'=>intval($goods_id)));
	}
	
	/**
	 * 增加订单详情
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addOrderDetail($data) {
		if (!$data || !is_array($data)) return false;
		return self::_getDetailDao()->insert($data);
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAddOrderDetail($data) {
	    if (!is_array($data)) return false;
	    self::_getDetailDao()->mutiInsert($data);
	    return true;
	}
	
    /**
	 * 
	 * @param unknown_type $trade_no
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getsDetailByTradeNo($trade_no) {
		if (!$trade_no) return false;
		return self::_getDetailDao()->getsBy(array('trade_no'=>$trade_no), array('id'=>'DESC'));
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 */
	public static function addOrder($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['get_token'] = self::getOrderToken();
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}
	
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	private static function _cookOrderData($params, $cart_list) {
	    //计算商品价格
	    list(, $price) = Fj_Service_Cart::getCartInfo($params['cart_ids']);
	    $trade_no = self::getTradeNo();
		
		$order_info = array(
				'uid' => $params['uid'],
				'out_uid'=>$params['open_id'],
				'buyer_name' => $params['buyer_name'],
				'trade_no' => $trade_no,
				'create_time'=>Common::getTime(),
				'phone' => $params['phone'],
				'status'=> 1,
        		'address_id'=> $params['address_id'],
        		'real_price'=> $price,
        		'get_date'=> $params['date'],
		       'get_time_id'=>$params['time_id'],
		);
		
		
		$order_detail = array();
		foreach ($cart_list as $key=>$value) {
		    $order_detail[] = array(
		        'id' => '',
    		    'uid' => $params['uid'],
    		    'goods_id' => $value['goods_id'],
    		    'trade_no' => $trade_no,
    		    'goods_num' => $value['goods_num'],
    		    'deal_price' => $value['price'],
				'descrip' => $value['descrip'],
		    );
		}
		
		return array($order_info, $order_detail);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['uid'])) $tmp['uid'] = intval($data['uid']);
		if(isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if(isset($data['address_id'])) $tmp['address_id'] = $data['address_id'];
		if(isset($data['buyer_name'])) $tmp['buyer_name'] = $data['buyer_name'];
		if(isset($data['trade_no'])) $tmp['trade_no'] = $data['trade_no'];
		if(isset($data['out_trade_no'])) $tmp['out_trade_no'] = $data['out_trade_no'];
		if(isset($data['deal_price'])) $tmp['deal_price'] = $data['deal_price'];
		if(isset($data['real_price'])) $tmp['real_price'] = $data['real_price'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if(isset($data['pay_time'])) $tmp['pay_time'] = intval($data['pay_time']);
		if(isset($data['address_id'])) $tmp['address_id'] = intval($data['address_id']);
		if(isset($data['take_time'])) $tmp['take_time'] = $data['take_time'];
		if(isset($data['get_date'])) $tmp['get_date'] = $data['get_date'];
		if(isset($data['get_token'])) $tmp['get_token'] = $data['get_token'];
		if(isset($data['pay_msg'])) $tmp['pay_msg'] = $data['pay_msg'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['get_date'])) $tmp['get_date'] = $data['get_date'];
		if(isset($data['get_time'])) $tmp['get_time'] = $data['get_time'];
		if(isset($data['get_time_id'])) $tmp['get_time_id'] = $data['get_time_id'];
		return $tmp;
	}
	
	public static function orderStatus($status=0) {
		$orderStatus = array(
			1 => '待付款',
			2 => '已付款',
			3 => '已提货',
			4 => '已取消',
		);
		return $status ? $orderStatus[intval($status)] : $orderStatus;
	}
	
	
	/**
	 * 提货时间段
	 */
	public function getTime() {
	    return array(
            1=>'06:00 - 08:00',
            2=>'08:00 - 10:00',
            3=>'10:00 - 12:00',
            4=>'12:00 - 14:00',
            5=>'14:00 - 16:00',
            6=>'16:00 - 18:00',
            7=>'18:00 - 20:00'
	    );
	}
	
	
	//get token 随机数
	private function getOrderToken() {
	    $rand = mt_rand('100000', '999999');
	    $info = self::getBy(array('get_token'=>$rand));
	    if($info) $this->getOrderToken();
	    
	    return $rand;
	}
	
	/**
	 * create trade_no 
	 * @return string
	 */
	public static function getTradeNo() {
		list($usec, $sec) = explode(" ", microtime());
		$usec = substr(str_replace('0.', '', $usec), 0, 4);
		$str = rand(10, 99);
		return date("YmdHis") . $usec . $str;
	}
	
	/**
	 * 
	 * @return Fj_Dao_Order
	 */
	private static function _getDao() {
		return Common::getDao("Fj_Dao_Order");
	}
	
	/**
	 *
	 * @return Fj_Dao_OrderDetail
	 */
	private static function _getDetailDao() {
	    return Common::getDao("Fj_Dao_OrderDetail");
	}
	
	/**
	 * @return Common_Service_Pdo
	 */
	private static function getPdoService() {
		return Common::getService('Common_Service_Pdo');
	}

}
