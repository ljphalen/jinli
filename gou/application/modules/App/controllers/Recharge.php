<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RechargeController extends App_BaseController {
	
	/**
	 * recharge index
	 */
	public function indexAction() {
		$channel_id = intval($this->getInput('channel_id'));
		$recharge_channel = Recharge_Service_Channel::getChannel($channel_id);
		if($recharge_channel) {
			$this->assign('channel_id', $channel_id);
			Recharge_Service_Channel::updateTJ($channel_id);
		}
		list(,$prices) = Recharge_Service_Price::getsBy(array('status'=>1), array('sort'=>'DESC', 'id'=>'ASC'));
		$this->assign('prices', $prices);
		$this->assign('recharge_notice', Gou_Service_Config::getValue('recharge_notice'));
		$this->assign('title', '话费充值');
	}
	
	
	/**
	 * create 
	 */
	public function createAction() {
		$mobile = $this->getPost('phone');
		$cardnum = intval($this->getPost('cardnum'));
		$channel_id = intval($this->getPost('channel_id'));
		
		if(!$mobile) $this->output(-1, '请填写要充值的手机号码');
		if(!$cardnum) $this->output(-1, '请选择充值金额');
		
		list(,$list) = Recharge_Service_Price::getsBy(array('status'=>1), array('id'=>'DESC'));
		$list = Common::resetKey($list, 'price');
		$prices = array_keys($list);
		
		if(!in_array($cardnum, $prices)) $this->output(-1, '充值金额错误!');
		
		//查询归属地和价格
		$fix_phone = Util_String::substr($mobile, 0, 7);
		$ret = Api_Ofpay_Recharge::telQuery($mobile, $cardnum);
		if($ret['cardinfo']['retcode'] != 1)  $this->output(-1, '此手机号码暂不能充值!');
		
		$plat_id = Api_Ofpay_Recharge::mobPlat($fix_phone);
		$operator = Recharge_Service_Operator::getBy(array('pid'=>$list[$cardnum]['id'], 'operator'=>$plat_id));
			
		$price = $ret['cardinfo']['inprice'] + $operator['offset'];
	
		$data = array(
				'rec_cardnum'=>$cardnum,
				'mobile'=>$mobile,
				'pay_type'=>"1",
				'price'=>$price,
				'goods_title'=>$cardnum.'元充值卡',
				'channel_id'=>$channel_id,
		);
	
		$ret = Gou_Service_Order::recharge_order_create($data);
		//Common::getLockHandle()->unlock($lockName); //解锁
		if (!$ret) $this->output(-1, '创建订单失败.');
	
		list($order_id, $out_trade_no) = $ret;
		$webroot = Common::getWebRoot();
		$url = $webroot . '/recharge/pay?out_order_no=' . $out_trade_no;
		$this->output(0,'订单创建成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	/**
	 * pay
	 */
	public function payAction() {
		$out_order_no = $this->getInput('out_order_no');
		if (!$out_order_no) $this->output(-1, '支付失败.');
	
		$order = Gou_Service_Order::getByOutTradeNo($out_order_no);
		if (!$order) $this->output(-1, '支付失败.');
	
		$url = Api_Gionee_Pay::getPayUrl($order['out_trade_no']);
		$this->redirect($url);
		exit;
	}
	
	/**
	 * order result
	 */
	public function retAction() {
		$id = $this->getInput('id');
		$out_order_no = $this->getInput('out_order_no');
		$pay_mark = $this->getInput('pay_mark');
	
		if ($id) {
			$order = Gou_Service_Order::getOrder($id);
		} else {
			$order = Gou_Service_Order::getByTradeNo($out_order_no);
		}
	
		if($order['status'] == 1) {
			$result = Api_Gionee_Pay::getOrder(array('trade_no'=>$order['out_trade_no'], 'create_time'=>$order['create_time']));
			if($result && $result['process_status'] == 3) {
				$order['status'] = 2;
			}
		}
	
		//订单状态结果 1:订单提交成功；2订单支付成功；3订单支付失败
		$result = array('code'=>'1', 'msg'=>'订单提交成功');
		
		if($order['status'] == 2) {
			$result = array('code'=>'2', 'msg'=>'订单已支付');
		}else if($order['status'] == 1){
			$result = array('code'=>'3', 'msg'=>'订单未支付');
		}else if($order['status'] == 5){
			$result = array('code'=>'1', 'msg'=>'订单成功');
		}else if($order['status'] == 7){
			$result = array('code'=>'6', 'msg'=>'话费充值失败');
		}else {
			$result = array('code'=>'1', 'msg'=>'订单成功');
		}
	
		$this->assign('result', $result);
		$this->assign('order', $order);
		$this->assign('title', '订单状态');
	}
}
