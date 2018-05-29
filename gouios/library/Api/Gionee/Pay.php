<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * gionee pay api
 * @author rainkid
 *
 */
class Api_Gionee_Pay{
	
	/**
	 * 
	 * @param array $params
	 */
	public static function createOrder($data) {
		$params = array(
				'out_order_no'=>$data['out_order_no'],
				'user_id'=>$data['user_id'],
				'subject'=>$data['subject'],
				'consumed_rewards'=>$data['consumed_rewards'],
				'total_fee'=>$data['total_fee'],
				'submit_time'=>$data['submit_time'],
				'deal_price'=>$data['deal_price'],
				'deliver_type'=>$data['deliver_type'],
				'call_back_url'=>$data['call_back_url']
				
		);
		return self::_getResponse('/order/create', $params);
	}
	
	/**
	 * 
	 * @param array $params
	 * @return string
	 */
	public static function getPayUrl($params) {
		list($url, $file) = self::_getSignConfig();
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
		$rsa = new Util_Rsa();
		
		$string  = json_encode(array(
				'out_uid'=>$params['out_uid'],
				'out_order_no'=>$params['trade_no'],
				'app_id'=>$appid,
				'submit_time'=>$params['submit_time']));
		$token = $rsa->encrypt($string, $file);
		return sprintf("%s%s?out_order_no=%s&app_id=%s&user_token=%s&submit_time=%s", $url, "/order/wap/pay", $params['trade_no'], $appid, urlencode($token), $params['submit_time']);
	}
	
	/**
	 * 
	 * @param array $data
	 */
	public static function cancelOrder($data) {
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
		$params = array('out_order_no'=>$data['trade_no'], 'app_id'=>$appid, 'submit_time'=>date('YmdHis', $data['create_time']));
		return self::_getResponse('/order/cancel', $params);
	}
	
	/**
	 * 
	 * @param array $data
	 */
	public static function getOrder($data) {
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
		$params = array('out_order_no'=>$data['trade_no'], 'app_id'=>$appid, 'submit_time'=>date('YmdHis', $data['create_time']));
		return self::_getResponse('/order/query', $params);
	}
	
	/**
	 * 
	 * @param array $data
	 */
	public static function codOrder($data) {
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
		$params = array('out_order_no'=>$data['trade_no'], 'app_id'=>$appid, 'submit_time'=>date('YmdHis', $data['create_time']));
		return self::_getResponse('/order/cod', $params);
	}
	
	/**
	 * 
	 * @param array $data
	 * @return 
	 */
	public static function getCoin($data) {
		$params = array('out_uid'=>$data['out_uid']);
		return self::_getResponse('/rewards/query', $params);
	}
	
	/**
	 * 
	 * @param array $data
	 * @return array
	 */
	public static function coinLog($data) {
		$params = array(
				'coin_type'=>$data['coin_type'],
				'out_uid'=>$data['out_uid'],
				'limit'=>$data['limit'],
				'page_no'=>$data['page_no']
				);
		return self::_getResponse('/rewards/log', $params);
	}
	
	/**
	 * 
	 * @param array $data
	 * @return
	 */
	public static function coinAdd($data) {
		$params = array(
				'out_uid'=>$data['out_uid'],
				'coin_type'=>$data['coin_type'], 
				'coin'=>$data['coin'], 
				'msg'=>$data['msg']);
		return self::_getResponse('/rewards/add', $params);
	}
	/**
	 * 
	 * @param string $url
	 * @param array $params
	 * @return
	 */
	private static function _getResponse($action, $params) {
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
	    $params['app_id'] = $appid;
		list($url, $param) = self::_createSign($params);
		$response = Util_Http::post($url.$action, $param);
		if ($response->state !== 200) {
// 			Common::sms(Common::getConfig('siteConfig', 'mobiles'), '[警告]支付服务器出错,请及时处理');
			Common::log(array($url.$action, $param, $response), 'response.log');
// 			Common::log(array($response->state, $response->data), 'pay.log');
			return false;
		}
		$ret = json_decode($response->data, true);
		if ($ret['status'] != 200) {
//  			Common::sms(Common::getConfig('siteConfig', 'mobiles'), '[警告]支付服务器出错,请及时处理');
			Common::log(array($url.$action, $ret), 'pay.log');
		}
		return $ret;
	}
	
	/**
	 * create params
	 * @return array
	 */
	private static function _createSign($params) {
		list($url, $file) = self::_getSignConfig();
		$rsa = new Util_Rsa($params);
		$sign = $rsa->build_mysign($params, $file);
		$params['sign'] = $sign;
		return array($url, json_encode($params));
	}
	
	/**
	 * get configs
	 * @return array
	 */
	private static function _getSignConfig() {
		return array(
				Common::getConfig('apiConfig', 'gionee_pay_url'),
				Common::getConfig('siteConfig', 'rsaPemFile')
		);
	}
}