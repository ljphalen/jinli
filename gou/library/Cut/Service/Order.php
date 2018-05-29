<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
define('GOLD_COIN', 1);
define('SILVER_COIN', 2);

class Cut_Service_Order extends Common_Service_Base{
	
	public static $logFile = "order.log";


    /**
     * @param $goods
     * @param $address
     * @param $params
     * @return array|bool
     */
    public static function create_winprize_order($goods, $address, $params){
        list($orderInfo, $addressInfo) = self::_cookWinPrizeOrder($goods, $address, $params);
//        try {
            $trans = parent::beginTransaction();
            if (!$trans) throw new Exception("begin transaction failed.");


            //更新商品状态
            $goods_ret = Cut_Service_Goods::updateGoods(array('status'=>4), $orderInfo['goods_id']);
            if (!$goods_ret) throw new Exception("update goods status failed.", -290);

            //create local order
            $order_id = Gou_Service_Order::addOrder($orderInfo);
            if (!$order_id) throw new Exception("create local order failed.", -201);

            //add order address info
            $addressInfo['order_id'] = $order_id;
            $address_ret = Gou_Service_Order::addAddress($addressInfo);
            if (!$address_ret) throw new Exception("add user address failed.", -205);

            //create gionee pay order
            $ret = parent::commit();
            if (!$ret) throw new Exception("transactoin commit failed.", -204);
            return true;
//        } catch(Exception $e) {
//            parent::rollBack();
//            Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
//            return false;
//        }
    }
	
	/**
	 *
	 *抢购订单
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function cut_order_create($goods, $address, $params) {
	    list($orderInfo, $addressInfo) = self::_cookCutOrderData($goods, $address, $params);
	    try {
	        $trans = parent::beginTransaction();
	        if (!$trans) throw new Exception("begin transaction failed.");
	        	
	        //更新库存
	        $qret = Cut_Service_Goods::updateQuantity(1, $orderInfo['goods_id']);
	        if (!$qret) throw new Exception("update Quantity failed.", -220);
	        
	        //update sale num
	        $sale_ret = Cut_Service_Goods::updateSaleNum($orderInfo['goods_id']);
	        if (!$sale_ret) throw new Exception("update sale num failed.", -224);
	        
	        //更新商品状态
	        $goods_ret = Cut_Service_Goods::updateGoods(array('status'=>3), $orderInfo['goods_id']);
	        if (!$goods_ret) throw new Exception("update goods status failed.", -290);
	        	
	        //create local order
	        $order_id = Gou_Service_Order::addOrder($orderInfo);
	        if (!$order_id) throw new Exception("create local order failed.", -201);
	        	
	        //add order address info
	        $addressInfo['order_id'] = $order_id;
	        $address_ret = Gou_Service_Order::addAddress($addressInfo);
	        if (!$address_ret) throw new Exception("add user address failed.", -205);
	
	        //create gionee pay order
	        $webroot = Common::getWebRoot();
	        $order_params = array(
    	        "out_order_no"=>$orderInfo['trade_no'],
    	        "subject"=>Util_String::substr(html_entity_decode($goods['title']), 0, 10,'', true),
    	        "total_fee"=>$orderInfo['real_price'],
    	        "deal_price"=>$orderInfo['real_price'],
    	        "deliver_type"=>$orderInfo['pay_type'],
    	        "submit_time"=>date('YmdHis', $orderInfo['create_time']),
    	        "call_back_url"=>$webroot.'/cutorder/list?pay_mark=1'
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
	 *抢购订单
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function create($goods, $address, $params) {
	    list($orderInfo, $addressInfo) = self::_cookCutOrderData($goods, $address, $params);
	    try {
	        $trans = parent::beginTransaction();
	        if (!$trans) throw new Exception("begin transaction failed.");
	
	        //更新库存
	        $qret = Cut_Service_Goods::updateQuantity(1, $orderInfo['goods_id']);
	        if (!$qret) throw new Exception("update Quantity failed.", -220);
	         
	        //update sale num
	        $sale_ret = Cut_Service_Goods::updateSaleNum($orderInfo['goods_id']);
	        if (!$sale_ret) throw new Exception("update sale num failed.", -224);
	         
	        //更新商品状态
	        $goods_ret = Cut_Service_Goods::updateGoods(array('status'=>3), $orderInfo['goods_id']);
	        if (!$goods_ret) throw new Exception("update goods status failed.", -290);
	
	        //create local order
	        $order_id = Gou_Service_Order::addOrder($orderInfo);
	        if (!$order_id) throw new Exception("create local order failed.", -201);
	
	        //add order address info
	        $addressInfo['order_id'] = $order_id;
	        $address_ret = Gou_Service_Order::addAddress($addressInfo);
	        if (!$address_ret) throw new Exception("add user address failed.", -205);
	
	        //create gionee pay order
	        $webroot = Common::getWebRoot();
	        $order_params = array(
    	        "trade_no"=>$orderInfo['trade_no'],
    	        "subject"=>Util_String::substr(html_entity_decode($goods['title']), 0, 20,'', true),
    	        "total_fee"=>$orderInfo['real_price'],
    	        "call_back_url"=>$webroot.'/cutorder/callback',
    	        "notify_url"=>$webroot.'/alipaynotify/index',
    	        "merchant_url"=>$webroot.'/cutorder/list',
    	        "pay_expire"=>'15'
	        );
	        $api = new Api_Alipay_Pay();
	        $request_token = $api->getRequestToken($order_params);
	
	        if (!$request_token) {
	            throw new Exception("create alipay order failed.", -202);
	        }
	
	        $ret = self::_getDao()->update(array('token'=>$request_token), $order_id);
	        if (!$ret) throw new Exception("update order failed.", -203);
	
	        $ret = parent::commit();
	        if (!$ret) throw new Exception("transactoin commit failed.", -204);
	        return array($order_id, $request_token);
	    } catch(Exception $e) {
	        parent::rollBack();
	        Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
	        return false;
	    }
	}
	
	
	/**
	 * getOrderPayStatus
	 * @param string $data
	 */
	public static function getOrderPayStatus($order_no) {
		$order = Gou_Service_Order::getByTradeNo($order_no);
		if (!$order) {
			Common::log('Error: ' . $order_no, 'notify.log');
			return false;
		}
		
		$result = Api_Gionee_Pay::getOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
		if ($result['process_status'] == 3) {
			
		    //更新订单状态
			$update_data = array(
					'status'=>2,
					'pay_time'=>strtotime($result['close_time']),
					'gionee_order_no'=>$result['order_no'],
					'pay_channel'=>$result['channel'],
					'pay_channel_billno'=>$result['channel_billno']
			);
			
			$ret = Gou_Service_Order::updateByOutTradeNo($update_data, $order["out_trade_no"]);
			if (!$ret) {
				Common::log('update order status : ' . $order_no, 'notify.log');
				return false;
			}
			
			//锁定商品
		    $goods_ret = Cut_Service_Goods::updateGoods(array('status'=>4), $order['goods_id']);
		    if(!$goods_ret) Common::log('Error: update goods status fail :' . $order_no, 'notify.log');
			
			$log = array(
			        'order_id'=>$order['id'],
			        'order_type'=>1,
			        'uid'=>0,
			        'create_time'=>time(),
			        'update_data'=>json_encode(array('status' => 2))
			);
			Gou_Service_Order::addOrderLog($log);
			
			return true;
		}
		return true;
	}
	
	
	/**
	 * 确认支付订单，处理业务
	 * @param array $notify
	 */
	public static function payOrder($order, $notify) {
	    if(!is_array($order) || !is_array($notify)) return false;
	    try {
	        $trans = parent::beginTransaction();
	        if (!$trans) throw new Exception("begin transaction failed.");
    	    //更新订单状态
    	    $update_data = array(
        	    'status'=>2,
        	    'pay_time'=>strtotime($notify['gmt_payment']),
        	    'out_trade_no'=>$notify['trade_no'],
    	    );
    	    $up_ret = Gou_Service_Order::updateOrder($update_data, $order['id']);
    	    if (!$up_ret) throw new Exception("updateOrder failed.", -304);
    	     
    	    //更新商品状态
    	    $goods_ret = Cut_Service_Goods::updateGoods(array('status'=>4), $order['goods_id']);
    	    if (!$goods_ret) throw new Exception("update goods failed.", -305);
    	     
    	    //log
    	    $log = array(
        	    'order_id'=>$order['id'],
        	    'order_type'=>1,
        	    'uid'=>0,
        	    'create_time'=>time(),
        	    'update_data'=>json_encode(array('status' => 2))
    	    );
    	    $log_ret = Gou_Service_Order::addOrderLog($log);
    	    if (!$log_ret) throw new Exception("add log failed.", -305);
    	    
    	    $ret = parent::commit();
    	    if (!$ret) throw new Exception("transactoin commit failed.", -204);
	        return true;
	    } catch(Exception $e) {
	        parent::rollBack();
	        Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
	        return false;
	    }
	}
	

	/**
	 * 砍价订单取消 
	 * @param string $data
	 */
	public static function cancelCutOrder($order_no) {
	    $order = Gou_Service_Order::getByTradeNo($order_no);
	    if (!$order)  return false;
	    
	    $result = Api_Gionee_Pay::getOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
	    if($result['process_status'] == 3) {
	        //已支付
	        Cut_Service_Order::getOrderPayStatus($order['trade_no']);
	        return false;
	    } else {
	        $ret =  Api_Gionee_Pay::cancelOrder(array('trade_no'=>$order['trade_no'],'create_time'=>$order['create_time']));
	        //取消成功
	        if($ret['status'] == 200) {
	            $goods = Cut_Service_Goods::getGoods($order['goods_id']);
	            
	            //更新商品库存
	            //if($goods['sale_num'] < 5) {
            		//更新商品状态
            		// Cut_Service_Goods::updateGoods(array('status'=>1), $goods['id']);
            		Cut_Service_Goods::updateGoods(array('quantity'=>1, 'status'=>1), $goods['id']);
                //} else {
	            	//	Cut_Service_Goods::updateGoods(array('status'=>5), $goods['id']);
	            	//}
	            //更新订单状态
	            Gou_Service_Order::updateOrder(array('status'=>6), $order['id']);
	            return true;
	        }
	        return false;
	    }
	}
	
	/**
	 * 砍价订单取消
	 * @param string $data
	 */
	public static function cancelOrder($order_no) {
	    $order = Gou_Service_Order::getByTradeNo($order_no);
	    if (!$order)  return false;
	    
	    //查询支付宝状态
	    $can_close = false;
	    $api = new Api_Alipay_Query();
	    $query_ret = $api->queryOrder($order['trade_no']);
	    if($query_ret['trade_status'] == 'TRADE_CLOSED') {
	        $can_close = true;
	    }
	    
	    //如果支付宝未取消，需调用接口取消 
	    if($can_close == false) {
    	    $api = new Api_Alipay_Close();
    	    $result = $api->closeOrder($order['trade_no']);
    	    if($result) $can_close = true;
	    }
	    
	    if($can_close) {
	        //更新商品状态和库存
	        $goods = Cut_Service_Goods::getGoods($order['goods_id']);
	        //if($goods['sale_num'] < 5) {
	            $update_data = array('quantity'=>1, 'status'=>1);
	        //} else {
	            //$update_data = array('status'=>5);
	        //}
	        Cut_Service_Goods::updateGoods($update_data, $goods['id']);
	        //更新订单状态
	        Gou_Service_Order::updateOrder(array('status'=>6), $order['id']);
	        return true;
	    } else {
	        return false;
	    }
	}
	
	
	/**
	 * 订单退款
	 * @param string $data
	 */
	public static function refundOrder($order_no) {
	    $order = Gou_Service_Order::getByTradeNo($order_no);
	    if (!$order || $order['status'] != '10')  return false;
	    
	    $refund_no = Gou_Service_Order::getTradeNo();
	    $reason = '支付超时';
	    //create refund order
	    $refund_order = array(
	        'trade_no'=>$order['trade_no'],
	        'out_trade_no'=>$order['out_trade_no'],
	        'refund_no'=>$refund_no,
	        'description'=>$reason,
	        'status'=>10,
	        'create_time'=>Common::getTime()
	    );
	    Gou_Service_OrderRefund::add($refund_order);
	    
	    $params = array(
    	    "refund_no"=>$refund_no,
    	    "out_trade_no"=>$order['out_trade_no'],
    	    "refund_total"=>$order['real_price'],
    	    "reason"=>$reason,
    	    "notify_url"=>Common::getWebRoot().'/Apk/Alipaynotify/refund',
	    );
	    $api = new Api_Alipay_Refund();
	    $response = $api->refund($params);
	}

	private static function _cookWinPrizeOrder($goods,$address,$params ){
		if(Common::getAndroidtUid()) {
			$out_uid = Common::getAndroidtUid();
		} else {
			$out_uid = Common::getIosUid();
		}
		return array(
			array(
				'buyer_name' => $address['buyer_name'],
				'goods_id' => $goods['id'],
				'order_type' => 5,
				'show_type' => 4,
				'number' => 1,
				'deal_price' => 0,
				'real_price' => Common::money($goods['current_price']),
				'pay_time' => 0,
				'pay_type' => "1",
				'trade_no' => Gou_Service_Order::getTradeNo(),
				'iscash' => 0,
				'create_time'=>Common::getTime(),
				'phone' => $address['mobile'],
				'remark' => "",
				'gbook' => $params['gbook'],
				'out_uid' => $out_uid,
				'status'=> 2,
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

	/**]
	 * @param $goods
	 * @param $address
	 * @param $params
	 * @return array
	 */
	private static function _cookCutOrderData($goods, $address, $params) {
	    $price = Common::money($goods['price']);
	    if(Common::getAndroidtUid()) {
	        $out_uid = Common::getAndroidtUid();
	    } else {
	        $out_uid = Common::getIosUid();
	    }
	    return array(
	        array(
        	    'buyer_name' => $address['buyer_name'],
        	    'goods_id' => $goods['id'],
        	    'order_type' => 5,
	            'show_type' => 3,
        	    'number' => 1,
        	    'deal_price' => $price,
        	    'real_price' => Common::money($goods['current_price']),
        	    'pay_time' => 0,
        	    'pay_type' => "1",
        	    'trade_no' => Gou_Service_Order::getTradeNo(),
        	    'iscash' => 0,
        	    'create_time'=>Common::getTime(),
        	    'phone' => $address['mobile'],
        	    'gbook' => $params['gbook'],
	            'out_uid' => $out_uid,
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
	 * @return Gou_Dao_Order
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Order");
	}
}
