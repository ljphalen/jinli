<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MallController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
			'exchangeAcouponUrl' => '/client/Mall/exchangeAcoupon/',
			'exchangeEntityUrl' => '/client/Mall/exchangeEntity/',
			'exchangeOverUrl' => '/client/Mall/exchangeOver/',
	);
    
    /**
     * 积分商城物品列表
     */
    public function indexAction(){
    	$parmes = $this->getGoodInput();
		if ($parmes['page'] < 1) $parmes['page'] = 1;
		list($total, $goods) = $this->listGoods($parmes['page']);
		$hasnext = (ceil((int) $total / $this->perpage) - $parmes['page']) > 0 ? true : false;
        $jsonData = $this->getJsonGoods($goods, $parmes);
        $this->output(0, '', array('list'=>$jsonData, 'hasNext'=>$hasnext, 'curPage'=>$parmes['page']));
	}
	
	private function listGoods($page) {
		$search = array();
		$search['status'] = 1;
		$search['start_time'] = array('<=', Common::getTime());
		$search['end_time']  = array('>=', Common::getTime());
		$orderBy = array('create_time'=>'DESC','id' =>'DESC');
		list($total, $goods) = Mall_Service_Goods::getList($page, $this->perpage, $search, $orderBy);
		return array($total, $goods);
	}
    
	private function getJsonGoods($data, $parmes) {
		if(!$data) return '';
		$goods = array();
		$webroot = Common::getWebRoot();
		foreach($data as $key=>$value) {
			if($value['type'] == 1 && $value['total_num']) {
				$detailUrl = urldecode($webroot.$this->actions['exchangeEntityUrl']. '?goodId=' . $value['id'].'&puuid='.$parmes['puuid'].'&uname='.$parmes['uname'].'&sp='.$parmes['sp'].'&serverId='.$parmes['serverId']);
			} else if($value['type'] == 2 && $value['total_num']){
				$detailUrl = urldecode($webroot.$this->actions['exchangeAcouponUrl']. '?goodId=' . $value['id'].'&puuid='.$parmes['puuid'].'&uname='.$parmes['uname'].'&sp='.$parmes['sp'].'&serverId='.$parmes['serverId']);
			}
			$goods[] = array(
					'id'=>$value['id'],
					'consumePoints'=>$value['consume_point'],
					'remaindNum'=>$value['remaind_num'],
					'title'=>$value['title'],
					'img'=>urldecode(Common::getAttachPath().$value['icon']),
					'redirectLink' => $detailUrl
			);
		}
		return $goods;
	}
	
	private function getGoodInput() {
		$parmes = array();
		$parmes['page'] = intval($this->getInput('page'));
		$parmes['puuid'] = $this->getInput('puuid');
		$parmes['uname'] = $this->getInput('uname');
		$parmes['serverId'] = $this->getInput('serverId');
		$parmes['sp'] = $this->getInput('sp');
		return $parmes;
	}
	
	/**
	 * 积分兑换记录列表接口
	 * @author yinjiayan
	 */
	public function pointExchangeListAction() {
        $uuid = $this->getInput('puuid');
	    $this->checkOnline($uuid);
        $noSendCount = $this->queryNoSendCount($uuid);
        $reqPage = $this->getPageInput();
        list($total, $list, $page) = $this->queryExchangeList($uuid, $reqPage);
	    $data = array();
	    $data['list'] = $list;
	    $data['hasnext'] = $this->hasNext($total, $page);
	    $data['curpage'] = $page;
	    $data['totalCount'] = intval($total);
	    $data['noSendCount'] = $noSendCount;
	    $data['hasSendCount'] = $total - $noSendCount;
	    $data['isFirstPage'] = $reqPage == 1 ? true : false;
	    
	    if (1 == $reqPage) {
            $userInfo = Account_Service_User::getUserInfo(array('uuid' => $uuid));
            $data['receiveName'] = $userInfo['receiver'];
            $data['receivePhone'] = $userInfo['receiverphone'];
            $data['receiveAddress'] = $userInfo['address'];
	    }
	    $this->localOutput(0, '', $data);
	}
	
	private function queryNoSendCount($uuid) {
	    $params = array('uuid' => $uuid, 'status' => 0);
	    return intval(Mall_Service_ExchangeLog::getCount($params));
	}
	
	private function queryExchangeList($uuid, $reqPage) {
	    $params = array('uuid' => $uuid);
	    $orderBy = array('status'=>'', 'send_time'=>'DESC', 'id' => 'DESC');
	    $list = array();
	    do{
	       list($total, $srcData) = Mall_Service_ExchangeLog::getList($reqPage, self::PERPAGE, $params, $orderBy);
	       foreach ( $srcData as $value ) {
	       	   $info = $this->getPrizeName($value['mall_id']);
	           $dataItem['id'] = $value['id'];
	           $dataItem['prizeName'] = $info['title'];
	           $dataItem['prizeTime'] = $value['exchange_time'];
	           $dataItem['sendTime'] = $value['send_time'];
	           $dataItem['receiveName'] = $value['receiver'];
	           $dataItem['receivePhone'] = $value['receiverphone'];
	           $dataItem['receiveAddress'] = $value['address'];
	           $dataItem['hasSend'] = $value['status'] ? true : false;
	           $dataItem['type'] = $this->getGoodType($info);
	           $list[] = $dataItem;
	       }
	       
	       $hasnext = $this->hasNext($total, $reqPage);
	       if (!$hasnext) {
	           break;
	       }
	       
	       $lastItem = end($srcData);
	       $lastItemStatus = $lastItem['status'];
	       if (1 == $lastItemStatus) {
	           break;
	       }
	       
	       $reqPage ++;
	    } while (true);
	    
	    return array($total, $list, $reqPage);
	}
	
	private function getPrizeName($mallId) {
	   return Mall_Service_Goods::get($mallId);
	}
	
	private function getGoodType($good) {
	    if($good['type'] == Mall_Service_Goods::ENTITY){
	        $goodType = intval($good['type']);
	    } else {
	        $goodType = Mall_Service_Goods::ACOUPON;
	    }
	    return $goodType;
	}
	
	/**
	 * 积分兑换记录   收货地址补填接口
	 * @author yinjiayan
	 */
	public function pointExchangeUpdateAddressAction() {
	    $reqParams = $this->getInput(array('puuid', 'id', 'receiveName', 'receivePhone', 'receiveAddress'));
	    $this->checkOnline($reqParams['puuid']);
	    $this->checkReqParams($reqParams);
	     
	    $succeeUpdateLog = $this->updateExchangeLog($reqParams);
	    $succeeUpdateUserInfo = $this->updateUserInfo($reqParams);
	    if ($succeeUpdateLog && $succeeUpdateUserInfo) {
	        $this->localOutput(0);
	    } else {
	        $this->localOutput(1, '更新失败');
	    }
	}
	
	private function checkReqParams($reqParams) {
	    $uuid = $reqParams['puuid'];
	    $id = $reqParams['id'];
	    $address = $reqParams['receiveAddress'];
	    $receiver = $reqParams['receiveName'];
	    $receiverphone = $reqParams['receivePhone'];
	    if (!$uuid || !$id || !$address || !$receiver || !$receiverphone) {
	        $this->localOutput(1, '参数错误');
	    }
	}
	
	private function updateExchangeLog($reqParams) {
	    $data = array(
	                    'address' => $reqParams['receiveAddress'],
	                    'receiver' => $reqParams['receiveName'],
	                    'receiverphone' => $reqParams['receivePhone']
	    );
	    return Mall_Service_ExchangeLog::update($data, $reqParams['id']);
	}
	
	public function testAction() {
	    $reqParams = array(
	                    'receiveAddress' => '地址1',
	                    'receiveName' => '名字1',
	                    'receivePhone' => '电话1',
	                    'puuid' => 'F5795CCEED1E484CB64CA65FDF058C28',
	                    'id' => '110'
	    );
	    $succeeUpdateLog = $this->updateExchangeLog($reqParams);
	    $succeeUpdateUserInfo = $this->updateUserInfo($reqParams);
	    var_dump($reqParams);
	}
	
	private function updateUserInfo($params) {
	    $data = array(
	                    'address' => $params['receiveAddress'],
	                    'receiver' => $params['receiveName'],
	                    'receiverphone' => $params['receivePhone']
	    );
	    $prarms = array(
	                    'uuid' => $params['puuid']
	    );
	    return Account_Service_User::updateUserInfo($data, $prarms);
	}
}