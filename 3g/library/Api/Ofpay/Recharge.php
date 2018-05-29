<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Api_Ofpay_Recharge {

    //外部状态与内部的关系
    static public $rechargeStatus = array(
        '0' => 2,
        '1' => 1,
        '9' => -1,
    );

    /**
     * 充值接口
     */
    public static function recharge($order_sn) {
        $order     = User_Service_Order::getBy(array('order_sn' => $order_sn));
        $queryInfo = self::checkMobileStatus($order['recharge_number'], intval($order['order_amount']));
        if ($queryInfo['cardinfo']['retcode'] != 1) {
            //$this->_getOutPut(0,'系统维护中，暂不能充值');
        }
        $data = array(
            'cardid'          => $queryInfo['cardinfo']['cardid'],
            'cardnum'         => $order['order_amount'],
            'order_sn'        => $order['order_sn'],
            'create_time'     => $order['add_time'],
            'recharge_number' => $order['recharge_number'],
            'ret_url'         => sprintf('%s/api/recharge/response', Common::getCurHost()),
        );

        $response = self::onlineOrder($data);
        if (empty($response)) {
            return self::_getOutPut('0', '充值失败！');
        }
        self::addApiRechargeLog(1, $response['orderinfo']['orderid'], $order_sn, $response['orderinfo']['game_state']);
        $ret = self::updateOrder($response['orderinfo'], $order['id']);
        return self::_getOutPut($ret);
    }

    /**
     * 获取购物券
     */
    public static function getCoupon($order_sn, $cardMsg = array()) {
        $order    = User_Service_Order::getBy(array('order_sn' => $order_sn));
        $response = self::_getCouponResult($cardMsg['card_id'], $order['order_sn'], 1, $order['add_time']);
        if (empty($response)) {
            return self::_getOutPut('0', '充值失败！');
        }
        self::updateOrder($response['cardinfo']['game_state'], $order['id'], $response['cardinfo']['orderid'], $response['cardinfo']['ordercash']);
        self::addApiRechargeLog(2, $response['cardinfo']['orderid'], $order_sn, $response['cardinfo']['game_state']);
        User_Service_Order::sendInnerCardMsg($order['uid'], $response['orderinfo']['game_state'], $cardMsg, $response['orderinfo']['cards']['card']);
        $result = self::changeScoresByStatus(self::$rechargeStatus[$response['cardinfo']['game_state']], $order['id']);
        return self::_getOutPut($result);
    }

    /**
     * 兑换流量包
     * $order_sn string  内部生成的订单号
     * $cardMsg array  流量包的详细信息
     */
    public static function getMobileFlow($order_sn, $cardMsg = array()) {
        $order = User_Service_Order::getBy(array('order_sn' => $order_sn));

        $response = self::_chargeFlow($order['recharge_number'], $order['order_amount'], $cardMsg['ext'], $order['order_sn']);
        if (empty($response)) {
            return self::_getOutPut('0', '运营商系统维护中，请稍后再试!');
        }
        self::addApiRechargeLog(3, $response['orderinfo']['orderid'], $order_sn, $response['cardinfo']['game_state']);
        self::updateOrder($response['cardinfo']['game_status'], $order['id'], $response['cardinfo']['orderid'], $response['cardinfo']['ordercash']);
        $ret = self::changeScoresByStatus(self::$rechargeStatus[$response['cardinfo']['game_state']], $order['id']);
        return self::_getOutPut($ret);
    }

    /**
     * Q币充值
     */
    public static function QQCoinRecharge($orderSn, $cardMsg) {
        $order = User_Service_Order::getBy(array('order_sn' => $orderSn));
        if (empty($order) || empty($cardMsg)) {
            return self::_getOutPut(0);
        }
        $retUrl = sprintf('%s/api/recharge/response', Common::getCurHost());
        /* 	$response = array(
                    'orderinfo'=> array(
                            "err_msg" => "",
                            "retcode"=> "1",
                            "orderid"=>  "S1503249264310",
                            "cardid"=>  "220612" ,
                            "cardnum"=> "3",
                            "ordercash"=> "0.964",
                            "cardname"=> 'ddd',
                            "sporder_id"=> "201503241620151581018150",
                            "game_userid"=> "296699151",
                            "game_area"=>  "",
                            "game_srv"=> "",
                            "game_state"=> "0"
                    )
            ); */
        $response = self::_getOnlineRechargeResult($cardMsg['card_id'], $order, $cardMsg['card_value'], $retUrl);
        if (empty($response) || !is_array($response['orderinfo'])) {
            return self::_getOutPut('0', '运营商系统维护中，请稍后再试!');
        }
        self::addApiRechargeLog(4, $response['orderinfo']['orderid'], $orderSn, $response['orderinfo']['game_state']);
        self::updateOrder($response['orderinfo'], $order['id']);
        $ret = self::changeScoresByStatus(self::$rechargeStatus[$response['orderinfo']['game_state']], $order['id']);
        return self::_getOutPut($ret);
    }

    //库存检测

    public static function checkIfEnoughNumber($cardId) {
        $cardInfo = self::queryCardInfo($cardId);
        if ($cardInfo['cardinfo']['retcode'] != '1') {
            return self::_getOutPut(0, '查询商品信息失败，请重试！');
        }
        $leftInfo = self::queryleftnumber($cardId);//查看剩下的卡数量
        if ($leftInfo['cardinfo']['retcode'] == '1') {
            if ($leftInfo['cardinfo']['ret_cardinfos']['card']['innum'] < 1) {
                return self::_getOutPut(0, '该卡库存不足！');
            }
        } else {
            return self::_getOutPut(0, '系统内部错误！');
        }
    }

    //充值返回
    private static function _getCouponResult($cardId, $orderSn, $number, $orderTime) {
        $params = array(
            'cardid'       => $cardId,
            'sporder_id'   => $orderSn,
            'cardnum'      => $number,
            'sporder_time' => date('YmdHis', $orderTime),
        );
        return self::couponMsg($params);
    }

    //返回在线充值接口调用后的数值
    private static function _getOnlineRechargeResult($cardId = 0, $order = array(), $num = 1, $retUrl = '') {

        $data['cardid']          = $cardId;
        $data['cardnum']         = intval($num);
        $data['order_sn']        = $order['order_sn'];
        $data['create_time']     = time();
        $data['recharge_number'] = $order['recharge_number'];
        $data['ret_url']         = $retUrl;
        return self::onlineOrder($data);
    }

    /**
     * 写调用第三方接口日志
     */
    public static function addApiRechargeLog($apiType = 1, $outOrderID, $innerOrderID, $stutus, $response = array()) {
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

    public static function sendInnerMsg($uid, $classify = 1, $status, $cardMsg, $cardMsg, $Tpl = '') {
        $data = array(
            'uid'      => $uid,
            'classify' => $classify,
            'status'   => $status,
            'cardMsg'  => $cardMsg,
            'card'     => $cardMsg
        );
        Common_Service_User::sendInnerMsg($data, $Tpl);
    }

    /**
     * 根据订单状态更改用户积分数
     *
     * @param unknown $orderStatus
     * @param unknown $orderId
     * @param string  $logFlag
     *
     * @return number
     */
    public static function changeScoresByStatus($orderStatus, $orderId, $logFlag = true) {
        $resultFlag = 1;
        if (!$logFlag || $orderStatus == -1) {
            $resultFlag = 0;
            Common_Service_User::changeUserScores($orderId, '205');//返还冻结积分
            return $resultFlag;
        }
        if ($orderStatus == 1) {
            Common_Service_User::changeUserScores($orderId, '401', '-');//扣除冻结积分
        }
        return $resultFlag;
    }

    private static function _getOutPut($status, $msg = '') {
        if (empty($msg)) {
            $temp = array(
                '0' => array('key' => -1, 'msg' => '操作失败'),
                '1' => array('key' => 0, 'msg' => '成功！'),
            );
        } else {
            $temp = array($status => array('key' => $status, 'msg' => $msg));
        }
        return $temp[$status];
    }

    private static function _chargeFlow($phoneNum, $perValue, $flowValue, $spOrderId, $retUrl = '') {
        if (!$phoneNum || !$perValue || !$flowValue) return false;
        //参数构造
        $params           = array_merge(self::_getBaseParams(), array(
            'phoneno'         => trim($phoneNum), //手机号
            'perValue'        => intval($perValue), //流量面值
            'flowValue'       => trim($flowValue), //流量值
            'netType'         => '3G', //网络制式
            'range'           => 2, //1,省内 2，全国
            'effectStartTime' => 1, //生效时间 1 当天 2 次日 3 次月
            'effectTime'      => 1, //有效期 1:当月有效
            'sporderId'       => $spOrderId,//内部订单号
            'retUrl'          => $retUrl, // 回调URL
        ));
        $config           = self::_ofpayConfig();
        $key_str          = $config['ofpay_keyStr'];
        $md5_str          = $params['userid'] . $params['userpws'] . $params['phoneno'] . $params['perValue'] . $params['flowValue'] . $params['range'] . $params['effectStartTime'] . $params['effectTime'] . $params['netType'] . $params['sporderId'] . $key_str;
        $params['md5Str'] = strtoupper(md5($md5_str));
        return self::_getResponse('flowOrder.do?', $params);
    }

    /**
     *
     * @param number  $chargeStatus 充值状态
     * @param unknown $orderId      内部订单ID
     * @param unknown $outOrderId   外部订单号
     * @param unknown $realCost     实际花费成本
     */
    public static function updateOrder($orderInfo = array(), $orderId) {
        $chargeStatus = $orderInfo['retcode'];
        if ($chargeStatus == 9) {
            $order_status = $rechare_status = -1;
        } else {
            $order_status = $rechare_status = self::$rechargeStatus[$chargeStatus];
        }
        $options = array(
            'order_status'   => $order_status,
            'rec_status'     => $rechare_status,
            'rec_order_id'   => $orderInfo['orderid'],
            'ordercrach'     => $orderInfo['ordercash'] * $orderInfo['cardnum'],
            'rec_order_time' => time(),
        );
        $res     = User_Service_Order::update($options, $orderId);
        return $res;
    }

    /**
     * 获取优惠券的密码
     */
    private static function  couponMsg($data = array()) {
        if (is_array($data) && $data['cardid'] && $data['cardnum'] && $data['sporder_id'] && $data['sporder_time']) {
            $params            = array_merge(self::_getBaseParams(), $data);
            $config            = self::_ofpayConfig();
            $key_str           = $config['ofpay_keyStr'];
            $md5_str           = $params['userid'] . $params['userpws'] . $params['cardid'] . $params['cardnum'] . $params['sporder_id'] . $params['sporder_time'] . $key_str;
            $params['md5_str'] = strtoupper(md5($md5_str));
            return self::_getResponse('order.do?', $params);
        }
    }

    /**
     * 用户信息查询接口，用于查询SP用户的信用点余额
     */

    public static function getUserInfo() {
        return self::_getResponse('queryuserinfo.do?', self::_getBaseParams());
    }

    /**
     * 检测用户手机号是否能充值
     * 此接口用于查询手机号是否能充值，如果能就返回商品信息，不能就返回提示正中维护中
     */
    public static function checkMobileStatus($mobile, $pervalue) {
        $params = array_merge(self::_getBaseParams(), array(
            'phoneno'  => $mobile,
            'pervalue' => intval($pervalue)
        ));
        return self::_getResponse('telquery.do?', $params);
    }


    /**
     * 查询手机号码平台信息
     */

    public static function getMobileplatform($mobile) {
        $config   = self::_ofpayConfig();
        $url      = $config['ofpay_url'] . 'mobinfo.do?' . http_build_query(array('mobilenum' => $mobile));
        $response = Util_Http::get($url);
        if ($response->state !== 200) {
            Common::log(array($url, $response), 'ofpay_response.log');
            return false;
        }
        $mobinfo = iconv('gbk', 'utf-8', $response->data);
        $mobinfo = explode('|', $mobinfo);
        $plat    = Util_String::substr($mobinfo[2], 0, 2);
        $plat_id = 0;
        switch ($plat) {
            case '移动':
                $plat_id = 10;
                break;
            case '联通':
                $plat_id = 11;
                break;
            case '电信':
                $plat_id = 12;
                break;
            default:
                $plat_id = 0;
        }

        return $plat_id;
    }

    /**
     * 充值第三方处理接口
     *
     * @param unknown $data
     *
     * @return boolean|Ambigous <boolean, DOMDocument, mixed, string, multitype:multitype: string multitype:string
     *                          mixed >
     */
    public static function onlineOrder($data) {
        if (!is_array($data) || !$data['cardid'] || !$data['cardnum'] || !$data['order_sn'] || !$data['create_time'] || !$data['recharge_number'] || !$data['ret_url']) return false;
        $params            = array_merge(self::_getBaseParams(), array(
            'cardid'       => $data['cardid'],//onlineorder
            'cardnum'      => intval($data['cardnum']),
            'sporder_id'   => $data['order_sn'],
            'sporder_time' => date('YmdHis', $data['create_time']),
            'game_userid'  => $data['recharge_number'],
            'ret_url'      => $data['ret_url']
        ));
        $config            = self::_ofpayConfig();
        $key_str           = $config['ofpay_keyStr'];
        $md5_str           = $params['userid'] . $params['userpws'] . $params['cardid'] . $params['cardnum'] . $params['sporder_id'] . $params['sporder_time'] . $params['game_userid'] . $key_str;
        $params['md5_str'] = strtoupper(md5($md5_str));
        return self::_getResponse('onlineorder.do?', $params);
    }

    /**
     * 查询商品信息的同步接口
     */
    public static function queryCardInfo($cardId) {
        $params = array_merge(self::_getBaseParams(), array(
            'cardid' => $cardId,
        ));
        return self::_getResponse('querycardinfo.do?', $params);
    }

    /**
     * 查询是否有库存
     */
    public static function queryleftnumber($cardId) {
        $params = array_merge(self::_getBaseParams(), array(
            'cardid' => $cardId,
        ));
        return self::_getResponse('querycardinfo.do?', $params);
    }

    /**
     * 根据SP订单号补发充值状态
     * 此接口用于没有接收到回调充值状态的情况下进行补发
     *
     * @param string $phone
     * @param string $pervalue
     */
    public static function reissue($order_id) {
        $params = array_merge(self::_getBaseParams(), array(
            'spbillid' => $order_id
        ));
        return self::_getResponse('reissue.do?', $params);
    }

    /**
     * 获得商品的信息
     */

    //数据处理
    public static function _getResponse($action, $params) {
        $config   = self::_ofpayConfig();
        $url      = $config['ofpay_url'] . $action . http_build_query($params);
        $response = Util_Http::get($url);
        if ($response->state != 200) {
            Common::log(array($url . $action, $params, $response), 'ofpay_response.log');
            return false;
        }
        $ret = Util_XML2Array::createArray($response->data);
        //如果是充值就是写充值日志
        if ($action == 'onlineorder.do?') {
            $options = array(
                'api_type' => '1',
                'order_sn' => $params['sporder_id'],
                'order_id' => $ret['orderinfo']['orderid'],
                'add_time' => time(),
                'desc'     => json_encode($ret),
                'status'   => $ret['orderinfo']['game_state']
            );
            User_Service_Recharge::add($options);
        }
        return $ret;
    }

    //基础数据信息
    public static function _getBaseParams() {
        $config = self::_ofpayConfig();
        return array(
            'userid'  => $config['ofpay_userid'],
            'userpws' => md5($config['ofpay_userpws']),
            'version' => '6.0'
        );
    }


    private static function _ofpayConfig() {
        $ofpayConfig = Common::getConfig('authConfig', 'ofpay');
        return $ofpayConfig;
    }

    /**
     * 处理虚拟物品的订单
     */

    public static function handleVirtualOrder($cid = 0, $order_sn = 0) {
        if (!intval($cid) || empty($order_sn)) return false;
        $cardMsg = self::get($cid);
        $result  = array();
        switch ($cardMsg['group_type']) {
            case '1':
                $result = Api_Ofpay_Recharge::recharge($order_sn); //充值
                break;
            case '2':
                $result = Api_Ofpay_Recharge::getCoupon($order_sn, $cardMsg); //购物券
                break;
            case '3':
                $result = Api_Ofpay_Recharge::getMobileFlow($order_sn, $cardMsg); //充手机流量包
                break;
            case '4':
                $result = Api_Ofpay_Recharge::QQCoinRecharge($order_sn, $cardMsg);//兑换Q币
                break;
            default:
                break;
        }


        return $result;
    }

}

?>