<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RechargeController extends Api_BaseController {
	
	
	/**
	 * get recharge info
	 */
	public function infoAction() {
		$info = $this->getInput(array('phone', 'cardnum'));
		
		if(!$info['cardnum']) $this->output(-1, '请选择充值金额!');
		
		list(,$list) = Recharge_Service_Price::getsBy(array('status'=>1), array('id'=>'DESC'));
		$list = Common::resetKey($list, 'price');
		$prices = array_keys($list);
		
		if(!in_array($info['cardnum'], $prices)) $this->output(-1, '充值金额错误!');		
		
		//tel query
		if($info['phone'] && (Util_String::strlen($info['phone']) >= 7)) {
			//查询归属地和价格
			$fix_phone = Util_String::substr($info['phone'], 0, 7);
			$ret = Api_Ofpay_Recharge::telQuery($info['phone'], $info['cardnum']);
			
			if($ret['cardinfo']['retcode'] != 1)  $this->output(-1, '此手机号码暂不能充值!', array('inprice'=>$list[$info['cardnum']]['range']));
			
			$plat_id = Api_Ofpay_Recharge::mobPlat($fix_phone);
			$operator = Recharge_Service_Operator::getBy(array('pid'=>$list[$info['cardnum']]['id'], 'operator'=>$plat_id));
			
			$card_info = array(
						'inprice'=>Common::money($ret['cardinfo']['inprice'] + $operator['offset']),
						'area'=>$ret['cardinfo']['game_area']
					);
			$this->output(0, '', $card_info);
		} else {
			$price = $list[$info['cardnum']];
			$card_info = array(
					'inprice'=>$price ? $price['range'] : '',
					'area'=>''
			);
			$this->output(-1, '', $card_info);
		}	
		
	}
	    
    /*
     * ret
     * 
     */
    public function retAction() {
    	$info = $this->getPost(array('ret_code', 'sporder_id', 'ordersuccesstime', 'err_msg'));
    	
    	$order = Gou_Service_Order::getByTradeNo($info['sporder_id']);
    	
    	if($order) {
    		
    		switch ($info['ret_code']){
    			case 1:
    				$order['status'] = 5;
    				break;
    			case 9:
    				$order['status'] = 7;
    				$info['ret_code'] = 2;
    				break;
    			case 0:
    				$info['ret_code'] = 3;
    				break;
    		}
    		
    		$order_data = array(
    				'rec_status'=>$info['ret_code'],
    				'rec_msg'=>$info['err_msg'],
    				'rec_order_time'=>strtotime($info['ordersuccesstime']),
    				'status'=>$order['status']
    		);
    			
    		$ret = Gou_Service_Order::updateOrder($order_data, $order['id']);
    		if (!$ret) Common::log('update rec_order status : ' . $order['trade_no'], 'notify.log');
    		
    		//logs
    		$log = array(
				'order_id'=>$order['id'],
				'order_type'=>1,
				'uid'=>0,
				'create_time'=>time(),
				'update_data'=>json_encode(array('status' => $order['status']))
			);
			Gou_Service_Order::addOrderLog($log);
			
			//api logs
			$webroot = Common::getWebRoot();
			$log_data = array(
					'mark'=>$order['trade_no'],
					'api_type'=>'recharge',
					'url'=>$webroot.'/api/recharge/ret',
					'request'=>json_encode($info),
					'response'=>$ret ? 'success' : 'fail',
					'create_time'=>Common::getTime()
			);
			Gou_Service_ApiLog::add($log_data);
				
			if($info['ret_code'] == 9) Common::sms('18312348022', '充值订单：'.$order['trade_no'].'(欧飞订单号：'.$order['rec_order_id'].')充值失败，请尽快处理！.');
    	}
    }
}