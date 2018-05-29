<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 支付宝接口
 *
 * @author tiansh
 *
 */
class Api_Alipay_Query extends Api_Alipay_Base {
	
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
	public function queryOrder($trade_no) {
	    $params = array(
    	    'service' => 'single_trade_query',
    	    "partner" => $this->partner,
	        '_input_charset' => $this->_input_charset,
    	    "sign_type" => $this->sign_type,
    	    "out_trade_no" => $trade_no
	    );
	    
	    $response = $this->buildRequestHttp($params);
	    $ret = self::parseQueryData($response);
	    
	    //logs
	    $log_data = array(
    	    'mark'=>$trade_no,
    	    'api_type'=>'alipay',
    	    'url'=>"query---".$this->gatewayUrl,
    	    'request'=>json_encode($params),
    	    'response'=>json_encode($ret),
    	    'create_time'=>Common::getTime()
	    );
	    // Gou_Service_ApiLog::add($log_data);
	    Common::getQueue()->push("api_log", $log_data);
	    
	    if($ret['out_trade_no']) {
	        return $ret;
	    }
	    return false;
	}
	
	
	/**
	 * 
	 * @param xml $query_data
	 * @return array
	 */
	public function parseQueryData($query_data) {
	    if(!$query_data) return false;
	    
	    $doc = new DOMDocument();
	    $doc->loadXML($query_data);
	    
	    //取xml值
	    $data = array();
	    if($doc->getElementsByTagName( "is_success" )->item(0)->nodeValue == 'T') {
	        //商户订单号
	        $data['out_trade_no'] = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
	        //支付宝交易号
	        $data['trade_no'] = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
	        //交易状态
	        $data['trade_status'] = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
	        //买家支付宝账号
	        $data['buyer_email'] = $doc->getElementsByTagName( "buyer_email" )->item(0)->nodeValue;
	        //交易创建时间
	        $data['gmt_create'] = $doc->getElementsByTagName( "gmt_create" )->item(0)->nodeValue;
	        //交易付款时间
	        $data['gmt_payment'] = $doc->getElementsByTagName( "gmt_payment" )->item(0)->nodeValue;
	        //交易关闭时间
	        $data['gmt_close'] = $doc->getElementsByTagName( "gmt_close" )->item(0)->nodeValue;
	        //退款状态
	        $data['refund_status'] = $doc->getElementsByTagName( "refund_status" )->item(0)->nodeValue;
	        //退款id
	        $data['refund_id '] = $doc->getElementsByTagName( "refund_id" )->item(0)->nodeValue;
	        //退款时间
	        $data['gmt_refund'] = $doc->getElementsByTagName( "gmt_refund" )->item(0)->nodeValue;
	         
	        return $data;
	    } else {
	        $data['error'] = $doc->getElementsByTagName( "error" )->item(0)->nodeValue;
	    }
	    return $data;        
	}	
}