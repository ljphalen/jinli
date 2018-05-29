<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Api_Ofpay_Recharge {
	
	/**
	 * 用户信息查询接口,此接口可以查询到SP用户的信用点余额
	 * @return array
	 */
	public static function userInfo() {
		return self::_getResponse('queryuserinfo.do?', self::_baseParams());
	}
	
	
	/**
	 * 根据手机号和面值查询商品信息
	 * 此接口用于查询手机号是否能充值，如果能充值返回商品信息，不能充返回运营商维护
	 * 
	 * @param string $phone
	 * @param string $pervalue
	 */
	public static function telQuery($phone, $pervalue) {
		$params = array_merge(
				self::_baseParams(),
				array(
						'phoneno'=>$phone,
						'pervalue'=>$pervalue						
				)
		);
		return self::_getResponse('telquery.do?', $params);
	}
	
	/**
	 * 根据手机号查询运营商
	 *
	 * @param string $phone
	 */
	public static function mobPlat($phone) {
		$config = Common::getConfig('apiConfig');
		$url = $config['ofpay_url'].'mobinfo.do?'.http_build_query(array('mobilenum'=>$phone));
		$response = Util_Http::get($url);
		if ($response->state !== 200) {
			Common::log(array($url, $response), 'ofpay_response.log');
			return false;
		}
		$mobinfo = iconv('gbk', 'utf-8', $response->data);
		$mobinfo = explode('|', $mobinfo);
		$plat = Util_String::substr($mobinfo[2], 0, 2);
		$plat_id = 0;
		
		switch ($plat){
			case '移动':
				$plat_id = 1;
				break;
			case '联通':
				$plat_id = 2;
				break;
			case '电信':
				$plat_id = 3;
				break;
			default:
				$plat_id = 0;
		}
		
		return $plat_id;
	}
	
	/**
	 * 手机直充接口 此接口依据用户提供的请求为指定手机直接充值
	 * 
	 * @array $params
	 * @return
	 */
	public static function onlineOrder($data) {
		if(!is_array($data) || !$data['cardid'] || !$data['cardnum']  || !$data['order_id'] || !$data['create_time'] || !$data['phone'] || !$data['ret_url']) return false;
		$params = array_merge(
			self::_baseParams(),
			array(
					'cardid' => $data['cardid'],
					'cardnum' => $data['cardnum'],
					'sporder_id' => $data['order_id'],
					'sporder_time' => date('YmdHis', $data['create_time']),
					'game_userid' => $data['phone'],
					'ret_url'=>$data['ret_url']						
			)
		);
		$key_str = Common::getConfig('apiConfig', 'ofpay_keyStr');
		$md5_str = $params['userid'].$params['userpws'].$params['cardid'].$params['cardnum'].$params['sporder_id'].$params['sporder_time'].$params['game_userid'].$key_str;
		$params['md5_str'] = strtoupper(md5($md5_str));
		
		return self::_getResponse('onlineorder.do?', $params);
	}
	
	
	/**
	 * 对订单充值
	 * @param array $order
	 */
	public static function recharge($order_no) {
		$order = Gou_Service_Order::getByTradeNo($order_no);
		if (!$order) {
			Common::log('Error: error order_no:' . $order_no, 'recharge.log');
			//return false;
		}
		
		//查询归属地和价格
		$tel_ret = Api_Ofpay_Recharge::telQuery($order['phone'], $order['rec_cardnum']);
		
		if($tel_ret['cardinfo']['retcode'] == 1) {
			$data = array(
					'cardid'=>$tel_ret['cardinfo']['cardid'],
					'cardnum'=>$order['rec_cardnum'],
					'order_id'=>$order['trade_no'],
					'create_time'=>$order['create_time'],
					'phone'=>$order['phone'],
					'ret_url'=>Common::getWebRoot().'/api/recharge/ret'
			);
			$rec_ret = self::onlineOrder($data);
			$orderInfo = $rec_ret['orderinfo'];
			
			if($orderInfo['retcode'] == 9) {
				Gou_Service_Order::updateOrder(array('status'=>7, 'rec_status'=>2), $order['id']);
				Common::sms('18312348022', '充值订单：'.$order_no.'(欧飞订单号：'.$orderInfo['orderid'].')充值失败，请尽快处理！.');
				//return false;
			}
			
			
			//充值状态
			switch ($orderInfo['game_state']){
				case 1:
					$rec_status = 1;
					$order_status = 5;
					break;
				case 9:
					$rec_status = 2;
					$order_status = 7;
					break;
				case 0:
					$rec_status = 3;
					$order_status = $order['status'];
					break;
				default:
					$rec_status = $order['rec_status'];;
					$order_status = $order['status'];
			}
			
			$order_data = array(
				'rec_status'=>$rec_status,
				'status'=>$order_status,
				'rec_order_id'=>$orderInfo['orderid'],
				'rec_price'=>$orderInfo['ordercash']
			);
			
			$ret = Gou_Service_Order::updateOrder($order_data, $order['id']);
			if (!$ret) {
				Common::log('update rec_order status : ' . $order['trade_no'], 'recharge.log');
				//return false;
			}
		} else {
			Gou_Service_Order::updateOrder(array('status'=>7, 'rec_status'=>2), $order['id']);
			Common::log('无法充值 : ' . $order['trade_no'], 'recharge.log');
			Common::sms('18312348022', '充值订单：'.$order['trade_no'].'无法充值，请及时处理');
			//return false;
		}
	}
	
	/**
	 * 根据SP订单号查询充值状态
	 * 此接口用于查询订单的充值状态
	 *
	 * @param string $phone
	 * @param string $pervalue
	 */
	public static function query($order_id) {
		$params = array_merge(
				self::_baseParams(),
				array(
						'spbillid'=>$order_id
				)
		);
		$config = Common::getConfig('apiConfig');
		$url = $config['ofpay_url'].'api/query.do?'.http_build_query($params);
		$response = Util_Http::get($url);

		if ($response->state !== 200) {
			Common::log(array($url, $params, $response), 'ofpay_response.log');
			return false;
		}
		return $response->data;
	}
	
	
	/**
	 * 根据SP订单号补发充值状态
	 * 此接口用于没有接收到回调充值状态的情况下进行补发
	 *
	 * @param string $phone
	 * @param string $pervalue
	 */
	public static function reissue($order_id) {
		$params = array_merge(
				self::_baseParams(),
				array(
						'spbillid'=>$order_id
				)
		);
		return self::_getResponse('reissue.do?', $params);
	}
	
	/**
	 *
	 * @param string $url
	 * @param array $params
	 * @return
	 */
	private static function _getResponse($action, $params) {
		$config = Common::getConfig('apiConfig');
		$url = $config['ofpay_url'].$action.http_build_query($params);
		$response = Util_Http::get($url);
		
		if ($response->state !== 200) {
			Common::log(array($url.$action, $params, $response), 'ofpay_response.log');
			return false;
		}
		$ret = Util_XML2Array::createArray($response->data);
		
		//logs
		if($action == 'onlineorder.do?'){
			$log_data = array(
					'mark'=>$params['sporder_id'],
					'api_type'=>'recharge',
					'url'=>$config['ofpay_url'].$action,
					'request'=>http_build_query($params),
					'response'=>json_encode($ret),
					'create_time'=>Common::getTime()
			);
			// Gou_Service_ApiLog::add($log_data);
			Common::getQueue()->push("api_log", $log_data);
		}
		
		//print_r($ret);
		return $ret;
	}
	
	
	/**
	 * get base params
	 * @return array
	 */
	private static function _baseParams() {
		return array(
				'userid'=>Common::getConfig('apiConfig', 'ofpay_userid'),
				'userpws'=>md5(Common::getConfig('apiConfig', 'ofpay_userpws')),
				'version' => '6.0'
		);
	}
	
}
