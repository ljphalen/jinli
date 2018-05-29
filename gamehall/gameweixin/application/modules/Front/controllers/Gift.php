<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class GiftController extends Front_BaseController {

    const GIFT_STATUS_ERROR = 0;
    const GIFT_STATUS_DETAIL = 1;
    const GIFT_STATUS_SCRATCH = 2;
    
    const RAND_BASE = 100000;
    
    const PERPAGE = 10;
    
    public $actions = array(
                    'giftCodePostUrl' => '/Front/Gift/giftCodePost',
                    'giftPageListPostUrl' => '/Front/Gift/giftPageListPost'
	);
    
    public function indexAction() {
        $openId = $this->getInput('token');
        $sign = $this->getInput('sign');
        $page = $this->getPageInput();
        list($total, $myGift) = $this->getGiftListData($openId, $page, $sign);
        $this->assign('token', $openId);
        $this->assign('myGift', $myGift);
        $this->assign('hasNext', $this->hasNext($page, $total));
        $this->assign('page', $page);
    }
    
    public function giftPageListPostAction() {
        $openId = $this->getInput('token');
        $sign = $this->getInput('sign');
        $page = $this->getPageInput();
        list($total, $myGift) = $this->getGiftListData($openId, $page, $sign);
        $data = array(
                        'success' => true,
                        'msg' => '',
                        'data' => array(
                                        'list' => $myGift,
                                        'hasnext' => $this->hasNext($page, $total),
                                        'curpage' => $page
                        )
        );
        exit(json_encode($data));
    }
    
    public function giftAction() {
        $openId = $this->getInput('token');
        $giftId = $this->getInput('id');
        $sign = $this->getInput('sign');
        $result = $this->getGift($openId, $giftId, $sign);
        list($status, $msg, $gift, $giftCodeStr) = $result;
        $this->assign('token', $openId);
        $this->assign('status', $status);
        $this->assign('msg', $msg);
        $this->assign('gift', $gift);
        $this->assign('giftCodeStr', $giftCodeStr);
        if (self::GIFT_STATUS_DETAIL == $status) {
        	$this->getView()->display('gift/mygift.phtml');
            exit();
        }
    }
    
    private function getGiftListData($openId, $page, $sign) {
        $uuid = $this->getUuidByOpenId($openId);
        if (!$uuid) {
            $this->redirect(Admin_Service_Weixinuser::getLoginUrl($openId));
            return;
        }
        
        $qureyParams = array(
                        'owner_uuid' => $uuid,
                        'status' => Admin_Service_GiftGrabLog::STATUS_SENDED
        );
        list($total, $userLogs) = Admin_Service_GiftGrabLog::getList($page, self::PERPAGE, $qureyParams, array('update_time'=>'DESC'));
        $myGift = array();
        $format = 'Y/m/d H:i';
        for($i = 0; $i<count($userLogs); $i++){
            $giftId = $userLogs[$i]['gift_bag_id'];
            $gift = Admin_Service_Gift::getById($giftId);
            $myGiftItem = array();
            $myGiftItem['giftId'] = $giftId;
            $myGiftItem['name'] = $gift['title'];
            $myGiftItem['startDate'] = date($format, $gift['exchange_start_time']);
            $myGiftItem['endDate'] = date($format, $gift['exchange_end_time']);
            $myGiftItem['code'] = $userLogs[$i]['code'];
            $myGiftItem['url'] = Admin_Service_Gift::getGiftUrl($giftId, $openId);
            $myGift[] = $myGiftItem;
        }
        return array($total, $myGift);
    }
    
    private function hasNext($page, $total) {
        return $total > $page*self::PERPAGE;
    }
    
    private function getGift($openId, $giftId, $sign) {
        if (!$this->checkInput($openId, $giftId, $sign)) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '非法请求');
        }
        
        $uuid = $this->getUuidByOpenId($openId);
        if (!$uuid) {
            $this->redirect(Admin_Service_Weixinuser::getLoginUrl($openId));
            return;
        }
        
        $gift = Admin_Service_Gift::getById($giftId);
        $grabLog = $this->getUserGrabLog($uuid, $giftId);
        if ($grabLog && $grabLog['status'] == Admin_Service_GiftGrabLog::STATUS_SENDED) {
            return $this->buildGiftResult(self::GIFT_STATUS_DETAIL, '恭喜您中奖了!', $gift, $grabLog['code']);        	
        }
        return $this->grabGift($uuid, $gift, $grabLog);
    }
    
    private function checkInput($openId, $giftId, $sign) {
        if (!$openId || !$giftId || !$sign) {
            return false;
        }
    
        $expectSign = Admin_Service_Gift::getGiftLinkSign($giftId, $openId);
        if ($expectSign != $sign) {
            return false;
        }
        return true;
    }
    
    private function getUserGrabLog($uuid, $giftId) {
        $queryParams = array(
                        'owner_uuid' => $uuid,
                        'gift_bag_id' => $giftId
        );
        return Admin_Service_GiftGrabLog::getBy($queryParams);
    }
    
    private function getUuidByOpenId($openId) {
        $user = Admin_Service_Weixinuser::getByOpenId($openId);
        if (!$user['is_binded']) {
            return false;
        }
        return $user['binded_uuid'];
    }
    
    private function grabGift($uuid, $gift, $grabLog) {
        if (!$this->isWeixin()) {
        	return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '非法请求');
        }
        
        $result = $this->checkGiftValid($uuid, $gift, $grabLog);
        if (!($result === true)) {
        	return $result;
        }
        
        $probability = floatval($gift['probability']);
        $rand = mt_rand(1, self::RAND_BASE);
        if ($rand > $probability*self::RAND_BASE) {
        	return $this->buildGiftResult(self::GIFT_STATUS_SCRATCH, '谢谢参与', $gift);
        }
        
        $giftCode = '';
        if ($grabLog && $grabLog['status'] == Admin_Service_GiftGrabLog::STATUS_LOCK) {
            $giftCode = $grabLog['code'];
        } else {
            $lockCode = Admin_Service_Gift::lockGift($gift['id'], $gift['code_type'], $uuid);
            if (!$lockCode) {
            	return $this->buildGiftResult(self::GIFT_STATUS_SCRATCH, '谢谢参与!', $gift);
            }
            $giftCode = $lockCode['code'];
        }
        return $this->buildGiftResult(self::GIFT_STATUS_SCRATCH, '恭喜您中奖了!', $gift, $giftCode);
    }
    
    private function isWeixin(){
        if (defined('ENV') && ENV != product) {
        	return true;
        }
        
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
    
    private function checkGiftValid($uuid, $gift, $grabLog) {
        if (!$gift || $gift['deleted']) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '活动不存在');
        }
        
        if (!$gift['status']) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '活动已关闭', $gift);
        }
        
        if($gift['activity_end_time'] < time()) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '活动已结束', $gift);
        }
        
        if($gift['activity_start_time'] > time()) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '活动未开始，敬请期待', $gift);
        }
        
        if ((!$grabLog || !$grabLog['code']) && $gift['residue'] <= 0) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '礼包已被抢完', $gift);
        }
        
        //今天已经抢过这个礼包
        if ($grabLog && $grabLog['update_time'] >= Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY)) {
        	return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '谢谢参与', $gift);
        }
        
        if ($this->userTodayGetCodeCount($uuid) >= 5) {
            return $this->buildGiftResult(self::GIFT_STATUS_ERROR, '今日领取礼包已满5个！', $gift);
        }
        return true;
    }
    
    private function userTodayGetCodeCount($uuid) {
        $queryParams = array(
                        'owner_uuid' => $uuid,
                        'update_time' => array('>=', strtotime(date('Y-m-d 00:00:00'))),
                        'status' => Admin_Service_GiftGrabLog::STATUS_SENDED
        );
        return Admin_Service_GiftGrabLog::count($queryParams);
    }
    
    private function buildGiftResult($status, $msg, $gift = array(), $giftCodeStr='') {
        return array($status, $msg, $gift, $giftCodeStr);
    }
    
    public function giftCodePostAction() {
        $openId = trim($this->getInput('token'));
        $giftId = trim($this->getInput('giftId'));
        $giftCode = trim($this->getInput('code'));
        $uuid = $this->getUuidByOpenId($openId);
        if (!$uuid || !$giftId) {
            $this->failPostOutput('上报异常');
        }
        $result = false;
        if ($giftCode) {
        	$result = $this->updateGrabLogStatus($giftId, $uuid);
        } else {
            $result = $this->updateGrabLogTime($giftId, $uuid);
        }
        
        if ($result) {
            $this->successPostOutput();
        } else {
            $this->failPostOutput('上报异常');
        }
    }
    
    private function updateGrabLogStatus($giftId, $uuid) {
        $params = array(
                        'gift_bag_id' => $giftId,
                        'owner_uuid' => $uuid,
                        'status' => Admin_Service_GiftGrabLog::STATUS_LOCK
        );
        $data = array(
                        'status' => Admin_Service_GiftGrabLog::STATUS_SENDED,
                        'update_time' => time()
        );
        return Admin_Service_GiftGrabLog::updateBy($data, $params);
    }
    
    private function updateGrabLogTime($giftId, $uuid) {
        $params = array(
                        'owner_uuid' => $uuid,
                        'gift_bag_id' => $giftId
        );
        $grabLog = Admin_Service_GiftGrabLog::getBy($params);
        
        if ($grabLog) {
            return Admin_Service_GiftGrabLog::update(array('update_time' => time()), $grabLog['id']);
        } else {
            $data = array(
                            'owner_uuid' => $uuid,
                            'gift_bag_id' => $giftId,
                            'update_time' => time()
            );
            return Admin_Service_GiftGrabLog::install($data);
        }
    }
}
?>