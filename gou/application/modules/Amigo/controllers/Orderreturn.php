<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderreturnController extends Amigo_BaseController {
	
	/**
	 * search
	 */
	public function searchAction() {
		$this->assign('title', '退/换货_订单查询');
	}
	
	/**
	 * search
	 */
	public function searchListAction() {
		$mobile = $this->getPost('mobile');
		$buyer_name = $this->getPost('buyer_name');
		
		if($buyer_name && $mobile) {
			Util_Cookie::set('GOU_ORDERRETURN_SEARCH', json_encode(array('mobile'=>$mobile, 'buyer_name'=>$buyer_name)), true, Common::getTime() + 86400, '/', $this->getDomain());
		} else {
			$cookie_data = json_decode(Util_Cookie::get('GOU_ORDERRETURN_SEARCH', true), true);
			$mobile = $cookie_data['mobile'];
			$buyer_name = $cookie_data['buyer_name'];
		}
		
		$orders = array();
		if($mobile && $buyer_name) {
			list(,$orders) = Gou_Service_Order::getList(1, 50, array('phone'=>$mobile, 'buyer_name'=>$buyer_name, 'status'=>array('IN', array('4','5'))));
			
			if($orders) {
				foreach ($orders as $key=>$value) {
					$orders[$key]['order_token'] = self::createSign($value['id']);
					$address = Gou_Service_Order::getOrderAddress($value['id']);
					$orders[$key]['address'] = $address['adds'].$address['detail_address'];
				}
				$order_list = Common::resetKey($orders, 'id');
				$order_ids = array_keys($order_list);
				
				$order_list = Common::resetKey($orders, 'goods_id');
				$goods_ids = array_keys($order_list);
			}
			
			if($goods_ids) {
				list(,$goods) = Gou_Service_LocalGoods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
				$goods = Common::resetKey($goods, 'id');
			}
		}
		
		$return_cookie_data = json_decode(Util_Cookie::get('GOU_ORDERRETURN', true), true);
		
		$this->assign('orders', $orders);
		$this->assign('goods', $goods);
		$this->assign('return_cookie_data', $return_cookie_data);
		$this->assign('title', '退/换货_订单列表');
	}
	
	/**
	 * step
	 */
	public function stepAction() {
		$parm = $this->getPost('parm');
		$parm = explode('|', $parm);
		$order_id = $parm[0];
		$order_token = $parm[1];
		
		if($order_id && $order_token) {
			Util_Cookie::set('GOU_ORDERRETURN', json_encode(array('order_id'=>$order_id, 'order_token'=>$order_token)), true, Common::getTime() + 86400, '/', $this->getDomain());
		} else {
			$cookie_data = json_decode(Util_Cookie::get('GOU_ORDERRETURN', true), true);
			$order_id = $cookie_data['order_id'];
			$order_token = $cookie_data['order_token'];
		}
		
		$webroot = COmmon::getWebRoot();
		if(!$order_id || !$order_token) $this->redirect($webroot.'/amigo/orderreturn/searchlist');
		if($order_token != self::createSign($order_id))  $this->redirect($webroot.'/amigo/orderreturn/searchlist');
		
		list(,$reason_t)  = Amigo_Service_Reason::getList(1, 20, array('status'=>'1', 'type'=>1));
		list(,$reason_h)  = Amigo_Service_Reason::getList(1, 20, array('status'=>'1', 'type'=>2));
		
		$this->assign('order_id', $order_id);
		$this->assign('order_token', $order_token);
		$this->assign('reason_t', $reason_t);
		$this->assign('reason_h', $reason_h);
		$this->assign('title', '退/换货');
	}
	
	/**
	 * submit
	 */
	public function submitAction() {
		$order_id = $this->getPost('order_id');
		$order_token = $this->getPost('order_token');
		//$phone = $this->getPost('phone');
		//$truename = $this->getPost('truename');
		$type_id = $this->getPost('type_id');
		$reason_id = $this->getPost('reason');
		$feedback = $this->getPost('gbook');
		
		$order = Gou_Service_Order::getOrder($order_id);
		
		//check
		if(!$order || !$order_id || !$order_token || $order_token != self::createSign($order_id)) $this->output(-1, '非法请求.');
		//if (!$truename) $this->output(-1, '请填写收货人姓名.');
		//if (!$phone) $this->output(-1, '请输入您的手机号码.');
		//if(!Common::checkMobile($phone))  $this->output(-1, '手机号码输入有误.');
		if(!$type_id)  $this->output(-1, '请选择退/换货类型.');
	
		$data = array(
				'truename'=>$order['buyer_name'],
				'phone'=>$order['phone'],
				'order_id'=>$order_id,
				'order_return_id'=>$type_id == 1 ? 'T'.$order['trade_no'] : 'H'.$order['trade_no'],
				'type_id'=>$type_id,
				'create_time'=>Common::getTime(),
				'reason_id'=>$reason_id,
				'feedback'=>$feedback,
				'status'=>$type_id == 1 ? 6 : 1,
		);
	
		$ret = Amigo_Service_Orderreturn::add($data);
		if (!$ret) $this->output(-1, '操作失败.');
		Gou_Service_Order::updateOrder(array('status'=>7), $order_id);
		$webroot = COmmon::getWebRoot();
		Util_Cookie::delete('GOU_ORDERRETURN');
		$this->output(0,'订单创建成功.', array('type'=>'redirect', 'url'=>$webroot.'/amigo/orderreturn/submitok/'));
	}
	
	
	/**
	 * submit ok
	 */
	public function submitokAction() {
		$this->assign('title', '退/换货');
	}
	
	/**
	 * rule
	 */
	public function ruleAction() {
		$this->assign('title', '退/换货规则');
	}
	
	/**
	 * 
	 * @param int $order_id
	 * @return string
	 */
	private function createSign($order_id) {
		return md5(Common::getConfig('siteConfig', 'secretKey').$order_id);
	}
}