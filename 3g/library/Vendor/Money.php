<?php

class Vendor_Money{
	
	public function submit($orderId){
		if(!intval($orderId)) return ;
		$order   = User_Service_UMoneyOrder::get($orderId);
		$config = Common::getConfig('moneyConfig' ,'money_recharge_config');
		$configData = $config['product'];
		$params = array();
		$params['out_order_no'] 		= $order['order_sn'];
		$params['subject'] 				= '金立浏览器';
		$params['deliver_type'] 		= $order['deliver_type'];
		$params['api_key'] 				= $configData['api_key'];
		$params['deal_price'] 			= $order['deal_price'];
		$params['total_fee'] 			= $order['deal_price'];
		$params['submit_time']		= date('YmdHis',time());
		$params['call_back_url'] 		= Common::getCurHost().'/api/money/callback';
		$params['notify_url'] 			= 	Common::getCurHost(). '/api/money/notify';
		ksort($params);
		$signData = '';
		foreach($params as $k=>$v){
			$signData .=$v;
		}
		$filePath = Common::getConfig('siteConfig','dataPath');
		$pem = $filePath.'rsa_private_online.pem';
		$rsa = new Util_Rsa();
		$sign = $rsa->sign($signData, $pem);
		$params['sign'] = $sign;
		$params['msg'] ='金立浏览器';
		$data =Common_Service_User::curl(Common::jsonEncode($params), $configData['create_order_url']);
		User_Service_UMoneyApiLog::add(array('add_time'=>time(),'type'=>1,'order_sn'=>$order['order_sn'], 'msg'=>json_encode($data)));
		return $data;
	}
	
	private function _curl($params,$url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$output  = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}