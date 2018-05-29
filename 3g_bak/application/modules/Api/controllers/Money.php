<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MoneyController extends Api_BaseController {
	/**
	 * 充值服务器回调接口
	 */
	public function notifyAction(){
		$postData = $this->getInput(array('api_key','close_time','create_time','deal_price','out_order_no','pay_channel','submit_time','user_id','sign'));
		User_Service_UMoneyApiLog::add(array('add_time'=>time(),'type'=>3,'order_sn'=>$postData['out_order_no'], 'msg'=>json_encode($postData)));
		$orderInfo = User_Service_UMoneyOrder::getBy(array('order_sn'=>$postData['out_order_no']));
		if(empty($orderInfo)){
			exit('can not find the order !');
		}
		if($orderInfo['order_status'] ==1 || !empty($orderInfo['back_response_status'])){//不能重复提交
			exit('success');
		} 
		$arr = array(
			'submit_time' => $postData['submit_time'],
			'api_key'			=>$postData['api_key'],
			'out_order_no'=>$postData['out_order_no'],
		);
		$config = Common::getConfig('moneyConfig','money_recharge_config');
		$response = Common_Service_User::curl(Common::jsonEncode($arr), $config['product']['order_query_url']);
		User_Service_UMoneyApiLog::add(array('add_time'=>time(),'type'=>4,'order_sn'=>$postData['out_order_no'],'msg'=>json_encode($response)));
		if($response['process_status'] ==3){
			$params = array();
			$params['pay_channel'] = $postData['pay_channel'];
			$params['order_status'] = 1;
			$params['back_response_time'] = time();
			$params['back_response_status']  = 1;
			$params['expire_time'] = strtotime($postData['close_time']);
			$edit = User_Service_UMoneyOrder::edit($params, $orderInfo['id']);
			if($edit){
				User_Service_UMoney::verifyUserMoneyData($orderInfo['uid'],$orderInfo['deal_price']);
				$arr =array(
						'uid'=>$orderInfo['uid'],
						'classify'=>'501',
						'money'=>$orderInfo['deal_price'],
						'status'=>1
				);
				Common_Service_User::sendInnerMsg($arr,'user_money_recharge_tpl');
				if($orderInfo['order_type'] == 2){ //如果为畅聊充值时 ,则要送时长
					Gionee_Service_VoIPUser::upgradeToVipUser($orderInfo['uid']);
				}
			}
			exit('success');
		}else{
			exit('failed');
		}
	}

	/**
	 * 前台页面回调接口
	 */
	public function callbackAction(){
		$postData = $this->getInput(array('result','out_order_no','sign'));
		User_Service_UMoneyApiLog::add(array('add_time'=>time(),'type'=>2,'order_sn'=>$postData['out_order_no'], 'msg'=>json_encode($postData)));
		ksort($postData);
		 if(!$this->_signCheck($postData)){
			$this->output('-1','验证失败!');
		} 
		$order = User_Service_UMoneyOrder::getBy(array('order_sn'=>$postData['out_order_no']));
		if(empty($order)){
			$this->output('-1','订单不存在');
		}
		$params = array();
		$params['front_response_time'] = time();
		if($postData['result'] !=0){
			$params['order_status'] = $params['pay_status'] = $params['front_response_status'] = -1;
		}else{
			$params['pay_status'] = $params['front_response_status'] = 1;
			if($order['back_response_status'] ==1){
				$params['order_status'] = 1;
			}else{
				$params['order_status'] = 2;
			}

		}
		User_Service_UMoneyOrder::edit($params, $order['id']);
		$this->redirect(	Common::getCurHost().'/user/index/index');
	}
	
	
	
	private function _signCheck($data){
		$filePath = Common::getConfig('siteConfig','dataPath');
		$filename = $filePath.'rsa_public_key.pem';
		$str = '';
		foreach ($data as $k=>$v){
			if($k=='sign') continue;
			$str .="{$k}={$v}&";
		}
		$str = substr($str, 0,-1);
		$rsa = new Util_Rsa();
		$ret =  $rsa->verify($str, $data['sign'], $filename);
		return $ret;
	}
}