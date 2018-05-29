<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MallController extends Client_BaseController {
	
	private $goodId;
	private $categoryId;
	private $puuid;
	private $serverId;
	private $imei;
	private $uname;
	private $exchangeNums;
	private $address;
	private $receiver;
	private $receiverphone;
	private $qq;
	private $page;
	private $sp;
	public $perpage = 10;
	private $exchangeData = array();
	public $actions = array(
			'exchangeAcouponUrl' => '/client/Mall/exchangeAcoupon/',
			'exchangeEntityUrl' => '/client/Mall/exchangeEntity/',
			'exchangeOverUrl' => '/client/Mall/exchangeOver/',
	        'exchangeDetailUrl' => '/client/Mall/exchangeDetail/',
	        'seckillDetailUrl' => '/client/Mall/seckillDetail/',
	);
	
	//商品兑换列表
	public function indexAction(){
		$good = $this->getIndexInfo();
		$isLogin = $this->checkUserOnline();
		$this->assign('isLogin', $isLogin);
		$userInfo = self::getUserInfo(array('uuid' => $this->puuid));
		$this->assign('userInfo', $userInfo);
		$this->getGoodInput();
		
		$parmes = $this->getParmes();
		$seckillGoods = $this->getIndexSeckillGoods();
		$commonGoods = $this->getCommonlistGoods();
		
		$this->assign('seckillGoods', $seckillGoods);
		$this->assign('commonGoods', $commonGoods);
		$this->assign('parmes', $parmes);
		Common::addSEO($this,'积分商城');
	}
	
	private function getParmes() {
	    $parmes['categoryId']  = $this->categoryId;
	    $parmes['puuid'] = $this->puuid;
	    $parmes['imei'] = $this->imei;
	    $parmes['uname'] = $this->uname;
	    $parmes['sp'] = $this->sp;
	    $parmes['serverId'] = $this->serverId;
	    return $parmes;
	}
	
	private function getIndexSeckillGoods() {
	    $orderBy = array('create_time'=>'DESC','sort'=>'DESC');
	    $currentTime = Common::getTime();
	    $parmes['category_id'] =  Mall_Service_Goods::SECKILL_CATEGORY;
	    $parmes['status'] = Mall_Service_Goods::STATUS_OPEN;
	    $parmes['start_time'] = array('<=', $currentTime);
	    $parmes['end_time']  = array('>=', $currentTime);
	    $seckillGood =  Mall_Service_Goods::getBy($parmes, $orderBy);
	    if($seckillGood){
	        return $seckillGood;
	    }
	    $futureGood = $this->getSeckillGoods('future',1);
	    return $futureGood[0];
	}
	
	private function getCommonlistGoods() {
	    $goodList = array();
	    $categorys = $this->getCommonGoodsCategory();
	    if(!$categorys) return $goodList;
	    foreach($categorys as $key=>$value){
	        list($total, $goods) = $this->getlistGoodsByCatrgoryId($value['id']);
	        $goodList[] = array(
	                'title' => $value['title'],
	                'categoryId' => $value['id'],
	                'total' => $total,
	                'list' => $goods,
	        );
	    }
	    return $goodList;
	}
	
	private function getCommonGoodsCategory() {
	    $params['id'] = array('!=',Mall_Service_Goods::SECKILL_CATEGORY);
	    $params['status'] = Mall_Service_Category::STATUS_OPEN ;
	    return Mall_Service_Category::getsBy($params);
	}
	
	private function getlistGoodsByCatrgoryId($catrgoryId) {
	    $total = 0;
	    $search = $goods= array();
	    $currentTime = Util_TimeConvert::floor(Common::getTime(), Util_TimeConvert::RADIX_HOUR);
	    $search['status'] = 1;
	    $search['category_id']  = $catrgoryId;
	    $search['start_time'] = array('<=', $currentTime);
	    $search['end_time']  = array('>=', $currentTime);
	    $orderBy = array('create_time'=>'DESC','id' =>'DESC');
	    list($total, $goods) = Mall_Service_Goods::getList($page, 6, $search, $orderBy);
	    return array($total, $goods);
	}
	
	public function seckillDetailAction(){
	    $params  = array();
	    $this->getGoodInput();
	    $this->getExchangeData();
	    $seckillGoods = $this->getGoodsInfo();
	    if(!is_array($seckillGoods)){
	        $seckillGoods = array();
	    }
	    
	    $params['uuid'] = $this->puuid;
	    $userInfo = $this->getUserInfo($params);
	    $isLogin = $this->checkUserOnline();
	
	    $pastGoods = $this->getSeckillGoods('past');
	    $futureGoods = $this->getSeckillGoods('future');
	    $exchangeInfo = Mall_Service_ExchangeGoods::countExchangeNums($this->exchangeData);
	    
	    $this->assign('pastGoods', $pastGoods);
	    $this->assign('futureGoods', $futureGoods);
	    $this->assign('seckillGoods', $seckillGoods);
	    $this->assign('exchangeData', $this->exchangeData);
	    $this->assign('isLogin', $isLogin);
	    $this->assign('userInfo', $userInfo);
	    $this->assign('exchangeInfo', $exchangeInfo);
	}
	
	private function getSeckillGoods($seckillType, $num=3){
	    $currentTime = Common::getTime();
	    $parmes['category_id'] =  Mall_Service_Goods::SECKILL_CATEGORY;
	    $parmes['status'] = Mall_Service_Goods::STATUS_OPEN;
	    if($seckillType == 'past'){
	        $orderBy = array('end_time'=>'DESC', 'id' => 'DESC');
	        $parmes['end_time'] = array('<', $currentTime);
	    } else if($seckillType == 'future'){
	        $orderBy = array('start_time'=>'ASC', 'id' => 'DESC');
	        $parmes['start_time'] = array('>', $currentTime);
	    }
	    list(,$goods) = Mall_Service_Goods::getList(1, $num, $parmes, $orderBy);
	    if($seckillType == 'past'){
	        $sortGoods = array();
	        for($i=count($goods); $i>0; $i--) {
	            $sortGoods[] = $goods[$i-1];
	        }
	        $goods = $sortGoods;
	    }
	    
	    return $goods;
	}
	
	public function goodListAction(){
	    $good = $this->getIndexInfo();
	    $isLogin = $this->checkUserOnline();
	    $this->assign('isLogin', $isLogin);
	    $userInfo = self::getUserInfo(array('uuid' => $this->puuid));
	    $this->assign('userInfo', $userInfo);
	    
	    $this->getGoodInput();
	    $parmes = $this->getParmes();
	    if ($this->page < 1) $this->page = 1;
	    list($total, $goods) = $this->listGoods($this->page);
	    $hasnext = (ceil((int) $total / $this->perpage) - $this->page) > 0 ? true : false;
	    $this->assign('categoryId', $this->categoryId);
	    $this->assign('total', $total);
	    $this->assign('goods', $goods);
	    $this->assign('parmes', $parmes);
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
		$categoryId = intval($this->getInput('categoryId'));
		$search['status'] = 1;
		$search['category_id'] = $categoryId;
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
		$parmes = $this->getParmes();
		$parmes['uuid'] = $parmes['puuid'];
		$webroot = Common::getWebRoot();
		$isLogin = $this->getInput('isLogin');
		foreach($data as $key=>$value) {
		    $listIsdiscount = '';
		    $listConsumePoint = '';
		    $listDiscountPoint = '';
			$listDiscountArr = array();
			$parmes['goodId'] = $value['id'];
			list($isCanExchange, $buttonCode, $buttonTitle) = Mall_Service_ExchangeGoods::exchangeInfo($parmes, $value);
 			list($listDetailUrl, $listDiscountPoint, $listDiscountArr) = Mall_Service_Goods::assembleGoodInfo($value, $parmes);
			$goodType = ($value['type'] == Mall_Service_Goods::ACOUPON ? 'virtual' : 'entity');
			
			if($value['discountArr'] && $listDiscountArr['discount_start_time'] <= Common::getTime() && $listDiscountArr['discount_end_time'] > Common::getTime()){
			    $listIsdiscount = 'true';
			    $listConsumePoint = Mall_Service_Goods::convertPointVal($listDiscountPoint);
			}
			
			$goods[] = array(
					'id'=>$value['id'],
					'consumePoints'=>Mall_Service_Goods::convertPointVal($value['consume_point']),
					'remaindNum'=>$value['remaind_num'],
					'title'=>$value['title'],
					'img'=>urldecode(Common::getAttachPath().$value['icon']),
					'redirectLink' => $listDetailUrl,
			        'discount'=>$listIsdiscount,
			        'goodType' =>$goodType,
			        'isLogin' =>$isLogin,
			        'isCanExchange' =>$buttonCode,
			        'enoughPoints'=>$listConsumePoint,
			);
		}
		return $goods;
	}
	
	//A券兑换详情页面
	public function exchangeDetailAction(){
		list($good, $userInfo, $exchangeInfo, $isLogin) = $this->exchangeDetailData();
		$this->assign('exchangeInfo', $exchangeInfo);
		$this->assign('exchangeData', $this->exchangeData);
		$this->assign('good', $good);
		$this->assign('userInfo', $userInfo);
		$this->assign('isLogin', $isLogin);
		Common::addSEO($this,$good['title']);
	}
	
	//实体兑换详情页面
	public function exchangeEntityAction(){
		list($good, $userInfo, $exchangeInfo, $isLogin) = $this->exchangeDetailData();
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
		return array($good, $userInfo, $exchangeInfo, $isLogin);
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
		$this->getGoodsInfo();
		$exchangeResult = Mall_Service_ExchangeGoods::exchangeGoods($this->exchangeData);
		$this->handleExchangeResultData($exchangeResult);
	}
	
	//实体商品兑换提交
	public function exchangeEntityPostAction(){
	    $isExchangeSucess = $this->getInput('isExchangeSucess');
	    $good = $this->getGoodsInfo();
		$exchangeResult = Mall_Service_ExchangeGoods::exchangeGoods($this->exchangeData);
		$this->handleExchangeResultData($exchangeResult);
	}
	
	public function exchangeAdressPostAction(){
	    $good = $this->getGoodsInfo();
        $this->getExchangeEntityPostInput();
        $this->checkExchangeEntityPostInput($good);
        $updateUser = array(
                'address'=>$this->address,
                'receiver'=>$this->receiver,
                'receiverphone'=>$this->receiverphone,
                'qq'=>$this->qq,
        );
        $exchangeLogRet = Mall_Service_ExchangeLog::updateBy($updateUser, array('uuid'=>$this->puuid,'mall_id'=>$good['id']));
        $userInfoRet = Account_Service_User::updateUserInfo($updateUser, array('uuid'=>$this->puuid));
        if(!$userInfoRet || !$exchangeLogRet){
            $data['msg'] = '地址填写失败,请检查提交信息是否正确';
            $this->output(-1, '',$data);
        }
        
        $data['msg'] = '地址填写成功';
        $this->output(0, '',$data);
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
		$this->exchangeData['categoryId'] = $this->categoryId;
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
		$this->categoryId = intval($this->getInput('categoryId'));
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
		$this->qq = intval($this->getInput('qq'));
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
	private function checkExchangeEntityPostInput($good) {
		if (!$this->address && $good['type'] == Mall_Service_Goods::ENTITY) {
			$this->output(-1, '收货人地址不能为空');
		}
		if (!$this->receiver) {
			$this->output(-1, '收货人姓名不能为空');
		}
		if (!$this->receiverphone) {
			$this->output(-1, '收货人电话不能为空');
		}
		if( $this->receiverphone && (!preg_match('/^1[3458]\d{9}$/', $this->receiverphone) && !preg_match('/^(0?(([1-9]\d)|([3-9]\d{2}))-?)?\d{7,8}$/', $this->receiverphone)) ){
			$this->output(-1, '收货人电话错误');
		}
		if (!$this->qq && $good['type'] == Mall_Service_Goods::PHONE_RECHARGE_CARD) {
		    $this->output(-1, 'QQ号码不能为空');
		}
		if($this->qq && $good['type'] == Mall_Service_Goods::PHONE_RECHARGE_CARD && !preg_match("/^[1-9]\d{5,11}$/",$this->qq) ) {
		    Common::alertMsg('请确保你输入的QQ号码正确');
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
				$tmp['code'] = $data['activation_code'];
				$tmp['msg'] = $data['msg'];
				$tmp['remainNum'] = Mall_Service_Goods::getRemainNum($data['goodId']);
			}
			$this->output(0, '', $tmp);
		}
 		
	}
}
