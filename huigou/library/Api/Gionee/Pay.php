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
	 * @param unknown_type $params
	 */
	public static function createOrder($data) {
		$params = array(
				'out_order_no'=>$data['out_order_no'],
				'user_id'=>$data['user_id'],
				'subject'=>$data['subject'],
				'consumed_rewards'=>$data['consumed_rewards'],
				'total_fee'=>$data['total_fee'],
				'submit_time'=>date('YmdHis', Common::getTime()),
				'deal_price'=>$data['deal_price'],
				'deliver_type'=>$data['deliver_type']
		);
		return self::_getResponse('/order/create', $params);
	}
	
	public static function getPayUrl() {
		
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 */
	public static function cancelOrder($data) {
		$params = array('order_no'=>$data['order_no']);
		return self::_getResponse('/order/cancel', $params);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 */
	public static function getOrder($data) {
		$params = array('order_no'=>$data['order_no']);
		return self::_getResponse('/order/query', $params);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 */
	public static function codOrder($data) {
		$params = array('order_no'=>$data['order_no']);
		return self::_getResponse('/order/cod', $params);
	}
	
	/**
	 * 
	 */
	public static function getCoin($data) {
		$params = array('out_uid'=>$data['out_uid']);
		return self::_getResponse('/rewards/query', $params);
	}
	
	/**
	 * 
	 * @param unknown_type $data
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
	 * @param unknown_type $data
	 * @return Ambigous <boolean, Ambigous, mixed>
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
	 * @param unknown_type $url
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, unknown, stdClass>
	 */
	private static function _getResponse($action, $params) {
		$appid = Common::getConfig('apiConfig', 'gionee_coin_appid');
	    $params['appid'] = $appid;
		list($url, $param) = self::_createSign($params);
		$response = Util_Http::post($url.$action, $param);
		Common::log(array($url.$action, $param, $response), 'response.log');
		if ($response->state !== 200) {
			Common::log(array($response->state, $response->data), 'pay.log');
			return false;
		}
		$ret = json_decode($response->data, true);
		if ($ret['status'] != 200) {
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