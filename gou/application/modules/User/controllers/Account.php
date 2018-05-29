<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 账号设置，目前没有会员中心首页，暂以账户设置为会员中心首页
 * @author tiansh
 *
 */
class AccountController extends User_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/account/index',
		'settingUrl'=>'/user/setting/index',
		'moreUrl' => '/user/account/more',
		'getauthcodeUrl' => '/user/account/getauthcode',
		'getauthtokenUrl' => '/user/account/getauthtoken',
		'bindUrl' => '/user/account/bind',
		'addaddressUrl'=> '/user/address/add',
		'editaddressUrl'=> '/user/address/edit',
		'silverulUrl' => '/user/account/silverule',
		'goldrulUrl' => '/user/account/goldrule',
		'orderlistUrl' => '/user/account/order',
		'orderdetailUrl' => '/user/account/order_detail',
		'gainlUrl' => '/user/account/gain',
		'gain_postlUrl' => '/user/account/gain_post',
		'coinloglUrl'=>'/user/account/coin_log'
	);
	
	public $perpage = 10;
	
	public $sex = array(
			1 => '女',
			2 => '男'
	);
	
	/**
	 * 
	 * 首页
	 */
	public function indexAction() {	
		$this->checkRight();
 		$this->assign('user_info',$this->userInfo);
 		$show_coin_desc = $this->getInput("show_coin_desc");
 		
 		//coin log
 		$params = array(
 				"out_uid"=>$this->userInfo['out_uid'],
 				"coin_type"=>'2',
 				"limit"=>'20',
 				"page_no"=>'1'
 		);
 		$result = Api_Gionee_Pay::coinLog($params);
 		$this->assign('logs', $result['data']['list']);
 		
 		$webroot = Common::getWebRoot();
 		
 		$nav = array(
 			'1'=> array('selected'=>'selected', 'href'=>''),
 			'2'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/order_list"'),
 			'3'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/want/index"'),
 			'4'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/address/index"'),
 		);
 		$this->assign('nav', $nav);
 		$this->assign('show_coin_desc', $show_coin_desc);
 		
	}
	
	public function stateAction() {
		$this->assign('curPageUrl', urldecode($this->getInput('curPageUrl')));
	}
	
	/**
	 * 绑定淘宝账号获得code
	 */
	public function getauthcodeAction() {
		$webroot = Common::getWebRoot();
		$redirect_url = $webroot.'/user/account/getauthtoken';
		$topApi = new Api_Top_Service();
		$url = $topApi->getAuthUrl($redirect_url);
		$this->redirect($url);
	}
	
	public function verifyAction() {
		$token = $this->getPost('userToken');
		if (!$token) $this->output(-1, '参数错误.', $token);
		
		$rsa = new Util_Rsa();
		$pubFile = Common::getConfig('siteConfig', 'rsaPubFile');
		$params = $rsa->decrypt($token, $pubFile);
		$params = json_decode($params, true);
		
// 		Common::log(array($_POST['userToken'], $params), 'test.log');
		if (!is_array($params)) {
			$this->output(-1, '参数解析失败.');
		}
		/* if ($this->userInfo['out_uid'] != $params['out_uid']) {
			$this->output(-1, '非法用户.');
		} */
		$this->output(0, '', $params);
	}
	
	/**
	 * get access_token
	 */
	public function getauthtokenAction() {
		$code = $this->getInput("code");
		$user_info = $this->userInfo;
		$webroot = Common::getWebRoot();
		$redirect_url = $webroot.'/user/account/bind';
		$topApi = new Api_Top_Service();
		$result = $topApi->getAuthToken($code, $redirect_url);
		$ret = json_decode($result,true);
		$data = array(
				'taobao_nick'=>$ret['taobao_user_nick'],
				'taobao_session'=>$ret['access_token'],
				'taobao_refresh'=>$ret['refresh_token'],
				'taobao_mobile_token'=>$ret['mobile_token'],
				'taobao_refresh_time'=>Common::getTime() + $ret['expires_in'],
				'taobao_refresh_expires'=>$ret['re_expires_in']
		);
		Gou_Service_User::updateUser($data, $user_info['id']);
		$this->redirect($webroot.'/user/setting/index?t_bi='.$this->t_bi);
		
	}
	
	/* public function gainAction() {
		list(, $rule) = Activity_Service_Coin::getCanUses(0, 1);
		$this->assign('rule', $rule[0]);
		if($this->userInfo['isgain']) $this->redirect('/user/account/index?t_bi='.$this->t_bi); 
	}
	
	public function gain_postAction() {
		if (!$this->userInfo) $this->output(-1, '用户信息获取失败.');
		if ($this->userInfo['isgain']) $this->output(-1, '您已经领取过了，不能重新领取了.');
		$ret = Gou_Service_User::gain($this->userInfo['out_uid'], $this->userInfo['id']);
		if (!$ret) $this->output(-1, '领取银币失败.');
		
		$webroot = Common::getWebRoot();
		
		$refer = Util_Cookie::get('login_refer', true);
		
		//第一次登录特殊处理
		if($refer && strpos($refer, '/user/login/index')) {
			$refer = str_replace('/login/', '/account/', $refer);
			$refer = strpos($refer, '?') ? $refer.'&show_coin_desc=1' : $refer.'?show_coin_desc=1';
		}
		
		$url = $refer ? $refer :  $webroot.'/user/account/index?show_coin_desc=1&t_bi='.$this->t_bi;
		$this->output(0, '恭喜您，领取成功.', array('type'=>'redirect','url'=>$url));
	} */
	
	
	
	/**
	 *
	 * 订单列表
	 */
	public function order_listAction() {
		$this->checkRight();
		$this->assign('user_info',$this->userInfo);
		//订单数量
 		list($total_1, ) = Gou_Service_Order::getList(1, 1, array('uid'=>$this->userInfo['id'], 'order_type'=>1));
 		list($total_2, ) = Gou_Service_Order::getList(1, 1, array('uid'=>$this->userInfo['id'], 'order_type'=>2));
 		list($total_3, ) = Gou_Service_Order::getList(1, 1, array('uid'=>$this->userInfo['id'], 'order_type'=>3));
 		
 		$this->assign('total_1', $total_1);
 		$this->assign('total_2', $total_2);
 		$this->assign('total_3', $total_3);
 		
 		$webroot = Common::getWebRoot();
 			
 		$nav = array(
 				'1'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/index"'),
 				'2'=> array('selected'=>'selected', 'href'=>''),
 				'3'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/want/index"'),
 				'4'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/address/index"'),
 		);
 		$this->assign('nav', $nav);
 		$this->assign('title', '我的订单');
	}
	
	/**
	 *
	 * 订单列表
	 */
	public function orderAction() {
		$page = intval($this->getInput('page'));
		$tid = intval($this->getInput('tid'));
		if ($page < 1) $page = 1;
		$uids = array();
		$webroot = Common::getWebRoot();
		$attachPath = Common::getAttachPath();
		list($total, $order) = Gou_Service_Order::getList($page, $this->perpage, array('uid'=>$this->userInfo['id'], 'order_type'=>$tid));
	
		$goods_ids = array();
		foreach($order as $key=>$value) {
			$goods_ids[] = $value['goods_id'];
		}
	
		if (count($goods_ids)) {
			$goods = Gou_Service_LocalGoods::getLocalGoodsByIds(array_unique($goods_ids));
			$goods = Common::resetKey($goods, 'id');
			$this->assign('goods', $goods);
		}
		
		$tmp = array();
		foreach($order as $key=>$value) {
			$tmp[$key]['title'] = $goods[$value['goods_id']]['title'];
			$tmp[$key]['img'] =  $attachPath.$goods[$value['goods_id']]['img'];
			$tmp[$key]['href'] =  $webroot.$this->actions['orderdetailUrl'].'?id='.$value['id'];
			$tmp[$key]['price'] = $goods[$value['goods_id']]['price'];
			$tmp[$key]['quantity'] = $value['number'];
			$tmp[$key]['created'] = date('Y-m-d H:i:s',$value['create_time']);
			$tmp[$key]['status'] = Gou_Service_Order::orderStatus($value['status']);;
			$tmp[$key]['totalprice'] = $value['real_price'];
			$tmp[$key]['silver'] = $value['silver_coin'];
		}
        
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));	
	}
	
	public function silveruleAction(){
		
	}
	
	public function goldruleAction(){
	}
	
	/**
	 *
	 * 订单详情
	 */
	public function order_detailAction() {
		$title = "积分换购订单详情";
		$this->assign('title', $title);
	
		$id = $this->getInput('id');
		$out_order_no = $this->getInput('out_order_no');
	
		if ($id) {
			$order = Gou_Service_Order::getOrder($id);
		} else {
			$order = Gou_Service_Order::getByTradeNo($out_order_no);
		}
		if ($this->userInfo['id'] != $order['uid']) {
			$webroot = Common::getWebRoot();
			$this->redirect($webroot.'/user/account/index');
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

		$this->assign('order', $order);
		
		//阅读币订单
		if($order['order_type'] == 3 && $order['status'] == 5) {
			list(, $read_coins) = Gou_Service_ReadCoin::getList(1, $order['number'], array('order_id'=>$order['trade_no']));
			$this->assign('read_coins', $read_coins);
		}
	
		$address = Gou_Service_Order::getOrderAddress($order['id']);
	
		$goods = Gou_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$this->assign('address', $address);
		$this->assign('goods', $goods);
		$this->assign('title', '订单详情');
	}
    
	/**
	 *
	 * 积分详情
	 */
	public function coin_logAction() {
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
		$this->assign('title', '积分详情');
	}
}