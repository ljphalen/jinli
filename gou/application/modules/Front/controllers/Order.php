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
		
		$goods = Gou_Service_LocalGoods::getLocalGoods($id);
		$this->assign('goods', $goods);
		
		$address = Gou_Service_UserAddress::getDefaultAddress($this->userInfo['id']);
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
		$silver_coin = Common::money($this->getInput('silver_coin'));
		$goods_id = intval($this->getInput('goods_id'));
		$number = intval($this->getInput('number'));
		$phone = $this->getInput('phone');
		$phone_conf = $this->getInput('phone_conf');
		$gbook = $this->getInput('gbook');
		if(!$number) $number = 1;
		
		if (!$goods_id) $this->output(-1, '参数异常.');
		
		//if user not exist
		$user = Gou_Service_User::isLogin();
		if (!$user) $this->output(-1, '查询用户失败,用户信息不正确.');
		
		$coin = Api_Gionee_Pay::getCoin(array('out_uid'=>$user['out_uid']));
		if (!$coin || $coin['status'] != 200) $this->output(-1, '积分信息查询失败.');
		unset($coin['description']);
		
		//if goods not exist
		$goods = Gou_Service_LocalGoods::getLocalGoods($goods_id);
		if (!$goods) $this->output(-1, '创建订单失败,商品信息不存在.');
		
		if ($goods['status'] == 0) $this->assign(-1, '商品已经下架');
		
		if ($goods['start_time'] > Common::getTime()) $this->output(-1, '商品还未开始，不能购买.');

		//if goods is new user
		if($user['order_num'] > 1 && $goods['is_new_user'] == 1) $this->output(-1, '该产品是新人专供，你已经购买过了,请尝试其他产品，谢谢.');
		
		$total = Gou_Service_Order::userOrderCount($user['id'], $goods['id']);
		
		if (($total+$number) > $goods['limit_num']) $this->output(-1, '商品已达到最大限购数.');
		
		if (!$goods['stock_num']) $this->output(-1, '商品库存不足.');
		
		//if goods_type = 3 阅读币
		if ($goods['goods_type'] == 3) {
			list ($readcoin_total, ) = Gou_Service_ReadCoin::getCanUseReadcoin($goods['id']);
			if ($number > $readcoin_total)	$this->output(-1, '商品库存不足.');
		}
		
		if($goods['stock_num'] - $number < 0) $this->output(-1, '库存数为"'.$goods['stock_num'].'"，你购买的数量比库存还多，请选择正确的数量.');
		
		//if address not exist
		if($goods['goods_type'] == 1 || $goods['goods_type'] == 2) {
			if($goods['goods_type'] == 1) {
				if (!$phone) $this->output(-1, '请输入您的手机号码.');
				if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[02356789]{1}[0-9]{8}$||147[0-9]{8}$/",$phone)) $this->output(-1, '手机号码格式不正确.');
				if (!$phone_conf) $this->output(-1, '请再输入一次您的手机号码.');
				if ($phone != $phone_conf) $this->output(-1, '两次填写的手机号码不一致.');
			}else {
				$address = Gou_Service_UserAddress::getDefaultAddress($user['id']);
				if ($address['user_id'] != $user['id']) $this->output(-1, '请完善并确认收货地址信息.');
			}
		}
		
		if ($coin['silver_coin'] < $silver_coin) $this->output(-1, '用户银币余额不足.'); 
		
		if ($silver_coin > ($goods['silver_coin'] * $number)) $this->output(-1, '下单银币使用数量大于商品最多使用银币限制.'); 
		
		$lockName = 'LOCK_' . $this->userInfo['id'] . '_' . $goods['id'];
		if (Common::getLockHandle()->lock($lockName) === false) $this->output(-1, "创建订单失败.");
		
		$ret = Gou_Service_Order::create($user, $goods, $address, array('silver_coin'=>$silver_coin, 'number'=>$number, 'phone'=>$phone, 'gbook'=>$gbook));
		Common::getLockHandle()->unlock($lockName); //解锁
		if (!$ret) $this->output(-1, '创建订单失败.');
		
		list($order_id, $out_trade_no) = $ret;
		$webroot = Common::getWebRoot();
		if ($goods['iscash'] == 1 || (($goods['price'] * $number) == $silver_coin)) {
			$url = $webroot.'/user/account/order_detail?id='.$order_id;
		} else {
			$url = $webroot . '/order/pay?out_order_no=' . $out_trade_no;
		}
		$this->output(0,'订单创建成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	public function payAction() {
		$out_order_no = $this->getInput('out_order_no');
		if (!$out_order_no) $this->output(-1, '支付失败.');
		
		$order = Gou_Service_Order::getByOutTradeNo($out_order_no);
		if (!$order) $this->output(-1, '支付失败.');
		
		$pay_url = Api_Gionee_Pay::getPayUrl(array(
					'out_uid'=>$this->userInfo['out_uid'],
					'trade_no'=>$order['trade_no'],
					'submit_time'=>date('YmdHis', $order['create_time']),
				));
		$this->redirect($pay_url);
	}
	
	public function testAction() {
		$rsa = new Util_Rsa();
		$params = array('a'=>1);
		
		$pri  = Common::getConfig('siteConfig', 'rsaPemFile');
		$pub  = Common::getConfig('siteConfig', 'rsaPubFile');
		
		
		$sign = $rsa->build_mysign($params, $pri);
		
		$verify = $rsa->verifySign($params, $sign, $pub);
		
		if ($verify) {
// 			print_r("aaaaaaaaaaaaaa");
		}
		$d = '{"out_uid":"8BFA8F255A4A4B12ADF67CB7EF898BCC","trade_no":"20121214174349294153","app_id":102,"submit_time":"19700101080000"}';
		$a = $rsa->encrypt($d, $pri);
		print_r($a);
		
		$content = $rsa->decrypt($a, $pub);
		exit($content);
	}
	
	/**
	 * pay
	 */
	public function testPayAction() {
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
	
	public function testPay1Action() {
		$order_id = "20121205192426948552";
		$this->redirect($webroot . '/order/get_token?id=' . $order_id);
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