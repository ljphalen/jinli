<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 支付宝接口  取消交易
 *
 * @author tiansh
 *
 */
class Api_Alipay_Close extends Api_Alipay_Base {
	
    /**
     * 构造方法
     */
    public function initParams() {
        $this->gatewayUrl = "https://mapi.alipay.com/gateway.do?";
        $this->sign_type = 'MD5';
    }
	/**
	 * get request token
	 */
	public function closeOrder($trade_no) {
	    $params = array(
    	    'service' => 'close_trade',
    	    "partner" => $this->partner,
	        '_input_charset' => $this->_input_charset,
    	    "sign_type" => $this->sign_type,
    	    "out_order_no" => $trade_no
	    );
	    
	    $response = $this->buildRequestHttp($params);
	    $ret = self::parseData($response);
	    
	    //logs
	    $log_data = array(
    	    'mark'=>$trade_no,
    	    'api_type'=>'alipay',
    	    'url'=>"close---".$this->gatewayUrl,
    	    'request'=>json_encode($params),
    	    'response'=>json_encode($ret),
    	    'create_time'=>Common::getTime()
	    );
	    // Gou_Service_ApiLog::add($log_data);
	    Common::getQueue()->push("api_log", $log_data);
	    
	    if($ret['is_success'] == 'T' || ($ret['is_success'] == 'F' && $ret['error'] == 'TRADE_NOT_EXIST')) {
	        return true;
	    }
	    return false;
	}
	
	
	/**
	 * 
	 * @param xml $query_data
	 * @return array
	 */
	public function parseData($res_data) {
	    if(!$res_data) return false;
	    
	    $doc = new DOMDocument();
	    $doc->loadXML($res_data);
	    
	    //取xml值
	    $data = array();
	    $data['is_success'] = $doc->getElementsByTagName( "is_success" )->item(0)->nodeValue;
	    if($data['is_success'] == 'F') {
	        $data['error'] = $doc->getElementsByTagName( "error" )->item(0)->nodeValue;
	    }
	    return $data;        
	}	
}