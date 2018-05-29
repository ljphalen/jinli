<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderController extends Amigo_BaseController {
	
	/**
	 * create order 
	 */
	public function createAction() {
		//get params;
		$cookie_data = json_decode(Util_Cookie::get('AMIGO_CART', true), true);
		if(!$cookie_data['goods_id'] || !$cookie_data['number']) $this->output(-1, '参数异常.');
		
		$mobile = $this->getPost('mobile');
		$buyer_name = $this->getPost('buyer_name');
		$province = $this->getPost('province');
		$city = $this->getPost('city');
		$country = $this->getPost('country');
		$detail_address = $this->getPost('detail_address');
		$postcode = $this->getPost('postcode');
		$gbook = $this->getPost('gbook');
		$pay_type = $this->getPost('pay_type');
		
		//if goods not exist
		$goods = Gou_Service_LocalGoods::getLocalGoods($cookie_data['goods_id']);
		if (!$goods) $this->output(-1, '创建订单失败,商品信息不存在.');		
		if ($goods['status'] == 0) $this->assign(-1, '商品已经下架');		
		if ($goods['start_time'] > Common::getTime()) $this->output(-1, '商品还未开始，不能购买.');
		if ($cookie_data['number'] > $goods['limit_num']) $this->output(-1, '商品已达到最大限购数.');
		
		//check
		if (!$buyer_name) $this->output(-1, '请填写收货人姓名.');
		if (!$mobile) $this->output(-1, '请输入您的手机号码.');
		if(!Common::checkMobile($mobile))  $this->output(-1, '手机号码输入有误.');
		if (!$province) $this->output(-1, '请选择省份.');
		if (!$city) $this->output(-1, '请选择城市.');
		if (!$country) $this->output(-1, '请选择区/县.');
		if (!$detail_address) $this->output(-1, '请填写街道地址.');
		if (!$postcode)  $this->output(-1, '请填写邮编.');
		if (Util_String::strlen($postcode) != 6 && is_int($postcode) == false)  $this->output(-1, '邮编填写错误.');
		if (!$pay_type) $this->output(-1, '请选择支付方式.');
		
		/*$lockName = 'LOCK_' . $this->userInfo['id'] . '_' . $goods['id'];
		if (Common::getLockHandle()->lock($lockName) === false) $this->output(-1, "创建订单失败.");*/
		
		$address = array(
			'buyer_name'=>$buyer_name,
			'mobile'=>$mobile,
			'province'=>$province,
			'city'=>$city,
			'country'=>$country,
			'detail_address'=>$detail_address,
			'postcode'=>$postcode,
		);
		
		$ret = Gou_Service_Order::amigo_order_create($goods, $address, array('number'=>$cookie_data['number'], 'gbook'=>$gbook, 'pay_type'=>$pay_type));
		//Common::getLockHandle()->unlock($lockName); //解锁
		if (!$ret) $this->output(-1, '创建订单失败.');

		list($order_id, $out_trade_no) = $ret;
		$webroot = Common::getWebRoot();
		if ($pay_type == 1) {
			$url = $webroot . '/amigo/order/pay?out_order_no=' . $out_trade_no;			
		} else {
			$url = $webroot.'/amigo/order/result?id='.$order_id;
		}
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
	public function resultAction() {
		$id = $this->getInput('id');
		$out_order_no = $this->getInput('out_order_no');
		$pay_mark = $this->getInput('pay_mark');
		
		if ($id) {
			$order = Gou_Service_Order::getOrder($id);
		} else {
			$order = Gou_Service_Order::getByTradeNo($out_order_no);
		}
		
		if($order['status'] == 1) {
			$ret = Gou_Service_Order::getOrderPayStatus($order['trade_no']);
			if($ret) {
				if($order['order_type'] == 3) {
					$order['status'] = 5;
				} else {
				    $order['status'] = 2;
				}
			}
		}
		
		//订单状态结果 1:订单提交成功；2订单支付成功；3订单支付失败
		$result = array('code'=>'1', 'msg'=>'提交成功');
		if($pay_mark) {
		/*if($order['status'] == 1) {
		    $result = array('code'=>'3', 'msg'=>'支付失败');
		} else {
		    $result = array('code'=>$order['status'], 'msg'=>Gou_Service_Order::orderStatus($order['status']));
		}*/
		
		    $result = array('code'=>$order['status'], 'msg'=>Gou_Service_Order::orderStatus($order['status']));
		}
			
	
		$this->assign('result', $result);
		$this->assign('order', $order);
		$this->assign('amigo_order_desc', Gou_Service_Config::getValue('amigo_order_desc'));
		$this->assign('title', '订单状态');
	}
	
	/**
	 * order detail 订单详情
	 */
	public function detailAction() {
		$trade_no = $this->getInput('trade_no');
	
		$order = Gou_Service_Order::getByTradeNo($trade_no);
	
		if($order['status'] == 1) {
			$ret = Gou_Service_Order::getOrderPayStatus($order['trade_no']);
			if($ret) {
				if($order['order_type'] == 3) {
					$order['status'] = 5;
				}
			}
		}
		
		$goods = Gou_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$order_address = Gou_Service_Order::getOrderAddress($order['id']);
		$this->assign('goods', $goods);
		$this->assign('order', $order);
		$this->assign('address', $order_address);
		$this->assign('title', '订单详情');
	}
	
	
	/**
	 * search
	 */
	public function searchAction() {
		$this->assign('title', '订单查询');
	}
	
	/**
	 * search
	 */
	public function searchlistAction() {
		$mobile = $this->getPost('mobile');
		$buyer_name = $this->getPost('buyer_name');
				
		if($buyer_name && $mobile) {
			Util_Cookie::set('GOU_ORDER_SEARCH', json_encode(array('mobile'=>$mobile, 'buyer_name'=>$buyer_name)), true, Common::getTime() + 86400, '/', $this->getDomain());
		} else {
			$cookie_data = json_decode(Util_Cookie::get('GOU_ORDER_SEARCH', true), true);
			$mobile = $cookie_data['mobile'];
			$buyer_name = $cookie_data['buyer_name'];
		}
		
		/*$orders = array();
		if($mobile && $buyer_name) {
			list(,$orders) = Gou_Service_Order::getList(1, 10, array('phone'=>$mobile, 'buyer_name'=>$buyer_name));
			
			if($orders) {
				$orders = Common::resetKey($orders, 'id');
				$order_ids = array_keys($orders);
				
				$orders = Common::resetKey($orders, 'goods_id');
				$goods_ids = array_keys($orders);
			}
			
			if($goods_ids) {
				list(,$goods) = Gou_Service_LocalGoods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
				$goods = Common::resetKey($goods, 'id');
				
				$order_address = Gou_Service_Order::getOrdersAddress($order_ids);
				$order_address = Common::resetKey($order_address, 'order_id');
			}
		}*/
		$webroot = Common::getWebRoot();
		$this->assign('dataUrl', $webroot.'/amigo/order/searchdata?mobile='.$mobile.'&buyer_name='.$buyer_name);
		$this->assign('title', '订单列表');
	}
	
	/**
	 * search
	 */
	public function searchDataAction() {
		$mobile = $this->getInput('mobile');
		$buyer_name = $this->getInput('buyer_name');
		$page = $this->getInput('page');
		if(!$page) $page = 1;
		$perpage = 10;
	
		$data = array();
		$webroot = Common::getWebRoot();
		if($mobile && $buyer_name) {
			list($total,$orders) = Gou_Service_Order::getList($page, $perpage, array('phone'=>$mobile, 'buyer_name'=>$buyer_name));
			
			if($orders) {
				$orders_list = Common::resetKey($orders, 'id');
				$order_ids = array_keys($orders_list);
				
				$orders_list = Common::resetKey($orders, 'goods_id');
				$goods_ids = array_keys($orders_list);
				
				if($goods_ids) {
					list(,$goods) = Gou_Service_LocalGoods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
					$goods = Common::resetKey($goods, 'id');
				
					$order_address = Gou_Service_Order::getOrdersAddress($order_ids);
					$order_address = Common::resetKey($order_address, 'order_id');
				}
				
				foreach ($orders as $key=>$value) {
					$data[$key]['img'] = Common::getAttachPath() .$goods[$value['goods_id']]['img'];
					$data[$key]['title'] = html_entity_decode($goods[$value['goods_id']]['title']);
					$data[$key]['price'] = $value['deal_price'];
					$address = Gou_Service_Order::getOrderAddress($value['id']);
					$data[$key]['address'] = $address['adds'].$address['detail_address'];
					$data[$key]['detail_url'] = $webroot.'/amigo/order/detail?trade_no='.$value['trade_no'];
					$data[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
				} 
			}			
			
		}
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}