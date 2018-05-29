<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 账号设置会员中心首页
 * @author lichanghua
 *
 */
class AccountController extends Front_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/account/index',
		'userinfoUrl' => '/user/setting/userinfo',
		'orderlistUrl' => '/user/account/orderlist',
		'orderdetailUrl' => '/user/account/order_detail',
		'coindetaillUrl' => '/user/account/coin_detail',
		'wantUrl' => '/user/want/index',
		'settingUrl' => '/user/setting/index',
	);
	public $perpage = 6;
	
	/**
	 * 
	 * 首页
	 */
	public function indexAction() {
		$title = "个人中心";
		//用户信息
 		$this->assign('user_info',$this->userInfo);
 		$this->assign('title', $title);
    }
	   
	/**
	 *
	 * 订单列表
	 */
	public function order_listAction() {
		$this->checkRight();
		$title = "积分换购订单";
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$uids = array();

		list($total, $order) = Gc_Service_Order::getList($page, $this->perpage, array('uid'=>$this->userInfo['id']));
		
		$this->assign('order', $order);
		$this->assign('title', $title);

		
		$goods_ids = array();
		foreach($order as $key=>$value) {
			$goods_ids[] = $value['goods_id'];
		}
		
		if (count($goods_ids)) {
			$goods = Gc_Service_LocalGoods::getLocalGoodsByIds(array_unique($goods_ids));
			$goods = Common::resetKey($goods, 'id');
			$this->assign('goods', $goods);
		}
	}
	
	public function goldAction() {
		$title = "我的金币";
		$this->assign('title', $title);
	}
	
	public function silverAction(){
		$title = "我的银币";
		$this->assign('title', $title);
	}
	
	/**
	 *
	 * 订单详情
	 */
	public function order_detailAction() {
		$title = "积分换购订单详情";
		$this->assign('title', $title);
		
		$id = $this->getInput('id');
		
		$order = Gc_Service_Order::getOrder($id);
		if ($this->userInfo['id'] != $order['uid']) {
			$webroot = Yaf_Application::app()->getConfig()->webroot;
			$this->redirect($webroot.'/user/account/index');
		}
		
		$result = Api_Gionee_Pay::getOrder(array('order_no'=>$order['out_trade_no']));
		if ($result['process_status'] == 3 && $order['status'] == 1) {
			$ret = Gc_Service_Order::updateByOutTradeNo(array('status'=>2, 'pay_time'=>Common::getTime()), $order['out_trade_no']);
			if ($ret) $order['status'] = 2;
		}
		$this->assign('order', $order);
		
		
		
		$address = Gc_Service_Order::getOrderAddress($order['id']);
		
		$goods = Gc_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$this->assign('address', $address);
		$this->assign('goods', $goods);
	}
	
	
	/**
	 *
	 * 积分详情
	 */
	public function coin_logAction() {
		$this->checkRight();
		$title = "积分流通记录";
		$this->assign('title', $title);
		
		$page = intval($this->getInput('page'));	
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		$params = array(
				"out_uid"=>$this->userInfo['out_uid'],
				"coin_type"=>'2',
				"limit"=>'20',
				"page_no"=>'1'
		);
		$result = Api_Gionee_Pay::coinLog($params);
		$this->assign('logs', $result['data']['list']);
	}
}