<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
define('GOLD_COIN', 1);
define('SILVER_COIN', 2);

class Gc_Service_Order extends Common_Service_Base{
	
	public static $logFile = "order.log";
	/**
	 * 
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('create_time'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getOrderList($page = 1, $limit = 4, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getOrderList($start, $limit, $params);
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
	 * @param int $id
	 * @return boolean
	 */
	public static function getOrderAddress($order_id) {
		if (!intval($order_id)) return false;
		return self::_getAddressDao()->getBy(array('order_id'=>intval($order_id)));
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
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function create($user, $goods, $address, $params) {
		list($orderInfo, $addressInfo) = self::_cookOrderData($user, $goods, $address, $params);

		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.");
			//update goods purchase_num
			$ret = Gc_Service_LocalGoods::updatePurchaseNum($orderInfo['goods_id'], $params['number']);
			if (!$ret) throw new Exception("update PurchaseNum failed.", -200);
			
			//update user order_num
			$ret = Gc_Service_User::updateOrderNum($orderInfo['out_uid']);
			if (!$ret) throw new Exception("update Order Number failed.", -200);
			
			//create local order
			$order_id = self::addOrder($orderInfo);
			if (!$order_id) throw new Exception("create local order failed.", -201);
			
			//create gionee pay order
			$order_params = array(
					"out_order_no"=>$orderInfo['trade_no'],
					"user_id"=>$orderInfo['out_uid'],
					"subject"=>$goods['title'],
					"consumed_rewards"=>$orderInfo['silver_coin'],
					"total_fee"=>$orderInfo['real_price'],
					"deal_price"=>$orderInfo['deal_price'],
					'deliver_type'=>$goods['iscash'] == 1 ? '2' : '1'
			);
			$pay_ret = Api_Gionee_Pay::createOrder($order_params);
			if ($pay_ret['status'] != 200) {
				Common::log($pay_ret, 'pay.log');
				throw new Exception("create gionee order failed.", -202);
			}
			
			$ret = self::_getDao()->update(array('out_trade_no'=>$pay_ret['order_no']), $order_id);
			if (!$ret) throw new Exception("update order failed.", -203);
			
			//add order address info
			$addressInfo['order_id'] = $order_id;
			$ret = self::addAddress($addressInfo);
			if (!$ret) throw new Exception("add user address failed.", -203);
			
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -204);
			return array($order_id, $pay_ret['order_no']);
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($user['id'], $e->getCode(), $e->getMessage()), self::$logFile);
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
	 * 增加收货地址
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addAddress($data) {
		if (!$data || !is_array($data)) return false;
		return self::_getAddressDao()->insert($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 */
	public static function addOrder($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['status'] = 1;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();		
	}
	

	/**
	 *
	 * @param array $user
	 * @param array $goods
	 * @param array $address
	 * @return array
	 */
	private static function _cookOrderData($user, $goods, $address, $params) {
		$silver_coin = Common::money($params['silver_coin']);
		$gold_coin = Common::money($params['gold_coin']);
		$price = Common::money($goods['price'] * $params['number']);
		
		return array(array(
				'uid' => $user['id'],
				'out_uid'=>$user['out_uid'],
				'username' => $user['username'],
				'buyer_name' => $address['buyer_name'],
				'supplier' => $goods['supplier'],
				'goods_id' => $goods['id'],
				'number' => $params['number'],
				'deal_price' => $price,
				'real_price' => Common::money($price - $silver_coin),
				'silver_coin' => $silver_coin,
				'trade_no' => self::getTradeNo(),
				'iscash' => $goods['iscash']
		),
		array(
				'uid' => $user['id'],
				'buyer_name' => $address['realname'],
				'province' => $address['province'],
				'city' => $address['city'],
				'country' => $address['country'],
				'detail_address' => $address['detail_address'],
				'postcode' => $address['postcode'],
				'mobile' => $address['mobile'],
				'phone' => $address['phone']
		));
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
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['buyer_name'])) $tmp['buyer_name'] = $data['buyer_name'];
		if(isset($data['goods_id'])) $tmp['goods_id'] = intval($data['goods_id']);
		if(isset($data['supplier'])) $tmp['supplier'] = intval($data['supplier']);
		if(isset($data['number'])) $tmp['number'] = intval($data['number']);
		if(isset($data['trade_no'])) $tmp['trade_no'] = $data['trade_no'];
		if(isset($data['out_trade_no'])) $tmp['out_trade_no'] = $data['out_trade_no'];
		if(isset($data['deal_price'])) $tmp['deal_price'] = $data['deal_price'];
		if(isset($data['real_price'])) $tmp['real_price'] = $data['real_price'];
		if(isset($data['gold_coin'])) $tmp['gold_coin'] = intval($data['gold_coin']);
		if(isset($data['silver_coin'])) $tmp['silver_coin'] = intval($data['silver_coin']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['pay_time'])) $tmp['pay_time'] = intval($data['pay_time']);
		if(isset($data['take_time'])) $tmp['take_time'] = $data['take_time'];
		if(isset($data['pay_msg'])) $tmp['pay_msg'] = $data['pay_msg'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['iscash'])) $tmp['iscash'] = $data['iscash'];
		return $tmp;
	}
	
	public static function orderStatus($status) {
		$orderStatus = array(
			1 => '未支付',
			2 => '已支付',
			3 => '卖家未发货',
			4 => '买家未收货',
			5 => '订单成功',
			6 => '订单关闭',
		);
		return $orderStatus[intval($status)];
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
	 * @return Gc_Dao_Order
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_Order");
	}
	
	/**
	 * 
	 * @return Gc_Dao_OrderAddress
	 */
	private static function _getAddressDao() {
		return Common::getDao("Gc_Dao_OrderAddress");
	}
	
	/**
	 * @return Common_Service_Pdo
	 */
	private static function getPdoService() {
		return Common::getService('Common_Service_Pdo');
	}
}
