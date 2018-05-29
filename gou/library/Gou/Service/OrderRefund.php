<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desOrderRefundiption here ...
 * @author tiansh
 *
 */
class Gou_Service_OrderRefund extends Common_Service_Base{
	
    public static $logFile = "order_refund.log";

	/**
	 *
	 * Enter getOrderRefund 
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter desOrderRefundiption here ...
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
	 * @param array $data
	 */
	public static function add($data) {
	    if (!is_array($data)) return false;
	    $data = self::_cookData($data);
	    $ret = self::_getDao()->insert($data);
	    if (!$ret) return false;
	    return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function update($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
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
	 * @param int $goods_id
	 * @param int $address_id
	 */
	public static function create_refund($trade_no, $reason) {
	    $orderInfo = self::_cookOrder($trade_no);
	    if($orderInfo) {
	        try {
	            $trans = parent::beginTransaction();
	            if (!$trans) throw new Exception("begin transaction failed.");
	            	        
	           //create local refund order
	           $order_id = Gou_Service_OrderRefund::add($orderInfo);
	           if (!$order_id) throw new Exception("create local order failed.", -201);
	           
	            //create gionee pay order
	            $webroot = Common::getWebRoot();
	            $order_params = array(
	                    "order_no"=>$orderInfo['trade_no'],
	                    "out_order_no"=>$orderInfo['out_trade_no'],
	                    "total_fee"=>$orderInfo['real_price'],
	                    "refund_fee"=>$orderInfo['real_price'],
	                    "out_refund_no"=>$orderInfo['refund_no'],
	                    "refund_date"=>date('YmdHis', $orderInfo['create_time']),
	                    "notify_url"=>$webroot.'/front/notify/refund',
	                    "reason"=>$reason
	                );
	            $pay_ret = Api_Gionee_Pay::refund($order_params);
	            
	            if (Util_String::substr($pay_ret['status'], 0, 4) != 2000) {
	                throw new Exception("create gionee refund order failed.", -202);
	              }
	        
	            $ret = self::_getDao()->update(array('out_refund_no'=>$pay_ret['refund_no']), $order_id);
	            if (!$ret) throw new Exception("update order failed.", -203);
	            
	            //commit
                $return = parent::commit();
                if (!$return) throw new Exception("transactoin commit failed.", -204);
                return array($order_id, $pay_ret['refund_no']);
	        } catch(Exception $e) {
    			parent::rollBack();
    			Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
    			return false;
    		}
	    }
	    
	}
	
	
	/**
	 * getOrderPayStatus
	 * @param string $data
	 */
	public static function getRefundStatus($refund_no, $append = array()) {
	    $order_refund = self::getBy(array('refund_no'=>$refund_no));
	    if (!$order_refund) {
	        Common::log('Error: ' . $refund_no, 'refund_notify.log');
	        return false;
	    }
	    
	    try {
	        $trans = parent::beginTransaction();
	        if (!$trans) throw new Exception("begin transaction failed.");
	
            $result = Api_Gionee_Pay::queryRefund(array(
                'order_no'=>$order_refund['out_trade_no'],
                'out_order_no'=>$order_refund['trade_no'],
                'refund_no'=>$order_refund['out_refund_no'],
                'out_refund_no'=>$order_refund['refund_no']
            ));
	        
            $order = Gou_Service_Order::getByTradeNo($order_refund['trade_no']);
    	    //更改订单状态
    	    if (in_array($result['refund_status'], array('30', '40', '50', '60', '70'))) {
    	        //退款成功
    	        if($result['refund_status'] == 30) {
    	            $order['status'] = 12;
    	            Common::sms($order['phone'], '尊敬的客户，您的订单 '.$order['trade_no'].' 交易失败,已将您的付款金额退回您的账户,请注意查收。');
    	        }
    	        if(in_array($result['refund_status'], array('40', '50', '60', '70'))) $order['status'] = 13;
    	        
    	        $order_ret = Gou_Service_Order::updateByOutTradeNo(array('status'=>$order['status']), $order["out_trade_no"]);
    	        if (!$order_ret) {
    	            Common::log('update order status : ' . $order['trade_no'], 'refund_notify.log');
    	            throw new Exception('update order status : ' . $order['trade_no'], -302);
    	        }
    	     }

	        $update_data = array(
	                'status'=>$result['refund_status'],
	                'description'=>$append['description'] ? $append['description'] : $result['description'],
	                'refund_time'=>strtotime($result['refund_time'])
	        );
	        
	        $ret = self::update($update_data, $order_refund["id"]);
	        if (!$ret) {
	            Common::log('update order status : ' . $order_refund['trade_no'], 'refund_notify.log');
	            throw new Exception('update order status : ' . $order_refund['trade_no'], -303);
	        }
	        
	        //order log
	        $log = array(
	                'order_id'=>$order['id'],
	                'order_type'=>1,
	                'uid'=>0,
	                'create_time'=>time(),
	                'update_data'=>json_encode(array('refund_status' => $result['refund_status']))
	        );
	        Gou_Service_Order::addOrderLog($log);
	        if($trans) {
	            $return = parent::commit();
	            if (!$return) throw new Exception("transactoin commit failed.", -304);
	            return true;
	        }
	        
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
	public static function cancleRefund($trade_no) {
	    $order = self::getBy(array('trade_no'=>$trade_no));
	    if (!$order) {
	        Common::log('Error: ' . $trade_no, 'refund_notify.log');
	        return false;
	    }
	    
	    try {
	        $trans = parent::beginTransaction();
	        if (!$trans) throw new Exception("begin transaction failed.");
	
    	    $result = Api_Gionee_Pay::cancleRefund(array(
    	            'order_no'=>$order['out_trade_no'],
    	            'out_order_no'=>$order['trade_no'],
    	            'refund_no'=>$order['out_refund_no'],
    	            'out_refund_no'=>$order['refund_no']
    	    ));
    	     
    	    //更改订单状态
    	    if (Util_String::substr($result['status'], 0, 4) != 2000) {
    	        Common::log('cancle refund fail : ' . $trade_no, 'cancle_refund.log');
    	        throw new Exception('cancle refund fail : ' . $trade_no,  -503);
    	    }
    	    
    	    $ret = self::update(array('status'=>40), $order["id"]);
    	    if (!$ret) {
    	        Common::log('update order refund status : ' . $trade_no, 'cancle_refund.log');
    	        throw new Exception('update order refund status : ' . $trade_no,  -504);
    	    }
	        
    	    if($trans) {
    	        $return = parent::commit();
    	        if (!$return) throw new Exception("transactoin commit failed.", -505);
    	        return true;
    	    }	        
	        
        } catch(Exception $e) {
            parent::rollBack();
            Common::log(array($e->getCode(), $e->getMessage()), self::$logFile);
            return false;
        }	    
	}
	
	public static function refundStatus($status) {
	    $orderStatus = array(
	            10 => '申请中',
	            20 => '已受理正在处理退款中',
	            30 => '退款处理成功',
	            40 => '取消',
	            50 => '驳回',
	            60 => '退款失败',
	            70 => '超过有效期'
	    );
	    return $status ? $orderStatus[intval($status)] : '';
	}
	
	/**
	 *
	 * @param array $user
	 * @param array $goods
	 * @param array $address
	 * @return array
	 */
	private static function _cookOrder($trade_no) {
	    if(!$trade_no) return false;
	    $order = Gou_Service_Order::getByTradeNo($trade_no);
	    if(!$order) return false;	    
	    if(!in_array($order['status'], array(2, 3, 4, 7, 8, 11, 13)))  return false;
	    
	    $data = array(
	         'trade_no'=>$order['trade_no'],
            'out_trade_no'=>$order['gionee_order_no'],
	         'real_price'=>$order['real_price'],
            'refund_no'=>Gou_Service_Order::getTradeNo(),
            'status'=>0,
            'create_time'=>Common::getTime()
	     );
	    return $data;
	}

	/**
	 *
	 * Enter desOrderRefundiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['trade_no'])) $tmp['trade_no'] = $data['trade_no'];
		if(isset($data['out_trade_no'])) $tmp['out_trade_no'] = $data['out_trade_no'];
		if(isset($data['refund_no'])) $tmp['refund_no'] = $data['refund_no'];
		if(isset($data['out_refund_no'])) $tmp['out_refund_no'] = $data['out_refund_no'];
		if(isset($data['description'])) $tmp['description'] = $data['description'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['refund_time'])) $tmp['refund_time'] = $data['refund_time'];
		return $tmp;
	}
	
	/**
	 * create trade_no
	 * @return string
	 */
	public static function getRefundNo() {
	    list($usec, $sec) = explode(" ", microtime());
	    $usec = substr(str_replace('0.', '', $usec), 0, 4);
	    $str = rand(10, 99);
	    return date("YmdHis") . $usec . $str;
	}
		
	/**
	 *
	 * @return Gou_Dao_OrderRefund
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_OrderRefund");
	}
}
