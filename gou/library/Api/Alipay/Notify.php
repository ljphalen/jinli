<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 支付宝接口 -- notify处理返回
 *
 * @author tiansh
 *
 */
class Api_Alipay_Notify extends Api_Alipay_Base {
    

    //notify url
    protected $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    protected $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	
	/**
	 * 针对return_url验证消息是否是支付宝发出的合法消息
	 * @return 验证结果
	 */
	public function verifyReturn($params){
	    if(!is_array($params)) return false;
        //生成签名结果
        $isSign = $this->getSignVeryfy($params, $params["sign"],true);
        if($isSign){
            return true;
        }
       return false;
	}
	
	
	/**
	 * 针对notify_url验证消息是否是支付宝发出的合法消息
	 * @return 验证结果
	 */
	public function verifyNotify($params){
        if(!is_array($params)) return false;
        //解密
        $notify_data = $this->rsaDecrypt($params['notify_data']);
        
        $params['notify_data'] = $notify_data;
        
        $notify_data = $this->parseNotifyData($params['notify_data']);
        $notify_id = $notify_data['notify_id'];
        
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        if (! empty($notify_id)) {$responseTxt = $this->getResponse($notify_id);}
    	
        //生成签名结果
        $isSign = $this->getSignVeryfy($params, $params["sign"],false);
        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if (preg_match("/true$/i",$responseTxt) && $isSign) {
            //return true;
            return $notify_data;
        } else {
            return false;
        }
	}
	
	/**
	 * 针对退款notify_url验证消息是否是支付宝发出的合法消息
	 * @return 验证结果
	 */
	public function verifyRefundNotify($params){
	    if(!is_array($params)) return false;
	    //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
	    $responseTxt = 'true';
	    if (! empty($params['notify_id'])) {
	        $responseTxt = $this->getResponse($params['notify_id']);
	    }
	    
	    //生成签名结果
	    $isSign = $this->getSignVeryfy($params, $params["sign"],true);
	    //验证
	    //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
	    //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
	    if (preg_match("/true$/i",$responseTxt) && $isSign) {
	        return true;
	    } else {
	        return false;
	    }
	}
	

	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @param $isSort 是否对待签名数组排序
	 * @return 签名验证结果
	 */
	function getSignVeryfy($para_temp, $sign, $isSort) {
	    if($para_temp['sign_type']) {
	        $sign_type = $para_temp['sign_type'];
	    } elseif ($para_temp['sec_id']) {
	        $sign_type = $para_temp['sec_id'];
	    } else {
	        $sign_type = $this->sec_id;
	    }
	    
	    //除去待签名参数数组中的空值和签名参数
	    $para = $this->paraFilter($para_temp);
	
	    //对待签名参数数组排序
	    if($isSort) {
	        $para = $this->argSort($para);
	    } else {
	        $para = $this->sortNotifyPara($para);
	    }
	
	    //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	    $prestr = $this->createLinkstring($para);
	    
	    $isSgin = false;
	    switch (strtoupper(trim($sign_type))) {
	        case "MD5" :
	            $isSgin = Api_Alipay_Md5::md5Verify($prestr, $sign, $this->key);
	            break;
	        case "RSA" :
	            $isSgin = Api_Alipay_Rsa::rsaVerify($prestr, trim($this->rsaPubFile), $sign);
	            break;
	        case "0001" :
	            $isSgin = Api_Alipay_Rsa::rsaVerify($prestr, trim($this->rsaPubFile), $sign);
	            break;
	        default :
	            $isSgin = false;
	    }
	    return $isSgin;
	}
	
	/**
	 * 异步通知时，对参数做固定排序
	 * @param $para 排序前的参数组
	 * @return 排序后的参数组
	 */
	function sortNotifyPara($para) {
	    $para_sort['service'] = $para['service'];
	    $para_sort['v'] = $para['v'];
	    $para_sort['sec_id'] = $para['sec_id'];
	    $para_sort['notify_data'] = $para['notify_data'];
	    return $para_sort;
	}
	
	/**
	 * 获取远程服务器ATN结果,验证返回URL
	 * @param $notify_id 通知校验ID
	 * @return 服务器ATN结果
	 * 验证结果集：
	 * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
	 * true 返回正确信息
	 * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
	 */
	function getResponse($notify_id) {
	    $transport = strtolower(trim($this->transport));
	    $partner = trim($this->partner);
	    $veryfy_url = '';
	    if($transport == 'https') {
	        $veryfy_url = $this->https_verify_url;
	    }else {
	        $veryfy_url = $this->http_verify_url;
	    }
	    $veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
	    $responseTxt = $this->getHttpResponseGET($veryfy_url, $this->cacertFile);
	
	    return $responseTxt;
	}
	
	
	
	
	/**
	 * 解析notify data
	 * @param unknown_type $notify_data
	 */
	public function parseNotifyData($notify_data) {
	    if(!$notify_data) return false;
	     
	    $doc = new DOMDocument();
	    $doc->loadXML($notify_data);
	    
	    //取xml值
	    $data = array();
	    if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
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
	        //通知时间
	        $data['notify_time'] = $doc->getElementsByTagName( "notify_time" )->item(0)->nodeValue;
	        //交易付款时间
	        $data['gmt_payment'] = $doc->getElementsByTagName( "gmt_payment" )->item(0)->nodeValue;
	        //交易关闭时间
	        $data['gmt_close'] = $doc->getElementsByTagName( "gmt_close" )->item(0)->nodeValue;
	        //通知校验ID
	        $data['notify_id'] = $doc->getElementsByTagName( "notify_id" )->item(0)->nodeValue;
	        //退款状态
	        $data['refund_status'] = $doc->getElementsByTagName( "refund_status" )->item(0)->nodeValue;
	        //退款时间
	        $data['gmt_refund'] = $doc->getElementsByTagName( "gmt_refund" )->item(0)->nodeValue;
	    }
	    
	    return $data;
	}
	
	
}