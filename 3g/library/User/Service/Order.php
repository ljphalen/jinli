<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//订单管理

class User_Service_Order {
    static $callFeeType    = 999;
    static $rechargeStatus = array(
        '0' => 2,
        '1' => 1,
        '9' => -1,
    );

    public static function add($params) {
        if (empty($params)) return false;
        $data = self::_checkData($params);
        $res  = self::_getDao()->insert($data);
        return $res ? self::_getDao()->getLastInsertId() : 0;
    }


    public static function getList($page, $limit, $where = array(), $orderBy = array()) {
        if (!is_array($where)) return false;
        $start = (max(intval($page), 1) - 1) * $limit;
        return array(self::count($where), self::_getDao()->getList($start, $limit, $where, $orderBy));
    }

    public static function count($params) {
        if (!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    public static function get($id) {
        if (!is_numeric($id)) return false;
        return self::_getDao()->get($id);
    }

    public static function getBy($params = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    public static function getsBy($params = array(), $order = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getsBy($params, $order);
    }

    public static function update($params, $id) {
        $data = self::_checkData($params);
        return self::_getDao()->update($data, $id);
    }

    public static function delete($id) {
        return self::_getDao()->delete($id);
    }

    //生成唯一订单号
    public static function createOrderId($uid = '') {
        return date('YmdHis', time()) . $uid . mt_rand(10000, 99999);
    }

    /**
     *    生成订单
     *
     * @param array $goods   商品的信息
     * @param array $options 用户兑换商品其它信息
     * @param int   $uid     用户ID
     *
     * @return boolean
     */
    public static function generateOrder($uid, $goods, $options) {
        if (!is_array($options) || !is_array($goods) || !$uid) return array('key' => '-1', 'msg' => '参数有错!');

        $temp                      = array();
        $number                    = $options['goods_number'] ? $options['goods_number'] : 1;
        $temp['uid']               = $uid;
        $temp['order_sn']          = self::createOrderId($uid);//生成唯一订单号
        $temp['order_status']      = 0;
        $temp['pay_status']        = 1;
        $temp['shipping_status']   = 0;
        $temp['order_amount']      = $goods['price'] * $number;
        $temp['total_cost_scores'] = $goods['scores'] * $number;
        $temp['pay_time']          = time();
        $cardMsg                   = User_Service_CardInfo::get($goods['card_info_id']);
        if ($goods['goods_type'] == 1) { //如果是兑换话费或流量包时
            $temp['recharge_number'] = $options['number'];
            $temp['rec_status']      = 0;
            $temp['ordercrach']      = $options['inprice'];
            $temp['order_type']      = $cardMsg['group_type'];
        } else {
            $temp['order_type'] = 5;//实物订单
        }
        $temp['shipping_id'] = $options['shipping_id'] ? $options['shipping_id'] : 0;
        $temp['address_id']  = $options['address_id'] ? $options['address_id'] : 0;
        $temp['add_time']    = time();
        $temp['user_ip']     = Util_Http::getClientIp();
        $temp['card_id']     = $cardMsg['id'];
        $res                 = User_Service_Order::add($temp);
        if ($res) {
            //如果订单成功,则写信息到订单商品表中(当前没有购物车的概念,默认一次提交只是一种商品)
            $add        = User_Service_OrderGoods::addOrderGoodsInfo($res, $number, $goods);
            $updateData = array(
            		'number' => ($goods['number'] - $number),
            		'show_number'=>$goods['show_number']- $goods['num_ratio']*$number
            );
            $reduceNum  = User_Service_Commodities::update($updateData, $goods['id']);//减少库存
            if ($add && $reduceNum) {
            	User_Service_Commodities::getGoodsList(true);
                return $temp['order_sn'];
            }
        }
        return false;
    }

    //未处理订单信息
    public static function unHandleOrders($page, $pageSize, $where, $orderBy) {
        if (!is_array($where)) return false;
        $count = self::_getDao()->getUnHandleCount();
        $data  = self::_getDao()->unHandleOrders($page, $pageSize, $where, $orderBy);
        return array($count, $data);
    }

    //每天处理订单统计
    public static function getOrderSuccessAmountByDay($where, $orderBy, $page, $pageSize) {
        if (!is_array($where)) return false;
        $data = self::_getDao()->getOrderSuccessAmountByDay($where, $orderBy, $page, $pageSize);
    }

    //用户兑换信息及当天金币流通情况
    public static function getUserExchangeMsg($params) {
        $dateList    = self::_getDao()->getUserExchangeMsg($params);
        $total       = count($dateList);
        $successed   = 0; //成功竞换的订单数
        $total_costs = 0; //消耗的金币数
        $temp        = array();
        foreach ($dateList as $k => $v) {
            if ($v['pay_status'] == 1 && $v['order_status'] == 1 && $v['shipping_status'] == 1) {
                ++$successed;
                $total_costs += $v['total_cost_scores'];
            }
            $temp[$v['uid']] += 1;
        }

        return array(
            'total_orders'     => $total,
            'successed_orders' => $successed,
            'user_amount'      => count(array_keys($temp)),
            'total_costs'      => $total_costs
        );
    }

    private static function _checkData($params) {
        $array = array();
        if (isset($params['uid'])) $array['uid'] = $params['uid'];
        if (isset($params['order_sn'])) $array['order_sn'] = $params['order_sn'];
        if (isset($params['order_status'])) $array['order_status'] = $params['order_status'];
        if (isset($params['pay_status'])) $array['pay_status'] = $params['pay_status'];
        if (isset($params['order_sn'])) $array['order_sn'] = $params['order_sn'];
        if (isset($params['order_amount'])) $array['order_amount'] = $params['order_amount'];
        if (isset($params['total_cost_scores'])) $array['total_cost_scores'] = $params['total_cost_scores'];
        if (isset($params['address_id'])) $array['address_id'] = $params['address_id'];
        if (isset($params['pay_time'])) $array['pay_time'] = $params['pay_time'];
        if (isset($params['recharge_number'])) $array['recharge_number'] = $params['recharge_number'];
        if (isset($params['add_time'])) $array['add_time'] = $params['add_time'];
        if (isset($params['user_ip'])) $array['user_ip'] = $params['user_ip'];
        if (isset($params['ordercrach'])) $array['ordercrach'] = $params['ordercrach'];
        if (isset($params['rec_status'])) $array['rec_status'] = $params['rec_status'];
        if (isset($params['rec_order_id'])) $array['rec_order_id'] = $params['rec_order_id'];
        if (isset($params['rec_order_time'])) $array['rec_order_time'] = $params['rec_order_time'];
        if (isset($params['desc'])) $array['desc'] = $params['desc'];
        if (isset($params['order_type'])) $array['order_type'] = $params['order_type'];
        if (isset($params['card_id'])) $array['card_id'] = $params['card_id'];
        if (isset($params['fail_msg'])) $array['fail_msg'] = $params['fail_msg'];
        if (isset($params['shipping_id'])) $array['shipping_id'] = $params['shipping_id'];
        if (isset($params['shipping_status'])) $array['shipping_status'] = $params['shipping_status'];
        if (isset($params['shipping_order'])) $array['shipping_order'] = $params['shipping_order'];
        if (isset($params['express_id'])) $array['express_id'] = $params['express_id'];
        if (isset($params['express_order'])) $array['express_order'] = $params['express_order'];
        return $array;
    }

    static public function run($limit = 10) {
        $where = array(
            'order_status' => 0,
            'pay_status'   => 1,
            'rec_order_id' => 0,
            'order_type'   => array('!=', 5),//实物排除在外
        );
        $ids   = array();
        list(, $dataList) = User_Service_Order::getList(1, $limit, $where, array('id' => 'asc'));
        foreach ($dataList as $v) {//充值或礼品卡等虚拟商品处理
            $ids[$v['id']] = User_Service_Order::_handleOfpayOrder($v);
        }
        return $ids;
    }

    public static function verifyFlowOrder(){
    	$ret = array();
    	$cardIdList = User_Service_CardInfo::getFlowCardIds();
    	list($total,$data) = User_Service_Order::getList(0, 5,array('rec_status'=>'2'),array('id'=>'ASC'));
    	foreach ($data as $k=>$v){
    		if(in_array($v['card_id'], $cardIdList)){
    			$ret[] = self::updateFlowOrderStatus($v);
    		}
    	}
    	return  $ret;
    }
    
    static public function updateFlowOrderStatus($order){
    	$data = array();
    		$ofpayReq                  = new Vendor_Ofpay();
    		$resp                      = $ofpayReq->req_search_flow($order['order_sn']);
    		$data[$order['id']]['response']= json_encode($resp);
    		//成功或失败时才更改状态
    		if($resp['rsp_code'] == '0000' || $resp['rsp_code'] == '0001'){
    			if($resp['rsp_code'] =='0000'){
    				$status = 1;
    			}elseif($resp['rsp_code'] =='0001'){
    				$status =-1;
    			}
    			$params = array(
    					'order_status'	=>$status,
    					'rec_status'		=>$status,
    					'rec_time'			=>time(),
    			);
    			$ret = User_Service_Order::update($params, $order['id']);
    			self::changeScoresAndSendMsg($order,$status);
    			list($params, $tpl) = User_Service_Order::getInnerMsgData($order, $status);
       			 Common_Service_User::sendInnerMsg($params, $tpl);
       			 $data[$order['id']]['sta'] = $ret;
    		}
    		return $data;
    }

    /**
     * 兑换话费
     *
     * @param $orderInfo
     *
     * @return array
     */
    static private function _forOfpayOrder_1($orderInfo) {
        $ofpayReq    = new Vendor_Ofpay();
        $respContent = $ofpayReq->req_telquery($orderInfo['recharge_number'], intval($orderInfo['order_amount']));
       
        self::_addApiRechargeLog(1, $respContent['orderinfo']['sporder_id'], $respContent['orderinfo']['orderid'],$respContent['orderinfo']['retcode'],$respContent);
        if ($respContent['cardinfo']['retcode'] != 1) {
            $upData = array(
                'order_status'   => -1,
                'rec_status'     => -1,
                'rec_order_id'   => 0,
                'ordercrach'     => 0,
                'rec_order_time' => time(),
                'fail_msg'       => sprintf('%s(%s)', $respContent['cardinfo']['err_msg'], $respContent['cardinfo']['retcode']),
            );
            return array($respContent['cardinfo'], $upData);
        }
        
        $out_cardid                = $respContent['cardinfo']['cardid'];
        $resp                      = $ofpayReq->req_onlineorder($orderInfo, $out_cardid);
        $respOrderInfo             = $resp['orderinfo'];
        $respOrderInfo['_uid']     = $orderInfo['uid'];
        $respOrderInfo['_sn']      = $orderInfo['order_sn'];
        $respOrderInfo['_card_id'] = $orderInfo['card_id'];
        $upData                    = self::_handleUpData($respOrderInfo);
        return array($respOrderInfo, $upData);
    }

    /**
     * 兑换购物券
     *
     * @param $orderInfo
     *
     * @return array
     */
    static private function _forOfpayOrder_2($orderInfo) {
        $ofpayReq                  = new Vendor_Ofpay();
        $resp                      = $ofpayReq->req_order($orderInfo);
        $respOrderInfo             = $resp['orderinfo'];
        $respOrderInfo['_uid']     = $orderInfo['uid'];
        $respOrderInfo['_sn']      = $orderInfo['order_sn'];
        $respOrderInfo['_card_id'] = $orderInfo['card_id'];
        $upData                    = self::_handleUpData($respOrderInfo);
        return array($respOrderInfo, $upData);

    }

    /**
     * 兑换流量
     *
     * @param $orderInfo
     *
     * @return array
     */
    static private function _forOfpayOrder_3($orderInfo) {
    	$respOrderInfo = array();
        $ofpayReq      = new Vendor_Ofpay();
        $resp          = $ofpayReq->req_flowOrder_new($orderInfo);
        if(!empty($resp)){
        	if($resp['rsp_code'] =='0000'){
        		$respOrderInfo['retcode'] = 1;
        		$respOrderInfo['rec_order_id'] = $resp['body']['wallet_order_id'];
        		$respOrderInfo['orderid'] = $resp['body']['wallet_order_id'];
        		$respOrderInfo['rec_order_time'] = strtotime($resp['body']['order_submit_time']);
        	}else{
        		$respOrderInfo['retcode']  = -1;
        		$respOrderInfo['err_msg'] = $resp['rsp_desc'];
        	}
        	$respOrderInfo['cardnum']  = 1;
        	$respOrderInfo['ordercash'] = $orderInfo['order_amount'];
        }
        self::_addApiRechargeLog(3, $resp['body']['wallet_order_id'], $resp['body']['order_id'], $respOrderInfo['retcode'],$resp);
        
        $respOrderInfo['_uid']     = $orderInfo['uid'];
        $respOrderInfo['_sn']      = $orderInfo['order_sn'];
        $respOrderInfo['_card_id'] = $orderInfo['card_id'];
        $upData                    = self::_handleUpData($respOrderInfo);
        return array($respOrderInfo, $upData);
    }

    
    static private function  _forOfPayOrder_3_old($orderInfo){
    	$ofpayReq      = new Vendor_Ofpay();
    	$resp          = $ofpayReq->req_flowOrder_new($orderInfo);
    	$respOrderInfo = $resp['orderinfo'];
    	$respOrderInfo['_uid']     = $orderInfo['uid'];
    	$respOrderInfo['_sn']      = $orderInfo['order_sn'];
    	$respOrderInfo['_card_id'] = $orderInfo['card_id'];
    	$upData                    = self::_handleUpData($respOrderInfo);
    	return array($respOrderInfo, $upData);
    }

    /**
     * 兑换Q币
     *
     * @param $orderInfo
     *
     * @return array
     */
    static private function _forOfpayOrder_4($orderInfo) {
        $ofpayReq                  = new Vendor_Ofpay();
        $cardMsg                   = User_Service_CardInfo::get($orderInfo['card_id']);
        $orderInfo['order_amount'] = $cardMsg['card_value'];
        $resp                      = $ofpayReq->req_onlineorder($orderInfo);
        $respOrderInfo             = $resp['orderinfo'];
        $respOrderInfo['_uid']     = $orderInfo['uid'];
        $respOrderInfo['_sn']      = $orderInfo['order_sn'];
        $respOrderInfo['_card_id'] = $orderInfo['card_id'];
        $respOrderInfo['cardnum']  = 1;
        $upData                    = self::_handleUpData($respOrderInfo);
        return array($respOrderInfo, $upData);
    }


    /**
     * 兑换电话时间
     *
     * @param $orderInfo
     *
     * @return array
     */
    static public function forSelfOrder_999($orderInfo) {
        $userInfo     = Gionee_Service_User::getUser($orderInfo['uid']);
        $userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($userInfo['mobile']);
        if (empty($userVoipInfo)) {
            $addData                      = array('user_phone' => $userInfo['mobile'], 'get_time' => time());
            $vid                          = Gionee_Service_VoIPUser::add($addData);
            $userVoipInfo['id']           = $vid;
            $userVoipInfo['exchange_sec'] = 0;
        }
        $newSec = $userVoipInfo['exchange_sec'] + $orderInfo['order_amount'];
        Gionee_Service_VoIPUser::set(array('exchange_sec' => $newSec), $userVoipInfo['id']);
        $upData = array(
            'order_status'    => 1,
            'rec_status'      => 1,
            'shipping_status' => 1,
            'rec_order_id'    => 0,
            'ordercrach'      => 0,
            'rec_order_time'  => time(),
            'fail_msg'        => '',
        );
        $ret    = User_Service_Order::update($upData, $orderInfo['id']);
        if ($ret) {
            User_Service_Order::changeScoresAndSendMsg($orderInfo, $upData['order_status']);
        }
    }

    //得到站内信数据
    public static function getInnerMsgData($order = array(), $status = 1) {
        $data    = array();
        $tplFlag = '';
        switch ($order['order_type']) {
            case '1': { //手机充值
                $data    = array(
                    'classify'        => 1,
                    'recharge_number' => $order['recharge_number'],
                    'recharge_money'  => $order['order_amount'],
                );
                $tplFlag = 'recharge_msg_tpl';
                break;
            }
            case '3': {//手机充流量
                $data    = array(
                    'classify'        => 3,
                    'recharge_number' => $order['recharge_number'],
                    'recharge_money'  => $order['order_amount'],
                );
                $tplFlag = 'charge_flow_tpl';
                break;
            }
            case '4': { //Q币
                $data    = array(
                    'classify'       => 4,
                    'qq_number'      => $order['recharge_number'],
                    'recharge_money' => $order['order_amount'],
                );
                $tplFlag = 'charge_qqcoin_tpl';
                break;
            }
            case '5': { //实物
                $data    = array(
                    'classify'       => 5,
                    'receiver_name'  => $order['recevier_name'],
                    'address'        => $order['address'],
                    'mobile'         => $order['mobile'],
                    'shipping_order' => $order['shipping_order'],
                );
                $tplFlag = 'entity_goods_exchange_tpl';
            }
            case '999': { //兑换通话时长
                $data    = array(
                    'classify' => 999,
                    'minutes'  => bcdiv($order['order_amount'], 60),
                );
                $tplFlag = 'charge_voip_seconds_tpl';
                break;
            }
            default:
                break;
        }
        $params = array_merge(array('uid' => $order['uid'], 'status' => $status), $data);
        return array($params, $tplFlag);
    }

    public  static function req_flowOrder_result($num=5){
    	$where = array();
    	$where['order_status'] = array("!=",-1);
    	$where['order_type'] = 3;
    	$where['fail_msg'] = array('=','');
    	list($total,$dataList) = User_Service_Order::getList(0,$num, $where,array('id'=>'ASC'));
    	foreach($dataList as $k=>$v){
    		$status = -1;
    		$arr = array();
    		 $ofpay = new Vendor_Ofpay();
    		 $ret = $ofpay->flowRechargeResult(array("order_id"=>$v['order_sn']));
    		 if(!empty($ret['body']) && in_array($ret['body']['status'],array('4','9'))){
    		 		if($ret['body']['status'] == '4'){
    		 			$status = 1;
    		 			$arr['fail_msg']  = '确认充值成功!';
    		 		}else{
    		 			$arr['order_status'] = $arr['rec_status']  = -1;
    		 			$arr['fail_msg'] = '充值失败!';
    		 		}
    		 		self::update($arr, $v['id']);
    		 		self::changeScoresAndSendMsg($v, $status);
    		 }
    	}
    }
    
    /**
     * 订单更新数据结构
     *
     * @param $orderInfo
     * @param $respOrderInfo
     *
     * @return array
     */
    static private function _handleUpData($params) {
        $upData = array();
        if ($params['retcode'] == 1) {
            $retcode      = $params['retcode'];
            $fail_msg     = '';
            $status       = User_Service_Order::$rechargeStatus[$retcode];
            $order_status  = $status;
            $rec_order_id = $params['orderid'];
            $ordercrach   = $params['ordercash'] * $params['cardnum'];
            $upData       = array(
                'order_status'   => $order_status,
                'rec_status'     => 2,
                'rec_order_id'   => $rec_order_id,
                'ordercrach'     => $ordercrach,
                'rec_order_time' => time(),
                'fail_msg'       => $fail_msg,
            );

        }
        return $upData;
    }

    /**
     * 写调用第三方接口日志
     */
    private static function _addApiRechargeLog($apiType = 1, $outOrderID, $innerOrderID, $stutus, $response = array()) {
        $addData = array(
            'api_type' => $apiType,
            'order_id' => $outOrderID,
            'add_time' => time(),
            'order_sn' => $innerOrderID,
            'status'   => $stutus,
            'desc'     => json_encode($response)
        );
        User_Service_Recharge::add($addData);
    }

    /**
     * 发送站内信　卡卷
     *
     * @param $uid
     * @param $status
     * @param $cardMsg
     * @param $card
     */
    static public function sendInnerCardMsg($uid, $status, $cardMsg, $card) {
        $data = array(
            'uid'      => $uid,
            'classify' => 2,
            'status'   => $status,
            'money'    => $cardMsg['card_value'],
            'name'     => $cardMsg['type_name'],
            'number'   => $card['cardno'],
            'password' => $card['cardpws'],
            'expire'   => $card['expiretime'],
        );

        Common_Service_User::sendInnerMsg($data, 'coupon_msg_tpl');
    }

    /**
     * 处理自身订单操作
     */
    static private function _handleSelfOrder() {

    }

    /**
     * 处理物品订单操作
     */
    static private function _handleEntityOrder() {

    }

    /**
     * 处理欧飞订单操作
     *
     * @param $orderInfo
     *
     * @return bool|int
     */
    static public  function _handleOfpayOrder($orderInfo) {
        $upData = array(
            'order_status'   => -1,
            'rec_status'     => -1,
            'rec_order_id'   => 0,
            'ordercrach'     => 0,
            'rec_order_time' => time(),
            'fail_msg'       => '充值失败！',
        );
 
        $cardMsg = User_Service_CardInfo::get($orderInfo['card_id']);
        $func    = '_forOfpayOrder_' . $cardMsg['group_type'];
        if (method_exists('User_Service_Order', $func)) {
            list($respOrderInfo, $callData) = self::$func($orderInfo);
            if ($respOrderInfo['retcode'] != 1) {
                $upData['fail_msg'] = sprintf('%s(%s)', $respOrderInfo['err_msg'], $respOrderInfo['retcode']);
            } else if (!empty($callData)) {
                if (in_array($cardMsg['group_type'], array('1', '2', '3', '4', '999'))) {
                    $rcKey = 'user_pay_phone_times' . date('Ymd');
                    Common::getCache()->hIncrBy($rcKey, $orderInfo['recharge_number']);
                }
                $upData = $callData;
            }
        }
        $ret = User_Service_Order::update($upData, $orderInfo['id']);
        if(empty($ret) || $upData['order_status'] == '-1'){
        	User_Service_Order::changeScoresAndSendMsg($orderInfo, '-1');
        }
        if ( !empty($respOrderInfo)) {
            if (!empty($respOrderInfo['cards']['card'])) {//发送卡卷帐号密码
                self::sendInnerCardMsg($orderInfo['uid'], $respOrderInfo['retcode'], $cardMsg, $respOrderInfo['cards']['card']);
            }
            self::_addApiRechargeLog($cardMsg['group_type'], $respOrderInfo['orderid'], $orderInfo['order_sn'], $respOrderInfo['game_state']);
        }
        return $ret;
    }

    public static function getRechargeTimesInfo($where = array(), $number = 1, $page, $pageSize) {
        if ($page < 1) {
            $page = 1;
        }
        $page  = $pageSize * ($page - 1);
        $total = self::_getDao()->getTotalNumber($where, $number);
        $data  = self::_getDao()->getRechargeTimesInfo($where, $number, $page, $pageSize);
        return array($total, $data);
    }


    public static function getRechargedMsg($where, $groupBy, $number, $page, $pageSize) {
        $page  = $pageSize * ($page - 1);
        $total = self::_getDao()->TotalMonthRechargeNum($where, $groupBy, $number);
        $data  = self::_getDao()->getRechargedMsg($where, $groupBy, $number, $page, $pageSize);
        return array($total, $data);
    }

    /**
     * 用户订单积分操作
     *
     * @param $orderId
     * @param $orderStatus
     */
    static public function changeScoresAndSendMsg($orderInfo, $orderStatus) {
        if ($orderStatus == 1) {
            Common_Service_User::changeUserScores($orderInfo['id'], '401', '-');//扣除冻结积分
        } else if ($orderStatus == -1) {
            Common_Service_User::changeUserScores($orderInfo['id'], '205');//返还冻结积分
        }
        list($params, $tpl) = User_Service_Order::getInnerMsgData($orderInfo, $orderStatus);
        Common_Service_User::sendInnerMsg($params, $tpl);
    }

    /**
     * @return User_Dao_Order
     */
    private static function _getDao() {
        return Common::getDao("User_Dao_Order");
    }

    /**
     * error log
     *
     * @param string $error
     * @param string $file
     */
    static public function log($error) {
        error_log(date('Y-m-d H:i:s') . ' ' . Common::jsonEncode($error) . "\n", 3, Common::getConfig('siteConfig', 'logPath') . '/order/' . date('Ymd') . '.log');
    }

}