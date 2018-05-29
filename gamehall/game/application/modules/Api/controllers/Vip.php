<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户vip
 * @author wupeng
 */
class VipController extends Api_BaseController {

    /**个人中心*/
	public function centerAction() {
        $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
        $vipLevel = $vipInfo['vipLevel'];
        $vipInfo["timeStamp"] = Common::getTime();
		$vipInfo["hasGift"] = false;
		$vipInfo["ATick"] = false;
		$flg = User_Service_VipFlg::getVipFlg($uuid);
		if($flg) {
		    if($flg[User_Service_VipFlg::F_GIFT] != $vipLevel) {
		        $vipInfo["hasGift"] = true;
		    }elseif($flg[User_Service_VipFlg::F_TICKET] != $vipLevel) {
		        if(User_Config_Vip::$vipTicket[$vipLevel] > 0) {
		            $vipInfo["ATick"] = true;
		        }
		    }
		}else{
		    $vipInfo["hasGift"] = true;
		}
		$data = array();
		$data['sign'] = 'GioneeGameHall';
		$data['msg'] = '';
		$data['success'] = true;
		$data['data'] = $vipInfo;
		$this->clientOutput($data);
	}

	/**会员等级*/
	public function levelAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_DESC);
	    $vipInfo["vipLevelDes"] = $vipDesc ? $vipDesc : "";
	    $vipInfo["items"] = $this->getLevelList();
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}
	
	private function getLevelList() {
	    $items = array();
	    foreach (User_Config_Vip::$vipExpr as $key => $value) {
	        $tmp = array();
	        if($key > 1) {
	            $nextValue = User_Config_Vip::$vipExpr[$key+1];
	        }
	        $tmp['vipLevel'] = $key;
	        $tmp['vipValue'] = $value . ($nextValue ? "-" . $nextValue : "");
	        $tmp['ATick'] = User_Config_Vip::$vipTicket[$key];
	        $items[] = $tmp;
	    }
	    return $items;
	}
    
	/**成长值明细*/
	public function valueAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_VALUE_DESC);
	    $vipInfo["vipValueDes"] = $vipDesc ? $vipDesc : "";
	    $webroot = Yaf_Application::app()->getConfig()->webroot;
	    $vipInfo["listGameUrl"] = $webroot . "/Api/Vip/valuePage";
	    
	    $valueList = $this->getVipValue($uuid, 1);
	    $vipInfo = array_merge($vipInfo, $valueList);
	    
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}

	public function valuePageAction() {
	    $uuid = $this->getInput('puuid');
	    $page = intval($this->getInput('page'));
	    if($page < 1) {
	        $page = 1;
	    }
	    $this->checkOnline($uuid);
	    $vipInfo = $this->getVipValue($uuid, $page);
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}
	
	private function getVipValue($uuid, $page) {
	    $list = array();
	    $searchParams = array(User_Service_VipExpr::F_UUID => $uuid);
	    $startTime = strtotime(date('Y-m-d', strtotime("-3 month")));
	    $searchParams[User_Service_VipExpr::F_CREATE_TIME] = array('>=', $startTime);
	    list($total, $exprList) = User_Service_VipExpr::getPageList($page, 10, $searchParams);
	    $hasnext = ceil((int) $total / 10) > $page ? true : false;
	    $size = count($exprList);
	    foreach ($exprList as $expr) {
	        $size--;
	        $tmp = array();
	        $tmp['vipValue'] = $expr[User_Service_VipExpr::F_ADD_EXPR];
	        $tmp['vipValue'] = floatval($tmp['vipValue']);
	        if($expr[User_Service_VipExpr::F_TYPE] == User_Service_VipExpr::TYPE_MONEY) {
	            $tmp['event'] = '消费' . $tmp['vipValue'] . 'A币';
	            $api_key = $expr[User_Service_VipExpr::F_LOGS];
	            $game = Resource_Service_Games::getBy(array('api_key' => $api_key));
	            if($game) {
	                $tmp['event'] = $game['name'] . $tmp['event'];
	            }
	        }else{
	            $tmp['event'] = '参加活动';
	        }
	        $tmp['vipValue'] = '+' . $tmp['vipValue'] . '成长值';
	        $tmp['timeStamp'] = $expr[User_Service_VipExpr::F_CREATE_TIME];
	        if(! $hasnext && $size == 0) {
	            $tmp['last'] = 'true';
	        }
	        $list[] = $tmp;
	    }
        $data = array('list'=>$list, 'hasnext'=>$hasnext, 'curpage'=>$page);
	    return $data;
	}

	/**升级VIP*/
	public function upgradeAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipInfo["vipResumeDes"] = "每消费1A币，";
	    $vipInfo["vipUpgradeDes"] = "累积1点会员成长值";
	    $webroot = Yaf_Application::app()->getConfig()->webroot;
	    $vipInfo["listGameUrl"] = $webroot . "/Api/Vip/upgrade";
	    $vipInfo["list"] = User_Api_MyGameList::getList($uuid);
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}

	/**排行榜*/
	public function rankingAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipInfo["vipRanking"] = '排行：'.$vipInfo["vipRanking"];
	    $rankList = User_Api_VipRank::getRankListByPage(1);
	    $vipInfo = array_merge($vipInfo, $rankList);
	    $vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_RANK_DESC);
	    $vipInfo["vipRankingDes"] = $vipDesc ? $vipDesc : "";
	    $webroot = Yaf_Application::app()->getConfig()->webroot;
	    $vipInfo["listGameUrl"] = $webroot . "/Api/Vip/rankingPage";
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}

	public function rankingPageAction() {
	    $uuid = $this->getInput('puuid');
	    User_Api_MyGameList::updateClientDayCacheData($uuid);
	    $page = intval($this->getInput('page'));
	    $this->checkOnline($uuid);
	    if($page < 1) {
	        $page = 1;
	    }
	    $vipInfo = User_Api_VipRank::getRankListByPage($page);
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}

	/**a券奖励*/
	public function aTicketAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->getClientTicketData($uuid);
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = $vipInfo;
	    $this->clientOutput($data);
	}
	
	/**领A券奖励*/
	public function getATicketAction() {
	    $uuid = $this->getInput('puuid');
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipLevel = $vipInfo['vipLevel'];
	    $vipFlg = User_Service_VipFlg::getVipFlg($uuid);
	    $ticketVip = 0;
	    if($vipFlg) {
	        $ticketVip = $vipFlg[User_Service_VipFlg::F_TICKET];
	    }
	    $getTicketVip = User_Config_Vip::getTicketVip($ticketVip);
	    if($getTicketVip && $vipLevel >= $getTicketVip) {
	        $vipFlgData = array(User_Service_VipFlg::F_TICKET => $getTicketVip);
	        if($vipFlg) {
	            User_Service_VipFlg::updateVipFlg($vipFlgData, $uuid);
	        }else{
	            $vipFlgData[User_Service_VipFlg::F_UUID] = $uuid;
	            User_Service_VipFlg::addVipFlg($vipFlgData);
	        }
	        $ticket = User_Config_Vip::getTicket($getTicketVip);
	        $sendData = array(
	            'uuid'=>$uuid,
	            'type'=> Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_VIP,
	            'task_id'=>0,
	            'section_start'=>1,
	            'section_end'=>7,
	            'denomination'=>$ticket,
	            'desc'=>'Vip'.$getTicketVip.'级赠送',
	        );
	        $exchange = new Util_Activity_Context(new Util_Activity_TicketSend($sendData));
	        $exchange->sendTictket();

	        $vipInfo = $this->getClientTicketData($uuid);
	        $data = array();
	        $data['sign'] = 'GioneeGameHall';
	        $data['msg'] = '领取成功' . ($vipInfo["vipLevelObtain"] ? ('，'.$vipInfo["vipLevelObtain"].$vipInfo["ATick"]) : '');
	        $data['success'] = true;
	        $data['data'] = $vipInfo;
	        $this->clientOutput($data);
	    }
	    $data = array('msg' => '', 'sign' => 'GioneeGameHall');
        $data['msg'] = '条件未达到';
        $data['success'] = false;
	    $this->clientOutput($data);
	}
	
	private function getClientTicketData($uuid) {
	    $vipInfo = $this->userVipInfo($uuid);
	    $vipLevel = $vipInfo['vipLevel'];
	    $vipFlg = User_Service_VipFlg::getVipFlg($uuid);
	    $ticketVip = 0;
	    if($vipFlg) {
	        $ticketVip = $vipFlg[User_Service_VipFlg::F_TICKET];
	    }
	    $getTicketVip = User_Config_Vip::getTicketVip($ticketVip);
	    $vipInfo["vipLevelObtain"] = '';
	    $vipInfo["ATick"] = '';
	    $vipInfo["vipGrab"] = 0;
	    if($getTicketVip) {
	        $ticket = User_Config_Vip::getTicket($getTicketVip);
	        $vipInfo["vipLevelObtain"] = 'VIP'.$getTicketVip.'可领取';
	        $vipInfo["ATick"] = $ticket;
	        $vipInfo["vipGrab"] = $getTicketVip > $vipLevel ? 0 : 1;
	    }
	    $vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_DESC);
	    $vipInfo["vipATicketDes"] = $vipDesc ? $vipDesc : "";
	    $vipInfo["items"] = $this->getLevelList();
	    return $vipInfo;
	}
	
	private function userVipInfo($uuid) {
	    $this->checkOnline($uuid);
	    $user = Account_Service_User::getUserInfo(array('uuid' => $uuid));
	    $vipLevel = $user['vip'];
	    $vipRanking = User_Api_VipRank::getUserVipRankByUserInfo($user);
	    $vipValue = intval($user['vip_mon_expr'] + $user['vip_act_expr']);
	    $vipNextLevel = User_Config_Vip::getNextVip($vipLevel);
	    $vipInfo = array();
	    $vipInfo["vipLevel"] = $vipLevel;
	    $vipInfo["vipNextLevel"] = $vipNextLevel;
	    $vipInfo["vipValue"] = $vipValue;
	    $vipInfo["vipRanking"] = $vipRanking;
	    $upgradeExpr = 0;
	    $nextVipExpr = User_Config_Vip::getVipExpr($vipNextLevel);
	    if($nextVipExpr) {
	        $upgradeExpr = $nextVipExpr - $vipValue;
	    }
	    $vipInfo["vipUpgradeValue"] = $upgradeExpr;
	    return $vipInfo;
	}
	
	/**vip图标*/
	public function iconListAction() {
	    $iconList = User_Service_VipIcon::getVipIconListBy(array());
	    $path = Common::getAttachPath();
	    $items = array();
	    foreach ($iconList as $icon) {
	        $vip = $icon[User_Service_VipIcon::F_VIP];
	        if(! isset(User_Config_Vip::$vipExpr[$vip])) {
	            continue;
	        }
	        $img = $icon[User_Service_VipIcon::F_IMG];
	        $items[] = array('vipLevel' => $vip, 'iconUrl' => $path . $img);
	    }
	    $data = array();
	    $data['sign'] = 'GioneeGameHall';
	    $data['msg'] = '';
	    $data['success'] = true;
	    $data['data'] = array('items' => $items);
	    $this->clientOutput($data);
	}
	
	public function testAction() {
	    var_dump(User_Service_VipFlg::getVipFlgListBy(array()));
	}
	
}
