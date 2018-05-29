<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 支付宝接口 -- 支付
 *
 * @author tiansh
 *
 */
class Api_Alipay_Pay extends Api_Alipay_Base {
    
    /**
     * 构造方法
     */
    public function initParams() {
        $this->gatewayUrl = "http://wappaygw.alipay.com/service/rest.htm?";
        $this->sign_type = '0001';
    }
	/**
	 * get request token
	 */
	public function getRequestToken($params) {
	    $req_data = $this->getTokenReqData($params);
	    $para_temp['req_data'] = $req_data;
	    $para_temp['service'] = 'alipay.wap.trade.create.direct';
	    $para_temp["format"] = $this->format;
	    $para_temp["v"] = $this->apiVersion;
	    $para_temp["sec_id"] = $this->sec_id;
	    $para_temp["req_id"] = $this->req_id;
	    $para_temp["partner"] = $this->partner;
	    $para_temp['_input_charset'] = $this->_input_charset;
	    
	    $response = $this->buildRequestHttp($para_temp);
	    //URLDECODE返回的信息
	    $response = urldecode($response);
	    
	    //解析远程模拟提交后返回的信息
	    $para_response = $this->parseResponse($response);
	    
	    //获取request_token
	    $request_token = $para_response['request_token'];
	    
	    //logs
	    $log_data = array(
    	    'mark'=>$params['trade_no'],
    	    'api_type'=>'alipay',
    	    'url'=>"gettoken---".$this->gatewayUrl,
    	    'request'=>json_encode($para_temp),
    	    'response'=>$para_response['res_data'] ? $para_response['res_data'] : $para_response['res_error'],
    	    'create_time'=>Common::getTime()
	    );
	    // Gou_Service_ApiLog::add($log_data);
	    Common::getQueue()->push("api_log", $log_data);
	    
	    if($request_token) {
	        return $request_token;
	    }
	    return false;
	}
	
	
	/**
	 * create order
	 */
	public function getPayUrl($token) {
	    $req_data = $this->getOrderReqXmlData($token);
	    $params = array(
    	    "partner" => $this->partner,
    	    '_input_charset' => $this->_input_charset,
    	    'req_data' => $req_data,
    	    'service' => 'alipay.wap.auth.authAndExecute',
    	    "format" => $this->format,
    	    "v" => $this->apiVersion,
    	    "sec_id" => $this->sec_id,
    	    "req_id" => $this->req_id
	    );
	     
	    $request_url = $this->buildRequestParaToString($params);
	    return $this->gatewayUrl.$request_url;
	}
	
	
	/**
	 * get auto request data (xml)
	 * @param array $params
	 */
	public function getTokenReqData($params) {
	    $xml = '<direct_trade_create_req>';
            $xml .= '<subject>'.$params['subject'].'</subject>';
            $xml .= '<out_trade_no>'.$params['trade_no'].'</out_trade_no>';
            $xml .= '<total_fee>'.$params['total_fee'].'</total_fee>';
            $xml .= '<seller_account_name>'.$this->accountName.'</seller_account_name>';
            $xml .= '<call_back_url>'.$params['call_back_url'].'</call_back_url>';
            $xml .= '<notify_url>'.$params['notify_url'].'</notify_url>';
            if($params['out_user']) $xml .= '<out_user>'.$params['out_user'].'</out_user>';
            if($params['merchant_url']) $xml .= '<merchant_url>'.$params['merchant_url'].'</merchant_url>';
            if($params['pay_expire']) $xml .= '<pay_expire>'.$params['pay_expire'].'</pay_expire>';
            if($params['agent_id']) $xml .= '<agent_id>'.$params['agent_id'].'</agent_id>';
        $xml .= '</direct_trade_create_req>';
	    return $xml;
	}
	
	/**
	 * get auto request data (xml)
	 * @param unknown_type $params
	 */
	public function getOrderReqXmlData($token) {
	    $xml = '<auth_and_execute_req>';
	    $xml .= '<request_token>'.$token.'</request_token>';
	    $xml .= '</auth_and_execute_req>';
	    return $xml;
	}
	
}