<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class Vendor_Ofpay
 * 欧飞接口api
 */
class Vendor_Ofpay {
    private $_userid   = '';
    private $_userpwds = '';
    private $_version  = '';
    private $_api_url  = '';
    private $_ret_url  = '';
    private $_base     = array();
    private static  $_private_key_str ='';

    public function __construct() {
        $config              = Common::getConfig('authConfig', 'ofpay');
        $this->_userid       = $config['ofpay_userid'];
        $this->_userpwds     = md5($config['ofpay_userpws']);
        $this->_version      = '6.0';
        $this->_api_url      = $config['ofpay_url'];
        $this->_ofpay_keyStr = $config['ofpay_keyStr'];

        $this->_base = array(
            'userid'  => $this->_userid,
            'userpws' => $this->_userpwds,
            'version' => $this->_version
        );

        $this->_ret_url = sprintf('%s/api/recharge/response', Common::getCurHost());
    }


    /**
     * 根据SP订单号补发充值状态
     * 此接口用于没有接收到回调充值状态的情况下进行补发
     */
    public function  req_reissue($orderSN) {
        $tmpParams = array(
            'spbillid' => $orderSN
        );
        $params    = array_merge($this->_base, $tmpParams);
        return $this->_call('reissue', $params);
    }


    public function req_querycardinfo($cardId) {
        $rcKey = 'API_OFPAY_querycardinfo:' . $cardId;
        $ret   = Common::getCache()->get($rcKey);
        if (empty($ret)) {
            $ret = $this->_req_querycardinfo($cardId);
            Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
        }
        return $ret;
    }

    /**
     * 查询商品信息的同步接口
     */
    private function  _req_querycardinfo($cardId) {
        $tmpParams = array(
            'cardid' => $cardId,//需查询的商品编码
        );
        $params    = array_merge($this->_base, $tmpParams);
        return $this->_call('querycardinfo', $params);
    }

    /**
     * 商品价格查询接口
     */
    private function _req_queryprice($cardId) {
        $tmpParams = array(
            'cardid' => $cardId,//需查询的商品编码
        );
        $params    = array_merge($this->_base, $tmpParams);
        return $this->_call('queryprice', $params);

    }

    /**
     * 卡密商品库存查询接口
     */
    private function _req_queryleftcardnum($cardId) {
        $tmpParams = array(
            'cardid' => $cardId,//需查询的商品编码
        );
        $params    = array_merge($this->_base, $tmpParams);
        return $this->_call('queryleftcardnum', $params);
    }

    /**
     * 充值第三方处理接口
     *
     */
    public function  req_onlineorder($orderInfo, $out_cardid = 0) {
        $cardMsg = User_Service_CardInfo::get($orderInfo['card_id']);
        if (!empty($out_cardid)) {
            $cardMsg['card_id'] = $out_cardid;
        }

        $tmpParams         = array(
            'cardid'       => $cardMsg['card_id'],
            'cardnum'      => intval($orderInfo['order_amount']),
            'sporder_id'   => $orderInfo['order_sn'],
            'sporder_time' => date('YmdHis', $orderInfo['add_time']),
            'game_userid'  => $orderInfo['recharge_number'],
            'ret_url'      => $this->_ret_url
        );
        $params            = array_merge($this->_base, $tmpParams);
        $md5_arr           = array(
            $params['userid'],
            $params['userpws'],
            $params['cardid'],
            $params['cardnum'],
            $params['sporder_id'],
            $params['sporder_time'],
            $params['game_userid'],
            $this->_ofpay_keyStr,
        );
        $md5_str           = implode('', $md5_arr);
        $params['md5_str'] = strtoupper(md5($md5_str));
        $content           = $this->_call('onlineorder', $params);
        if (!empty($content['orderinfo']['orderid'])) {
            $options = array(
                'api_type' => '1',
                'order_sn' => $params['sporder_id'],
                'order_id' => $content['orderinfo']['orderid'],
                'add_time' => time(),
                'desc'     => json_encode($content),
                'status'   => $content['orderinfo']['game_state']
            );
            User_Service_Recharge::add($options);
        }
        return $content;
    }

    /**
     * 检测用户手机号是否能充值
     * 此接口用于查询手机号是否能充值，如果能就返回商品信息，不能就返回提示正中维护中
     */
    public function  req_telquery($mobile, $pervalue) {
        $tmpParams = array(
            'phoneno'  => $mobile,
            'pervalue' => intval($pervalue)
        );
        $params    = array_merge($this->_base, $tmpParams);
        return $this->_call('telquery', $params);
    }

    /**
     * 用户信息查询接口，用于查询SP用户的信用点余额
     */
    public function req_queryuserinfo() {
        $params = $this->_base;
        return $this->_call('queryuserinfo', $params);
    }

    /**
     * 提卡接口    此接口依据用户的请求返回卡号密码信息
     */
    public function req_order($orderInfo) {
        $cardMsg   = User_Service_CardInfo::get($orderInfo['card_id']);
        $tmpParams = array(
            'cardid'       => $cardMsg['card_id'],
            'sporder_id'   => $orderInfo['order_sn'],
            'cardnum'      => 1,
            'sporder_time' => date('YmdHis', $orderInfo['add_time']),
        );

        $params            = array_merge($this->_base, $tmpParams);
        $md5_arr           = array(
            $params['userid'],
            $params['userpws'],
            $params['cardid'],
            $params['cardnum'],
            $params['sporder_id'],
            $params['sporder_time'],
            $this->_ofpay_keyStr,
        );
        $md5_str           = implode('', $md5_arr);
        $params['md5_str'] = strtoupper(md5($md5_str));
        return $this->_call('order', $params);
    }

    /**
     * 流量充值接口
     */
    public function req_flowOrder($orderInfo) {
        $cardMsg = User_Service_CardInfo::get($orderInfo['card_id']);
        //参数构造
        $tmpParams        = array(
            'phoneno'         => trim($orderInfo['recharge_number']), //手机号
            'perValue'        => intval($orderInfo['order_amount']), //流量面值
            'flowValue'       => trim($cardMsg['ext']), //流量值
            'netType'         => '3G', //网络制式
            'range'           => 2, //1,省内 2，全国
            'effectStartTime' => 1, //生效时间 1 当天 2 次日 3 次月
            'effectTime'      => 1, //有效期 1:当月有效
            'sporderId'       => $orderInfo['order_sn'],//内部订单号
            'retUrl'          => $this->_ret_url, // 回调URL
        );
        $params           = array_merge($this->_base, $tmpParams);
        $md5_arr          = array(
            $params['userid'],
            $params['userpws'],
            $params['phoneno'],
            $params['perValue'],
            $params['flowValue'],
            $params['range'],
            $params['effectStartTime'],
            $params['effectTime'],
            $params['netType'],
            $params['sporderId'],
            $this->_ofpay_keyStr
        );
        $md5_str          = implode('', $md5_arr);
        $params['md5Str'] = strtoupper(md5($md5_str));
        return $this->_call('flowOrder', $params);

    }

    public function req_flowOrder_new($orderInfo){
   		 $params = self::_flowRechargeParams($orderInfo);
   		 $config = Common::getConfig('userConfig','gou_recharge_url');
   		 $baseUrl = $config[ENV];
   		 $ret = self::_curl($baseUrl, $params);
   		 return  json_decode($ret,true);
    }
    
    public function req_search_flow($orderSn){
    	$config = Common::getConfig('userConfig','gou_recharge_url');
    	$url = $config[ENV];
    	$params = self::_flowBaseParams('020012');
    	$body = array('order_id'=>$orderSn);
    	$params['sign'] = self::_sign($body);
    	$params['body'] 	= $body;
    	$data = Common::jsonEncode($params);
    	$ret = self::_curl($url, $data);
    	return json_decode($ret,true);
    }
    public  function flowRechargeResult($p=array()){
    	$url = self::_url();
    	$params = self::_flowBaseParams('020012');
    	$params['sign'] = self::_sign($p);
    	$params['body'] 	= $p;
    	$data = Common::jsonEncode($params);
    	$ret = self::_curl($url,$data );
    	return json_decode($ret,true);
    }
    
    private function _curl($url,$params){
    	$curl = curl_init();
    	curl_setopt($curl,CURLOPT_URL, $url);
    	curl_setopt($curl, CURLOPT_POST, 1);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    	$ret = curl_exec($curl);
    	curl_close($curl);
    	return $ret;
    }
    
    private function _url(){
    	$config = Common::getConfig('userConfig','gou_recharge_url');
    	$url = $config[ENV];
    	return $url;
    }
    
    static private function _flowRechargeParams($data) {
    	$cardInfo = User_Service_CardInfo::getCardInfo($data['card_id']);
    	$userInfo = Gionee_Service_User::getUser($data['uid']);
    	$acitivityNum = Gionee_Service_Config::getValue('user_flow_acitivity_num');
    	$params = self::_flowBaseParams('010003');
    	$body           = array(
    			'user_id'           => $data['uid'],
    			'user_type'         => '',
    			'phone_no'          => $userInfo['username'],
    			'charge_phone_no' => $data['recharge_number'],
    			'order_id'          => $data['order_sn'],
    			'subject'           => '',
    			'prod_id'           => '',
    			'prod_type'         => '1',
    			'prod_title'        => intval($cardInfo['card_value']).'M',
    			'activity_no'       => $acitivityNum,
    			'trans_type'        => 02, 
    			'prod_cnt'          => 1,
    	);
    	ksort($body);
    	$params['sign'] 	=self::_sign($body);
    	$params['body'] 	= $body;
    	return  COmmon::jsonEncode($params);
    }
    
    private function _flowBaseParams($transCode=0){
    	$params = array(
    			'trans_code'  =>$transCode,
    			'req_sys'     => '0007',
    			'req_channel' => '',
    			'req_date'    => date('Ymd', time()),
    			'req_time'    => date('YmdHis',time()),
    	);
    	return $params;
    }
    
    private function _sign($data){
    	$sign ='';
    	foreach ($data as $k=>$v){
    		$sign.="{$k}={$v}&";
    	}
    	$content 				= 	self::_getFlowPrivateKey();
    	$str = $sign.$content;
    	$sign	= md5($str);
    	return $sign;
    	
    }
    
    private function _getFlowPrivateKey(){
    			$filePath       			= Common::getConfig('siteConfig', 'dataPath');
    			$filename           	= $filePath.'flow_recharge_private_key.txt';
    			if(file_exists($filename) && is_readable($filename)){
    				self::$_private_key_str = file_get_contents($filename);
    			}
    		return self::$_private_key_str;
    }
    
    public function req_mobinfo($mobile) {
        $rcKey = 'API_OFPAY_mobinfo:' . $mobile;
        $ret   = Common::getCache()->get($rcKey);
        $ret = '';
        if (empty($ret)) {
            $ret = $this->_req_mobinfo($mobile);
            Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
        }
        return $ret;
    }


    private function _req_mobinfo($mobile) {
        $params  = array('mobilenum' => $mobile);
        $content = $this->_call('mobinfo', $params, 1);
        $mobinfo = iconv('gbk', 'utf-8', $content);
        $mobinfo = explode('|', $mobinfo);
        $k       = Util_String::substr($mobinfo[2], 0, 2);
        $platArr = array(
            '移动' => 10,
            '联通' => 11,
            '电信' => 12,
        );
        $plat_id = isset($platArr[$k]) ? $platArr[$k] : 0;
        return array('plat_id'=>$plat_id,'area'=>$mobinfo[1]);
    }

    public function _call($action, $params, $txt = false) {
        $url = sprintf('%s/%s.do?%s', $this->_api_url, $action, http_build_query($params));
        $ret = Common::getUrlContent($url);
        if (!$txt && !empty($ret)) {
            $ret = Util_XML2Array::createArray($ret);
        }
		error_log(json_encode($ret),3,'/tmp/rcharge_'.date('Ymd',time()));
        return $ret;
    }

}