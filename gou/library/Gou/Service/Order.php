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

class Gou_Service_Order extends Common_Service_Base{
	
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
	public static function search($page = 1, $limit = 4, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
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
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public static function getOrderAddress($order_id) {
		if (!intval($order_id)) return false;
		$address = self::_getAddressDao()->getBy(array('order_id'=>intval($order_id)));
		return Gou_Service_UserAddress::cookUserAddress($address);
	}
	
	/**
	 * @param unknown $order_id
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getOrderAddressSub($order_id, $all = false){
		if (!intval($order_id)) return false;
		$address =  self::_getAddressDao()->getBy(array('order_id'=>intval($order_id)));
		if ($all){
			return $address;
		}
		$addr = explode('|', $address['country']);
		return $addr[1];
	}
	
	/**
	 * 更新订单地址
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateOrderAddress($data, $id){
		if (empty($data)) return false;
		$data = self::_cookAddressData($data);
		return self::_getAddressDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param array $orderids
	 * @return boolean
	 */
	public static function getOrdersAddress($order_ids) {
		if (!is_array($order_ids)) return false;
		return self::_getAddressDao()->getsBy(array('order_id'=>array('IN', $order_ids)));
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
	public static function create($user, $goods, $address, $params) {
		list($orderInfo, $addressInfo) = self::_cookOrderData($user, $goods, $address, $params);
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.");
			//update goods purchase_num
			$ret = Gou_Service_LocalGoods::updatePurchaseNum($orderInfo['goods_id'], $params['number']);
			if (!$ret) throw new Exception("update PurchaseNum failed.", -200);
			
			//update user order_num
			$ret = Gou_Service_User::updateOrderNum($orderInfo['out_uid']);
			if (!$ret) throw new Exception("update Order Number failed.", -200);
			
			//create local order
			$order_id = self::addOrder($orderInfo);
			if (!$order_id) throw new Exception("create local order failed.", -201);
			
			//create gionee pay order
			$webroot = Common::getWebRoot();
			$order_params = array(
					"out_order_no"=>$orderInfo['trade_no'],
					"user_id"=>$orderInfo['out_uid'],
					"subject"=>html_entity_decode($goods['title']),
					"consumed_rewards"=>$orderInfo['silver_coin'],
					"total_fee"=>$orderInfo['real_price'],
					"deal_price"=>$orderInfo['deal_price'],
					"deliver_type"=>$goods['iscash'] == 1 ? '2' : '1',
					"submit_time"=>date('YmdHis', $orderInfo['create_time']),
					"call_back_url"=>$webroot.'/user/account/order_detail?id='.$order_id
			);
			$pay_ret = Api_Gionee_Pay::createOrder($order_params);
			if ($pay_ret['status'] != 200010000) {
				Common::log($pay_ret, 'pay.log');
				throw new Exception("create gionee order failed.", -202);
			}
			
			$ret = self::_getDao()->update(array('out_trade_no'=>$pay_ret['order_no']), $order_id);
			if (!$ret) throw new Exception("update order failed.", -203);
			
			//add order address info
			$addressInfo['order_id'] = $order_id;
			$ret = self::addAddress($addressInfo);
			if (!$ret) throw new Exception("add user address failed.", -203);
			
			//bind read_coin
			//0元购并且是阅读币订单,不需支付需要绑定卡号
			if($orderInfo['real_price'] == 0.00 && $orderInfo['order_type'] == 3 && $orderInfo['status'] == 5) {
				list(, $read_coins) = Gou_Service_ReadCoin::getCanUseReadcoin($orderInfo['goods_id'], $orderInfo['number']);
				$read_coins = Common::resetKey($read_coins, 'id');
				$ids = array_keys($read_coins);
				$update_ret = Gou_Service_ReadCoin::updateByIds(array('order_id'=>$orderInfo['trade_no']), $ids);
				if(!$update_ret) throw new Exception("bind readcoin fail.", -228);
					
				$order['status'] = 5;
					
				Gou_Service_Order::updateOrder(array('status'=>5), $order['id']);
			}
			
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
	 *
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function amigo_order_create($goods, $address, $params) {
		list($orderInfo, $addressInfo) = self::_cookAmigoOrderData($goods, $address, $params);
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.");
			//update goods purchase_num
			//$ret = Gou_Service_LocalGoods::updatePurchaseNum($orderInfo['goods_id'], $params['number']);
			//if (!$ret) throw new Exception("update PurchaseNum failed.", -200);

			//更新库存
			$stock_ret = Gou_Service_LocalGoods::minusStock($orderInfo['goods_id'], $params['number']);
			if (!$stock_ret) throw new Exception("update stock failed.", -200);
		
			//更新销量
			$purchase_ret = Gou_Service_LocalGoods::updatePurchaseNum($orderInfo['goods_id'], $params['number']);
			if (!$purchase_ret) throw new Exception("update PurchaseNum failed.", -290);
			
			//create local order
			$order_id = self::addOrder($orderInfo);			
			if (!$order_id) throw new Exception("create local order failed.", -201);
			
			//add order address info
			$addressInfo['order_id'] = $order_id;
			$address_ret = self::addAddress($addressInfo);
			if (!$address_ret) throw new Exception("add user address failed.", -205);
				
			//create gionee pay order
			$webroot = Common::getWebRoot();
			$order_params = array(
					"out_order_no"=>$orderInfo['trade_no'],
					"subject"=>html_entity_decode($goods['title']),
					"total_fee"=>$orderInfo['real_price'],
					"deal_price"=>$orderInfo['deal_price'],
					"deliver_type"=>$params['pay_type'],
					"submit_time"=>date('YmdHis', $orderInfo['create_time']),
					"call_back_url"=>$webroot.'/amigo/order/result?id='.$order_id.'&pay_mark=1'
			);
			$pay_ret = Api_Gionee_Pay::createOrder($order_params);

			if ($pay_ret['status'] != 200010000) {
				Common::log($pay_ret, 'pay.log');
				throw new Exception("create gionee order failed.", -202);
			}
			
			$ret = self::_getDao()->update(array('out_trade_no'=>$pay_ret['token_id']), $order_id);
			if (!$ret) throw new Exception("update order failed.", -203);
				
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -204);
			return array($order_id, $pay_ret['token_id']);
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
			return false;
		}
	}
	
	/**
	 *
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function recharge_order_create($params) {
		$price = Common::money($params['price']);
		$orderInfo = array(
				'supplier' => '欧飞',
				'goods_title' => $params['goods_title'],
				'order_type' => 4,
				'number' => 1,
				'deal_price' => $price,
				'real_price' => $price,
				'pay_time' => 0,
				'pay_type' => $params['pay_type'],
				'trade_no' => self::getTradeNo(),
				'rec_cardnum' => $params['rec_cardnum'],
				'create_time'=>Common::getTime(),
				'phone' => $params['mobile'],
				'status'=> 1,
				'rec_status'=>4,
				'show_type'=>2,
				'channel_id'=>$params['channel_id'],
		);
		
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.");
			
			//create local order
			$order_id = self::addOrder($orderInfo);
			if (!$order_id) throw new Exception("create local order failed.", -201);
				
			//create gionee pay order
			$webroot = Common::getWebRoot();
			$order_data = array(
					"out_order_no"=>$orderInfo['trade_no'],
					"subject"=>html_entity_decode($params['goods_title'] .' - '.$params['mobile']),
					"total_fee"=>$orderInfo['real_price'],
					"deal_price"=>$orderInfo['deal_price'],
					"deliver_type"=>$params['pay_type'],
					"submit_time"=>date('YmdHis', $orderInfo['create_time']),
					"call_back_url"=>$webroot.'/recharge/ret?id='.$order_id.'&pay_mark=1'
			);
			$pay_ret = Api_Gionee_Pay::createOrder($order_data);
	
			if ($pay_ret['status'] != 200010000) {
				Common::log($pay_ret, 'pay.log');
				throw new Exception("create gionee order failed.", -202);
			}
				
			$ret = self::_getDao()->update(array('out_trade_no'=>$pay_ret['token_id']), $order_id);
			if (!$ret) throw new Exception("update order failed.", -203);
	
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -204);
			return array($order_id, $pay_ret['token_id']);
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array('recharge_order', $e->getCode(), $e->getMessage()), self::$logFile);
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
	 * 增加收货地址
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateAddressByOrderId($data, $orderId) {
	    if(!is_array($data) || !$orderId) return false;
	    return self::_getAddressDao()->updateBy($data, array('order_id'=>$orderId));
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 */
	public static function addOrder($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();		
	}
	
	/**
	 * getOrderPayStatus
	 * @param string $data
	 */
	public static function getOrderPayStatus($order_no) {
		$order = self::getByTradeNo($order_no);
		if (!$order) {
			Common::log('Error: ' . $order_no, 'notify.log');
			return false;
		}
		
		$result = Api_Gionee_Pay::getOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
		if ($result['process_status'] == 3 && $order['status'] == 1) {
			if($order['order_type'] == 3) {
				$status = 5;
			} else {
				$status = 2;
			}
			
			$update_data = array(
					'status'=>$status,
					'pay_time'=>strtotime($result['close_time']),
					'status'=>$status,
					'gionee_order_no'=>$result['order_no'],
					'pay_channel'=>$result['channel'],
					'pay_channel_billno'=>$result['channel_billno']
			);
			
			$ret = Gou_Service_Order::updateByOutTradeNo($update_data, $order["out_trade_no"]);
			if (!$ret) {
				Common::log('update order status : ' . $order_no, 'notify.log');
				return false;
			}
			//Common::log('order_id : ' . $order_no.'----process_status:'.$result['process_status'].'----ret:'.$ret, 'test.log');
			//阅读币订单
			if($order['order_type'] == 3) {
				list(, $read_coins) = Gou_Service_ReadCoin::getCanUseReadcoin($order['goods_id'], $order['number']);
				$read_coins = Common::resetKey($read_coins, 'id');
				$ids = array_keys($read_coins);
				$update_ret = Gou_Service_ReadCoin::updateByIds(array('order_id'=>$order['trade_no']), $ids);
				if(!$update_ret) Common::log('Error: bind readcoin fail :' . $order_no, 'notify.log');
			}
			
			//话费充值
			if($order['order_type'] == 4) {
				Api_Ofpay_Recharge::recharge($order['trade_no']);
			}
			
			$log = array(
			        'order_id'=>$order['id'],
			        'order_type'=>1,
			        'uid'=>0,
			        'create_time'=>time(),
			        'update_data'=>json_encode(array('status' => $status))
			);
			Gou_Service_Order::addOrderLog($log);
			
			return true;
		}
		return true;
	}
	
	/**
	 * 获取订单操作日志
	 * @param integer $order_id
	 */
	public static function getOrderLog($order_id, $order_type){
		if (empty($order_id)) return false;
		return self::_getLogDao()->getsBy(array('order_id'=>$order_id, 'order_type'=>$order_type), 
				array('create_time'=>'ASC'));
	}
	
	/**
	 * 添加订单操作日志
	 * @param array $data
	 */
	public static function addOrderLog($data){
		if (empty($data)) return false;
		$data = self::_cookLogData($data);
		return self::_getLogDao()->insert($data);
	}
	
	/**
	 * 格式化操作日志
	 * @param json $data
	 */
	public static function formatLog($data){
		$update_data_array = json_decode($data, true);
		$log = '';
		if (!empty($update_data_array['status'])){
			$log .= '订单状态：' . self::orderStatus($update_data_array['status']);
		}
		
		if (!empty($update_data_array['refund_status'])){
		    $log .= '退款状态：' . Gou_Service_OrderRefund::refundStatus($update_data_array['refund_status']);
		}
		
		if (!empty($update_data_array['express_code'])){
			$log .= '快递单号：' . $update_data_array['express_code'];
		}
		
		if (!empty($update_data_array['number'])){
			$log .= '购买数量：' . $update_data_array['number'];
		}
		
		if (!empty($update_data_array['buyer_name'])){
			$log .= '收货人：' . $update_data_array['buyer_name'];
		}
		
		if (!empty($update_data_array['postcode'])){
			$log .= '邮编：' . $update_data_array['postcode'];
		}
		
		if (!empty($update_data_array['mobile'])){
			$log .= '电话号码：' . $update_data_array['mobile'];
		}
		
		if (!empty($update_data_array['city']) || 
			!empty($update_data_array['province']) || 
			!empty($update_data_array['country']) || 
			!empty($update_data_array['detail_address'])){
			$log .= '地址：' . $update_data_array['province'] . 
				$update_data_array['city'] . 
				$update_data_array['country'] . 
				$update_data_array['detail_address'];
		}
		return $log;
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
		$real_price = Common::money($price - $silver_coin);
		
		$status = 1;
		if($real_price == 0) $status = 2;
		if($real_price == 0 && $goods['goods_type'] == 3) $status = 5;
		
		return array(array(
				'uid' => $user['id'],
				'out_uid'=>$user['out_uid'],
				'username' => $user['username'],
				'buyer_name' => $address['buyer_name'],
				'supplier' => $goods['supplier'],
				'goods_id' => $goods['id'],
				'order_type' => $goods['goods_type'],
				'number' => $params['number'],
				'deal_price' => $price,
				'real_price' => $real_price,
				'silver_coin' => $silver_coin,
				'pay_time' => ($real_price == 0) ? Common::getTime() : 0,
				'trade_no' => self::getTradeNo(),
				'iscash' => $goods['iscash'],
				'create_time'=>Common::getTime(),
				'phone' => $params['phone'],
				'gbook' => $params['gbook'],
				'status'=> $status,
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
	
	private static function _cookAddressData($data) {
		$tmp = array();
		if(isset($data['buyer_name'])) $tmp['buyer_name'] = $data['buyer_name'];
		if(isset($data['province'])) $tmp['province'] = $data['province'];
		if(isset($data['city'])) $tmp['city'] = $data['city'];
		if(isset($data['country'])) $tmp['country'] = $data['country'];
		if(isset($data['detail_address'])) $tmp['detail_address'] = $data['detail_address'];
		if(isset($data['postcode'])) $tmp['postcode'] = $data['postcode'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		return $tmp;
	}
	
	/**
	 *
	 * @param array $user
	 * @param array $goods
	 * @param array $address
	 * @return array
	 */
	private static function _cookAmigoOrderData($goods, $address, $params) {
		$price = Common::money($goods['price'] * $params['number']);
		return array(array(
				'buyer_name' => $address['buyer_name'],
				'supplier' => $goods['supplier'],
				'goods_id' => $goods['id'],
				'order_type' => $goods['goods_type'],
				'number' => $params['number'],
				'deal_price' => $price,
				'real_price' => $price,
				'pay_time' => 0,
				'pay_type' => $params['pay_type'],
				'trade_no' => self::getTradeNo(),
				'iscash' => $goods['iscash'],
				'create_time'=>Common::getTime(),
				'phone' => $address['mobile'],
				'gbook' => $params['gbook'],
				'status'=> 1,
		),
				array(
						'buyer_name' => $address['buyer_name'],
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
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['supplier'])) $tmp['supplier'] = intval($data['supplier']);
		if(isset($data['number'])) $tmp['number'] = intval($data['number']);
		if(isset($data['trade_no'])) $tmp['trade_no'] = $data['trade_no'];
		if(isset($data['out_trade_no'])) $tmp['out_trade_no'] = $data['out_trade_no'];
		if(isset($data['deal_price'])) $tmp['deal_price'] = $data['deal_price'];
		if(isset($data['real_price'])) $tmp['real_price'] = $data['real_price'];
		if(isset($data['gold_coin'])) $tmp['gold_coin'] = Common::money($data['gold_coin']);
		if(isset($data['silver_coin'])) $tmp['silver_coin'] = Common::money($data['silver_coin']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['pay_time'])) $tmp['pay_time'] = intval($data['pay_time']);
		if(isset($data['take_time'])) $tmp['take_time'] = $data['take_time'];
		if(isset($data['pay_msg'])) $tmp['pay_msg'] = $data['pay_msg'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['iscash'])) $tmp['iscash'] = $data['iscash'];
		if(isset($data['express_code'])) $tmp['express_code'] = $data['express_code'];
		if(isset($data['gbook'])) $tmp['gbook'] = $data['gbook'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['order_type'])) $tmp['order_type'] = $data['order_type'];
		if(isset($data['show_type'])) $tmp['show_type'] = $data['show_type'];
		if(isset($data['pay_type'])) $tmp['pay_type'] = $data['pay_type'];
		
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['remark'])) $tmp['remark'] = strval($data['remark']);
		if(isset($data['refund_reason'])) $tmp['refund_reason'] = strval($data['refund_reason']);
		
		if(isset($data['rec_status'])) $tmp['rec_status'] = $data['rec_status'];
		if(isset($data['rec_cardnum'])) $tmp['rec_cardnum'] = $data['rec_cardnum'];
		if(isset($data['rec_order_id'])) $tmp['rec_order_id'] = $data['rec_order_id'];
		if(isset($data['rec_msg'])) $tmp['rec_msg'] = $data['rec_msg'];
		if(isset($data['rec_order_time'])) $tmp['rec_order_time'] = $data['rec_order_time'];
		if(isset($data['rec_price'])) $tmp['rec_price'] = $data['rec_price'];
		if(isset($data['goods_title'])) $tmp['goods_title'] = $data['goods_title'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		
		if(isset($data['gionee_order_no'])) $tmp['gionee_order_no'] = $data['gionee_order_no'];
		if(isset($data['pay_channel'])) $tmp['pay_channel'] = $data['pay_channel'];
		if(isset($data['pay_channel_billno'])) $tmp['pay_channel_billno'] = $data['pay_channel_billno'];
		return $tmp;
	}
	
	
	private static function _cookLogData($data) {
		$tmp = array();
		if(isset($data['uid'])) $tmp['uid'] = intval($data['uid']);
		if(isset($data['order_id'])) $tmp['order_id'] = intval($data['order_id']);
		if(isset($data['order_type'])) $tmp['order_type'] = intval($data['order_type']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_data'])) $tmp['update_data'] = strval($data['update_data']);
		return $tmp;
	}
	
	public static function orderStatus($status=0) {
		$orderStatus = array(
			1 => '已下单/未支付',
			2 => '已确认/已支付',
			3 => '已确认/已支付，未发货',
			4 => '已发货',
			5 => '订单成功',
			6 => '订单关闭',
			7 => '申请退/换货',
			8 => '拒收',
			9 => '测试',
		   10 => '申请退款',
		   11 => '取消退款',           
		   12 => '已退款',
		   13 => '退款失败',
		);
		return $status ? $orderStatus[intval($status)] : $orderStatus;
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
	 * @return Gou_Dao_Order
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Order");
	}
	
	/**
	 * 
	 * @return Gou_Dao_OrderAddress
	 */
	private static function _getAddressDao() {
		return Common::getDao("Gou_Dao_OrderAddress");
	}
	
	/**
	 * @return Common_Service_Pdo
	 */
	private static function getPdoService() {
		return Common::getService('Common_Service_Pdo');
	}
	
	/**
	 * 
	 * @return Gou_Dao_Orderlog
	 */
	private static function _getLogDao() {
		return Common::getDao("Gou_Dao_Orderlog");
	}
}
