<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MallController extends Client_BaseController {
	
	private $goodId;
	private $puuid;
	private $serverId;
	private $imei;
	private $uname;
	private $exchangeNums;
	private $address;
	private $receiver;
	private $receiverphone;
	private $page;
	private $sp;
	public $perpage = 10;
	private $exchangeData = array();
	public $actions = array(
			'exchangeAcouponUrl' => '/client/Mall/exchangeAcoupon/',
			'exchangeEntityUrl' => '/client/Mall/exchangeEntity/',
			'exchangeOverUrl' => '/client/Mall/exchangeOver/',
	);
	
	//商品兑换列表
	public function indexAction(){
		$good = $this->getIndexInfo();
		$isLogin = $this->checkUserOnline();
		$this->assign('isLogin', $isLogin);
		$userInfo = self::getUserInfo(array('uuid' => $this->puuid));
		$this->assign('userInfo', $userInfo);
		
		$this->getGoodInput();
		if ($this->page < 1) $this->page = 1;
		list($total, $goods) = $this->listGoods($this->page);
		$hasnext = (ceil((int) $total / $this->perpage) - $this->page) > 0 ? true : false;
		$this->assign('total', $total);
		$this->assign('goods', $goods);
		$this->assign('puuid', $this->puuid);
		$this->assign('imei', $this->imei);
		$this->assign('uname', $this->uname);
		$this->assign('sp', $this->sp);
		$this->assign('serverId', $this->serverId);
		$this->assign('page', $this->page);
		$this->assign('hasnext', $hasnext);
		Common::addSEO($this,'积分商城');
	}
	
	public function moreAction(){
		$this->getGoodInput();
		$this->getExchangeData();
		if ($this->page < 1) $this->page = 1;
		list($total, $goods) = $this->listGoods($this->page);
		$hasnext = (ceil((int) $total / $this->perpage) - $this->page) > 0 ? true : false;
		$jsonData = $this->getIndexJsonGoods($goods);
		$this->output(0, '', array('list'=>$jsonData, 'hasNext'=>$hasnext, 'curPage'=>$this->page));
	}
	
	private function listGoods($page) {
		$search = array();
		$search['status'] = 1;
		$currentTime = strtotime(date('Y-m-d H:00:00'));
		$search['start_time'] = array('<=', $currentTime);
		$search['end_time']  = array('>=', $currentTime);
		$orderBy = array('create_time'=>'DESC','id' =>'DESC');
		list($total, $goods) = Mall_Service_Goods::getList($page, $this->perpage, $search, $orderBy);
		return array($total, $goods);
	}
	
	private function getIndexJsonGoods($data) {
		if(!$data) return '';
		$goods = array();
		$webroot = Common::getWebRoot();
		foreach($data as $key=>$value) {
		    $discount ='';
		    if($value['discountArr']){
		        $discountArr = json_decode($value['discountArr'],true);
		        $discountPoint =  round($value['consume_point'] * ($discountArr['discount'] / 10),0);
		        $discountPoint = ($discountPoint < 1 ? 1 : $discountPoint);
		        if($discountArr['discount_start_time'] <= Common::getTime() && $discountArr['discount_end_time'] > Common::getTime()){
		            $discount = $discountArr['discount'];
		        }
		        
		    }
		    
		    
			if($value['type'] == 1 && $value['total_num']) {
				$detailUrl = urldecode($webroot.$this->actions['exchangeEntityUrl']. '?goodId=' . $value['id'].'&puuid='.$this->exchangeData['uuid'].'&uname='.$this->exchangeData['uname'].'&imei='.$this->exchangeData['imei'].'&sp='.$this->exchangeData['sp'].'&serverId='.$this->exchangeData['serverId']);
			} else if($value['type'] == 2 && $value['total_num']){
				$detailUrl = urldecode($webroot.$this->actions['exchangeAcouponUrl']. '?goodId=' . $value['id'].'&puuid='.$this->exchangeData['uuid'].'&uname='.$this->exchangeData['uname'].'&imei='.$this->exchangeData['imei'].'&sp='.$this->exchangeData['sp'].'&serverId='.$this->exchangeData['serverId']);
			}
			$goods[] = array(
					'id'=>$value['id'],
					'consumePoints'=>$value['consume_point'],
					'remaindNum'=>$value['remaind_num'],
					'title'=>$value['title'],
					'img'=>urldecode(Common::getAttachPath().$value['icon']),
					'redirectLink' => $detailUrl,
			        'discount'=>$discount,
			        'enoughPoints'=>$discountPoint,
			);
		}
		return $goods;
	}
	
	//A券兑换详情页面
	public function exchangeAcouponAction(){
		list($good, $userInfo, $exchangeInfo, $logs, $isLogin) = $this->exchangeDetailData();
		$this->assign('serverId', $this->serverId);
		$this->assign('exchangeInfo', $exchangeInfo);
		$this->assign('good', $good);
		$this->assign('userInfo', $userInfo);
		$this->assign('logs', $logs);
		$this->assign('isLogin', $isLogin);
		Common::addSEO($this,$good['title']);
	}
	
	//实体兑换详情页面
	public function exchangeEntityAction(){
		list($good, $userInfo, $exchangeInfo, $logs, $isLogin) = $this->exchangeDetailData();
		$this->assign('exchangeInfo', $exchangeInfo);
		$this->assign('good', $good);
		$this->assign('userInfo', $userInfo);
		$this->assign('logs', $logs);
		$this->assign('isLogin', $isLogin);
		Common::addSEO($this,$good['title']);
	}
	
	//组装兑换请求数据
	public function exchangeDetailData(){
		$params = $logs = array();
		$good = $this->getGoodsInfo();
		$params['uuid'] = $this->puuid;
		$userInfo = $this->getUserInfo($params);
		$isLogin = $this->checkUserOnline();
		$exchangeInfo = Mall_Service_ExchangeGoods::countExchangeNums($this->exchangeData);
		//已经兑完
		if($good['remaind_num'] == 0) {
		    $logs = $this->exchangeOver();
		}
		return array($good, $userInfo, $exchangeInfo, $logs, $isLogin);
	}
	
	//商品兑完页面
	public function exchangeOver(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $logs) = $this->listexchangeGoods($page);
		$logs = $this->getJsonGoods($logs, $page);
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		return array($total, $logs, $hasnext, $page);
	}
	
	//商品兑完加载列表
	public function exchangeOverMoreAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $logs) = $this->listexchangeGoods($page);
		$hasNext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$jsonData = $this->getJsonGoods($logs, $page);
		$this->output(0, '', array('list'=>$jsonData, 'hasNext'=>$hasNext, 'curPage'=>$page, 'total'=>$total));
	}
	
	//A券商品兑换提交
	public function exchangeAcouponPostAction(){
		$this->getExchangeAcouponPostInput();
		$good = $this->getGoodsInfo();
		$exchangeResult = Mall_Service_ExchangeGoods::exchangeGoods($this->exchangeData);
		$this->handleExchangeResultData($exchangeResult);
	}
	
	//实体商品兑换提交
	public function exchangeEntityPostAction(){
		$this->getExchangeEntityPostInput();
		$this->checkExchangeEntityPostInput();
		$good = $this->getGoodsInfo();
		$this->exchangeData['address'] = $this->address;
		$this->exchangeData['receiver'] = $this->receiver;
		$this->exchangeData['receiverphone'] = $this->receiverphone;
		$exchangeResult = Mall_Service_ExchangeGoods::exchangeGoods($this->exchangeData);
		$this->handleExchangeResultData($exchangeResult);
	}
	
	//检查兑换商品传参
	public function getGoodsInfo() {
		$this->getGoodInput();
		$this->checkGoodInput();
		$this->getExchangeData();
		return Mall_Service_ExchangeGoods::checkIsGoods($this->goodId);
	}
	
	//检查商品列表传参
	public function getIndexInfo() {
		$this->getGoodInput();
		$this->checkIndexInput();
		$this->getExchangeData();
		return Mall_Service_ExchangeGoods::checkIsGoods($this->goodId);
	}
	
	//组装兑换传参数组
	private function getExchangeData() {
		$this->exchangeData['goodId'] = $this->goodId;
		$this->exchangeData['uuid'] = $this->puuid;
		$this->exchangeData['uname'] = $this->uname;
		$this->exchangeData['imei'] = $this->imei;
		$this->exchangeData['serverId'] = $this->getInput('serverId');
		$this->exchangeData['sp'] = $this->sp;
		$this->exchangeData['page'] = $this->getInput('page');
	}
	
	//获取商品传参
	private function getGoodInput() {
		$this->goodId = intval($this->getInput('goodId'));
		$this->puuid = $this->getInput('puuid');
		$this->uname = $this->getInput('uname');
		$this->imei = $this->getInput('imei');
		$this->serverId = $this->getInput('serverId');
		$this->sp = $this->getInput('sp');
		$this->page = $this->getInput('page');
	}
	
	//获取A券商品传参
	private function getExchangeAcouponPostInput() {
		$this->exchangeNums = intval($this->getInput('exchangeNums'));
	}
	
	//获取实体商品传参
	private function getExchangeEntityPostInput() {
		$this->address = $this->getInput('address');
		$this->receiver = $this->getInput('receiver');
		$this->receiverphone = $this->getInput('receiverphone');
	}
	
	//检查传参合法性
	private function checkGoodInput() {
		if (!$this->goodId || !$this->puuid || !$this->uname) {
			$this->output(-1, '非法请求');
		}
		
		$online = $this->checkUserOnline();
		if (!$online) {
			$this->output(0, '',array('isLogin'=>false));
		}
	}
	
	//首页加载检查传参合法性
	private function checkIndexInput() {
		if (!$this->puuid || !$this->uname) {
			$this->output(-1, '非法请求');
		}
	}
	
	//检查用户是否在线或存在
	private function checkUserOnline() {
		$isLogin = false;
		$imei = end(explode('_',$this->sp));
		$isLogin = Account_Service_User::checkOnline($this->puuid, $imei, 'uuid');
		if($isLogin)  $isLogin = true;
		return $isLogin;
	}
	
	
	//检查实体输入
	private function checkExchangeEntityPostInput() {
		if (!$this->address) {
			$this->output(-1, '收货人地址不能为空');
		}
		if (!$this->receiver) {
			$this->output(-1, '收货人姓名不能为空');
		}
		if (!$this->receiverphone) {
			$this->output(-1, '收货人电话不能为空');
		}
		if( $this->receiverphone && !preg_match('/^1[3458]\d{9}$/', $this->receiverphone) ){
			$this->output(-1, '收货人电话错误');
		}
	}
	
	//获取用户信息
	private function getUserInfo($params) {
		return Account_Service_User::getUserInfo($params);
	}
	
	//获取兑换记录列表
    private function listExchangeGoods($page) {
		$search = array();
		$search['mall_id'] = intval($this->getInput('goodId'));
		list($total, $logs) = Mall_Service_ExchangeLog::getList($page, $this->perpage, $search);
		return array($total, $logs);
	}
	
	//组装兑换记录json
	private function getJsonGoods($data, $page) {
		if(!$data) return '';
		$logs = array();
		foreach($data as $key=>$value) {
			$userInfo = self::getUserInfo(array('uuid'=>$value['uuid']));
			$nickname = $userInfo['nickname'];
			if($userInfo['nickname'] == $userInfo['uname']){ //如果昵称是手机号要打*替换
				$nickname = substr_replace($userInfo['nickname'],'*****',3,5);
			}
			$logs[] = array(
					'no'=>($key + 1) + ($page - 1) * $this->perpage,
					'nickname'=>$nickname,
			);
		}
		return $logs;
	}
	
	/**
	 * 处理兑奖提交返回的数据
	 */
	public function handleExchangeResultData($data){
		$error_data = array(
				'msg' => '',
				'isLogin' => true
		);
		$webroot = Common::getWebRoot();
		if(!is_array($data))  {
			switch ($data){
				case '2':
					$this->output(-1, '非法请求');
					break;
				case '3':
					$error_data['isLogin'] = false;
					$this->output(0, '未登陆', $error_data);
					break;
				case '4':
					$tmp['url'] = $webroot . '/Client/Mall/index/?puuid='.$this->puuid.'&uname='.$this->uname.'&sp='.$this->sp;;
					$tmp['exchangeStatus'] = 6;
					$this->output(0, '', $tmp);
					break;
				default:
					$this->output(-1, '非法请求');
					break;
	    	}
		}  else {
			$tmp = array();
			if($data['exchangeStatus'] == 1){ //兑换成功
				$tmp['exchangeStatus'] = 1;
			}
			$tmp = $data;
			$this->output(0, '', $tmp);
		}
 		
	}
}
