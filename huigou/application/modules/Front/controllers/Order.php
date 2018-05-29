<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderController extends Front_BaseController {
	
	/**
	 * order detail
	 */
	public function detailAction() {
		$this->checkRight();
		$title = "产品购买";
		$id = intval($this->getInput('id'));
		
		$goods = Gc_Service_LocalGoods::getLocalGoods($id);
		$this->assign('goods', $goods);
		
		$address = Gc_Service_UserAddress::getDefaultAddress($this->userInfo['id']);
		$this->assign('address', $address);
		
		$coinInfo = Api_Gionee_Pay::getCoin(array('out_uid'=>$this->userInfo['out_uid']));
		$this->userInfo = array_merge($this->userInfo, $coinInfo);
		$this->assign('user', $this->userInfo);
		$this->assign('title', $title);
	}
	
	/**
	 * create order 
	 */
	public function createAction() {
		//get params;
		$silver_coin = Common::money($this->getPost('silver_coin'));
		$goods_id = intval($this->getPost('goods_id'));
		$number = intval($this->getPost('number'));
		
		if (!$goods_id) $this->output(-1, '参数异常.');
		
		//if user not exist
		$user = Gc_Service_User::isLogin();
		if (!$user) $this->output(-1, '查询用户失败,用户信息不正确.');
		
		$coin = Api_Gionee_Pay::getCoin(array('out_uid'=>$this->userInfo['out_uid']));
		if (!$coin) $this->output(-1, '积分信息查询失败.');
		
		$user = array_merge($user, $coin);

		//if goods not exist
		$goods = Gc_Service_LocalGoods::getLocalGoods($goods_id);
		if (!$goods) $this->output(-1, '创建订单失败,商品信息不存在.');
		
		//if goods is new user
		if($user['order_num'] > 1 && $goods['is_new_user'] == 1) $this->output(-1, '该产品是新人专供，你已经购买过了,请尝试其他产品，谢谢.');
		
		$total = Gc_Service_Order::userOrderCount($user['id'], $goods['id']);
		if (($total+$number) > $goods['limit_num']) $this->output(-1, '商品已达到最大限购数.');
		
		if (!$goods['stock_num']) $this->output(-1, '商品库存不足.');
		
		if($goods['stock_num'] - $number < 0) $this->output(-1, '库存数为"'.$goods['stock_num'].'"，你购买的数量比库存还多，请选择正确的数量.');
		
		//if address not exist
		$address = Gc_Service_UserAddress::getDefaultAddress($this->userInfo['id']);
		if (!address) $this->output(-1, '创建订单失败,用户收货地址出错.');
		
		if ($user['silver_coin'] < $silver_coin) $this->output(-1, '用户银币余额不足.'); 
		
		if ($silver_coin > ($goods['silver_coin'] * $number)) $this->output(-1, '下单银币使用数量大于商品最多使用银币限制.'); 
		
		$ret = Gc_Service_Order::create($user, $goods, $address, array('silver_coin'=>$silver_coin, 'number'=>$number));
		if (!$ret) $this->output(-1, '创建订单失败.');
		
		list($order_id, $out_trade_no) = $ret;
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->output(0, '订单创建成功.', array('iscash'=>$goods['iscash'] ,'out_trade_no'=>$out_trade_no, 'url'=>$webroot.'/user/account/order_detail?id='.$order_id));
	}
	
	public function payAction() {
		$trade_no = $this->getInput('trade_no');
		if (!$trade_no) $this->output(-1, '支付失败,订单号不正确.');
		if (!$this->userInfo) $this->output(-1, '支付失败,用户未登录.');
		$order = Gc_Service_Order::getByOutTradeNo($trade_no);
		if (!$order) $this->output(-1, '支付失败,订单不存在.');
		if ($order['out_uid'] != $this->userInfo['out_uid']) $this->output(-1, '订单支付失败.');
		if ($order['status'] > 1) $this->output(-1,'支付失败,订单已支付.');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$url = $webroot . '/order/detail/?id=' . $order['id'];
		$this->output(0, '', array('datatype'=>'redirect', 'url'=>$url));
	}
	
	/**
	 * pay
	 */
	public function testCreateAction() {
		$params = array(
				"out_order_no"=>'20121016160283022525',
				"user_id"=>'8BFA8F255A4A4B12ADF67CB7EF898BCC',
				"subject"=>'aaaaaa',
				"consumed_rewards"=>'1.00',
				"total_fee"=>'12.00',
				"deal_price"=>'13.00',
				"deliver_type"=>'1'
		);
		$ret = Api_Gionee_Pay::createOrder($params);
		print_r($ret);
		exit;
	}
	
	public function testPayAction() {
		$params = array(
					'out_order_no'=>'20121205192426948552',
					'app_id'=>'304984',
					'user_token'=>''
				);
	}
	
	public function cancelAction() {
		$ret = Api_Gionee_Pay::cancelOrder(array('order_no'=>'509113290cf25bc78e85f8d6'));
		print_r($ret);
		exit;
	}
	
	public function getAction() {
		$ret = Api_Gionee_Pay::getOrder(array('order_no'=>'508b512a0cf2b59341b5f9c8'));
		print_r($ret);
		exit;
	}
	
	public function codAction() {
		$ret = Api_Gionee_Pay::codOrder(array('order_no'=>'509119fe0cf25bc78e85f8e3'));
		print_r($ret);
		exit;
	}
	
	public function coinAction() {
		$params = array("out_uid"=>'8BFA8F255A4A4B12ADF67CB7EF898BCC');
		$ret = Api_Gionee_Pay::getCoin($params);
		print_r($ret);
		exit;
	}
	
	public function coinlogAction() {
		$params = array(
				"out_uid"=>'8BFA8F255A4A4B12ADF67CB7EF898BCC',
				"coin_type"=>'1',
				"limit"=>'20',
				"page_no"=>'1'
		);
		$ret = Api_Gionee_Pay::coinLog($params);
		print_r($ret);
		exit;
	}
	
	public function coinaddAction() {
		$params = array(
				'out_uid'=>'8BFA8F255A4A4B12ADF67CB7EF898BCC',
				'coin_type'=>'1', 
				'coin'=>'100.00', 
				'msg'=>"Test_add");
		$ret = Api_Gionee_Pay::coinAdd($params);
		print_r($ret);
		exit;
	}
}