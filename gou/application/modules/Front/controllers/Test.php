<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TestController extends Front_BaseController {
	
	/**
	 * order detail
	 */
	public function indexAction() {
	    
		$params = array(
				"trade_no"=>Gou_Service_Order::getTradeNo(),
				"subject"=>'测试标题',
		       "total_fee"=>'0.01',
				"call_back_url"=>'http://gou.3gtest.gionee.com/test/callback',
		       "notify_url"=>'http://gou.3gtest.gionee.com/Alipaynotify/index',
		);
		$api = new Api_Alipay_Pay();
		$request_token = $api->getRequestToken($params);
		if($request_token) {
		    $request_url = $api->getPayUrl($request_token);
		    $this->redirect($request_url);
		    exit;
		}
        return false;		
	}
	
	/**
	 * taoke
	 */
	public function taokeAction() {
	    $topApi  = new Api_Top_Service();
	    $ret = $topApi->getTaobaokeShops(array('keyword'=>'包包'));
	    exit;
	}
	
	/**
	 * notify
	 */
	public function notifyAction() {
	    $params = array (
          'service' => 'alipay.wap.trade.create.direct',
          'sec_id' => '0001',
          'v' => '1.0',
          'sign' => 'YTlf7Yn2xmwBODxAg33Jdk7kbhw9glfi+swtZAO/PPQkh/xnCtR5gc/s6erOcEWyBud/QrMOOcv8MOeUJmW6OyM+71a9etQ6uynx0IPsVsxlthSAcAxOzaX7IsPAh5JE9P5MyJd8yDROnxYl/b4xwPqKO0M9x3QZ6m0qoSsZu0c=',
          'notify_data' => 'YkwXmrkmwJ0IWTeBs16Am1IeRpLVKg9lQXYh4Sdwvv55Z72iBw+IMuNkmI6NLXcA1hvIh/D9V7vEK3A9l9RhxM0Jc8gX7GcMC40gVRbpu4mqvgvZ5MCpuuPL28kedEHH9i9zsseVR1N+bQbCUVCA+cuKrF/C2b/Qy1/L0I5/nxhs79fsr/JCVBBz2jpaEOm5UXWsZ4QctnRayj7VoIKHPCEkOW4zoPpEyBqcW5jIrbkcTbDi1VPjEu++mh4JQSUXwb1OarMH8RtSwVSoEybCA7+/1jqidIMweliEIuBpWh1EWBokFjpXcmZEBybFO7s7D42XKP24eRMcd94mAuE8i0Cw+fY2lo1yMcvoEH00fQQiR0YD9ykZsY44B8Rl+Mv8RQercFKo/c/Wd8yRlMelp2IbUGaGpyhv5mljfmVM6o0TifDE0p6PQzpKIiiRbOB6TZ36oEqLupnQ1rms/c137Zj7NDHNnp44f7jZP6lKXTHIxs8OZGKZpaQiqa9Sa182YQlpvbtjRjWnn6SIiX5OH3MokurXcBTwu0SNXWH/uQ6kNN8Kk/J3IbBzF0XcdIV8cqiiXDrnSeSU/CxUMP3XbltkdXdet4h/ECrCkYgHhuSFV+hX+07lktKLar5+z/qyJ7F4T2NgBwkqsMGWUBoZEKsv1TvQATHYYw7ya7JpEg8QOtqfcOLeQu61vi//VY1yXk9Sm1UJXesozk30hZb2/9CLuBzsKAQWo9rx1jvLYqacoIZvWvkK0ICWg8dZEwgvptLblOWMx82cjvaPllh+/vMun0I3PtHLGvKsMEpTV7rjYmvaH9LqX1ErvbOnP464+OIPLjlWTnzHGvOYHAYVRX5qJozjqpHcBm3I5HWqBDjeflJkMYDaUIKgUEUeZtHWKgQFdujjQYd/p1bntxxKbLN2fwFAHhS9Ua661aye6Qa7UQES/wl5Z9OPJmiO2uB1zKeii8k4ogg9KVx4I8EuJ7Oh2wySFVZNagvlDNuodmLSel6sbRBFHzhxwodtjEJCBfUTotj4HHr96m+UqTcezSEkr5KEVgj2Ppot7TLVUDz9QU6rbOoIDJMTOiEm+YRJW1ts7sPDF8AfoJy4KBi8hZkj3hlauVM4JNgYoZ3VCI1EOcJkR06/xf0tUDutZMjdJg3LV3E09JBOe/haVvP3i5N2YPz5of7IlXB1IuAOQueF0qiuo8wo7SKsWqGvxBbkGTYPkzzVpg6V8ipaDR5tlph0mILAhZfAPuhwwiAXcSOO09Kt1fG0TERaFXHimh+6asacH6KKZJcg3F7YMKiTR7WAR7/YJD/enSKMLSNUUN9VWZlIfYTeOdSS4Ga5BGNrRQIABWRUNcfch7a2R5Hc5A==',
          'payment_type' => '',
          'subject' => '',
          'buyer_email' => '',
          'gmt_create' => '',
          'notify_type' => '',
          'quantity' => '',
          'out_trade_no' => '',
          'notify_time' => '',
          'seller_id' => '',
          'trade_status' => '',
          'is_total_fee_adjust' => '',
          'total_fee' => '',
          'gmt_payment' => '',
          'seller_email' => '',
          'gmt_close' => '',
          'price' => '',
          'buyer_id' => '',
          'notify_id' => '',
          'use_coupon' => '',
          'refund_status' => '',
          'gmt_refund' => '',
        );
	    
	    $api = new Api_Alipay_Notify();
	    $verify = $api->verifyNotify($params);
	    print_r($verify);die;
	    if($verify) {
	        $notify_data = $api->parseNotifyData($params['notify_data']);
	        
	        //处理订单业务
	    }
	    exit('fail');
	}
	
	
	/**
	 * callback
	 */
	public function callbackAction() {
	    $params = $this->getInput(array('sign', 'result', 'out_trade_no', 'trade_no', 'request_token'));
	     
	    $api = new Api_Alipay_Notify();
	    $verify = $api->verifyReturn($params);
	    if($verify) {
	        print_r($verify);
	        //处理订单业务
	    }
	    print_r($params);die;
	}
	
	/**
	 * callback
	 */
	public function queryAction() {
	
	    $api = new Api_Alipay_Query();
	    $response = $api->queryOrder('20141226111511428314');
	    
	    print_r($response['trade_status']);die;
	}
	
	
	/**
	 * callback
	 */
	public function closeAction() {
	
	    $api = new Api_Alipay_Close();
	    $response = $api->closeOrder('20141225113559046076');
	    print_r($response);die;
	}
	
	
	/**
	 * 退款
	 */
	public function refundAction() {
	    $params = array(
    	    "refund_no"=>'201502020001',
	       "out_trade_no"=>'20150131173410641915',
    	    "refund_total"=>'0.01',
    	    "reason"=>'test',
	        "notify_url"=>'http://apk.gou.gionee.com/Alipaynotify/refund',
	    );
	    $api = new Api_Alipay_Refund();
	    $response = $api->refund($params);
	}
	
	
	/**
	 * notify
	 */
	public function refundnotifyAction() {
	    $params = $this->getPost('notify_time', 'notify_id', 'batch_no', 'success_num', 'sign', 'result_details',
	                                 'notify_type', 'sign_type');
	    if(!$params['sign'] || !$params['notify_data']) exit('fail');
	     
	    $api = new Api_Alipay_Notify();
	    $verify = $api->verifyRefundNotify($params);
	    if($verify) {
	         
	        //处理订单业务
	    }
	    exit('fail');
	}
	
	
}