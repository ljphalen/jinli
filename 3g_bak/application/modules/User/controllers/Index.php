<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *用户中心首页
 */
class IndexController extends User_BaseController {
	private $_codeExpireInterval = 60;

	public $actions = array();

	public function indexAction() {
		Gionee_Service_Log::pvLog('user_index');
		Gionee_Service_Log::uvLog('user_index', $this->getSource());
		Common_Service_User::setReqeustUri('/user/index/index');//设当前URI，以便后退
		$from      = $this->getInput('from');
		$upVerCoin = 0;
		if ($from == 5) {//浏览器版本升级
			$login     = Common_Service_User::checkLogin('/user/index/index?from=5', true);        //检测登陆状态
			$userInfo  = $login['keyMain'];
			$upVerCoin = User_Service_TaskUpVer::upVerData($userInfo['id']);
		}

		if ($from == 6) {//浏览器在线时长
			$login = Common_Service_User::checkLogin('/user/index/index?from=6', true);        //检测登陆状态
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'browser_online:user_index');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'browser_online:user_index');
		}
		$userScoreInfo = $levelMsg = $userInfo = array();
		$sign          = $unreadNum = 0;
		$canGetCoupon = 1;
		$prizePop = 0;//国庆活动弹窗标识
		$prizeImage = $prizeName=  '';
		$userInfo      = Gionee_Service_User::getCurUserInfo(false,$this->getInput('testMobile'));
		if (!empty($userInfo)) {
			$start_time =  mktime('0', '0', '0');
			$end_time   = mktime('23', '59', '59');
			//得到用户积分信息
			$userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
			//获得用户等级等级信息
			$levelMsg = Common_Service_User::getUserLevelInfo($userInfo['id'], $userInfo['user_level'], $userInfo['level_group']);
			//获得是否有未读内站信
			$unreadNum = Common_Service_User::unReadMsgNumber($userInfo['id']);
			$sign      = User_Service_Earn::getSignInfo($start_time, $end_time, $userInfo['id']);//检测打卡情况
			//Gionee_Service_User::upImei($userInfo['id'], $userInfo['imei_id']);
			//添加经验值
			$isGetPoints = User_Service_ExperienceLog::isGetExperiencePoints($userInfo['id'], $start_time, $end_time);
			if (!intval($isGetPoints)) {
				Common_Service_User::increExperiencePoints($userInfo['id'], $userInfo['experience_level'], 1, 1, 7);
			}
			$unused = $this->_getCouponNumber(array("uid"=>0));
			if(intval($unused)){
				$couponNum =  $this->_getCouponNumber(array("uid"=>$userInfo['id']),$userInfo['id']);
				if ($couponNum) {
					$canGetCoupon = 0;
				} 
			}else{
				$canGetCoupon = 0;
			}
	
			//活动
            $prize_id  = intval($this->getInput('prize_id'));
            $config =Event_Service_Activity::getActivityTypeInfoBySign('miaosha');//双十一活动秒杀
            $pop_back_url='/event/seckill/index';
            $type_id=$config['id'];
            $prizeInfo= Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id);
			if($prizeInfo['prize_status'] == 1 && $prizeInfo['pop_status'] == 0){
				$prizeGoods = Event_Service_Activity::getPrizeGoodsInfo($prizeInfo['prize_id']);
				if($prizeGoods['prize_type'] == 2){
					$prizeImage = $prizeGoods['image'];
					$prizePop = 1;
					$prizeName = $prizeGoods['name'];
					Event_Service_Activity::getResultDao()->update(array('pop_status'=>1),$prizeInfo['id']);
                    Event_Service_Activity::getUserPrizeById($userInfo['id'],$type_id,$prize_id,true);
                    Event_Service_Activity::getUserPrizeList($userInfo['id'],$type_id,true);
				}
			}


            $secpop  = intval($this->getInput('secpop'));
            if($secpop==1){
                $popInfo=Event_Service_Activity::getUserRemindInfo($userInfo['id']);
                if($popInfo['is_pop']==0){ //弹框
                    $prizePop = 1;
                    $config = Event_Service_Activity::getRemindConfigData();
                    $scores=$config['seckill_remind_jb'];
                    $prizeName =  $scores.'金币';
                    $prizeImage = 'jb';
                    $pop_back_url='/event/seckill/preheat';
                    Event_Service_Seckillremind::_getDao()->update(array('is_pop'=>1),$popInfo['id']);
                    Event_Service_Activity::getUserRemindInfo($userInfo['id'],true);
                }
            }
		}
		$popup       = 0;
		$upgradeInfo = User_Service_ExperienceLevelLog::getUpdradeLevelMsg($userInfo['id'], $userInfo['experience_level'], 0);
		if (!empty($upgradeInfo)) {
			$popup     = 1;
			$levelData = User_Service_ExperienceInfo::getLevelPrivilegeDetailData($userInfo['experience_level']);
			$this->assign('levelData', $levelData);
			$key = "USER:UPGRADE:LEVEL:{$userInfo['id']}:{$userInfo['experience_level']}:";
			Common::getCache()->delete($key);
			User_Service_ExperienceLevelLog::update(array('is_popup' => 1), $upgradeInfo['id']);
		}
		
		$scores   = Common_Service_User::getFinalScores($userInfo['id']); //签到获得的积分数
		$products = $this->_getProduces();//赚取积分活动分类(生产类)
		$ads      = Common_Service_User::getAdsByPageType('index'); //获得首页广告

		$showNotice 	= Gionee_Service_Config::getValue('user_show_notice');
		$noticeTitle		 = Gionee_Service_Config::getValue('user_notice_title');
		$this->assign('noticeTitle', $noticeTitle);
		$this->assign('showNotice', $showNotice);
		$this->assign('couponFlag', $canGetCoupon);
		$this->assign('popup', $popup);
		$this->assign('ads', $ads);                        //首页广告
		$this->assign('unread', $unreadNum);            //是否有未读信息
		$this->assign('levelMsg', $levelMsg);    //用户等F级信息
		$this->assign('userInfo', $userInfo);        //用户信息
		$this->assign('scoreInfo', $userScoreInfo);    //用户积分
		$this->assign('products', $products);    //所有产生积分物品
		$this->assign('sign', $sign);                        //用户打卡情况
		$this->assign('scores', $scores);
		$this->assign('upVerCoin', $upVerCoin);
		$this->assign('prizePop', $prizePop);
        $this->assign('pop_back_url', $pop_back_url);
		$this->assign('prizeImage', $prizeImage);
		$this->assign('prizeName', $prizeName);
		
	}

	/**
	 * 规则说明
	 */
	public function ruleAction() {
		$rk      = "USER:INDEX:RULE";
		$content = Common::getCache()->get($rk);
		if (empty($content)) {
			$content = Gionee_Service_Config::getValue('user_index_rule');
			Common::getCache()->set($rk, $content, 60);
		}
		$this->assign('content', $content);
	}

	/**
	 * 获得所以产生积分的物品分类
	 * @return boolean
	 */
	private function _getProduces() {
		$rs         = Common::getCache();
		$produceKey = 'USER:GOODS:PRODUCT';
		$products   = $rs->get($produceKey);
		if (empty($products)) {
			$where    = array('group_id' => '2', 'status' => '1');
			$order    = array('sort' => 'DESC', 'id' => 'DESC');
			$products = User_Service_Category::getsBy($where, $order);
			$rs->set($produceKey, $products, 300);
		}
		return $products;
	}

	/**
	 *用户已完成任务列表
	 */
	private function _doneTasks($start, $end, $uid = 0) {
		$rs        = Common::getCache();
		$taskKey   = 'USER:GOODS:DONE:' . $uid;
		$doneTasks = $rs->get($taskKey);
		if (empty($doneTasks)) {
			$params             = array();
			$params['group_id'] = array("IN", array('1', '2')); //只能是生产类
			$params['add_time'] = array($start, $end);
			$params['uid']      = $uid;
			$doneTasks          = User_Service_ScoreLog::checkTasks($params, array('group_id', 'score_type'));
			$rs->set($taskKey, $doneTasks, 24 * 3600);
		}
		return $doneTasks;
	}



	private function _checkUserSign($start, $end, $uid) {
		if (!$uid) return false;
		$key = "User_Sign_Flag_{$uid}";
		$data = Common::getCache()->get($key);
		if(empty($data)){
			$params             = array();
			$params['group_id'] = 1;
			$params['add_time'] = array($start, $end);
			$params['uid']      = $uid;
			$data                = User_Service_Earn::getBy($params, array('id' => 'DESC'));
			return $data;
		}
	}

	private function _getCouponNumber($params,$uid=1){
		$key = "USER:COUPON:NUM:{$uid}:";
		$num = Common::getCache()->get($key);
		if(empty($num)){
			$num = User_Service_BookCoupon::count($params);
			Common::getCache()->set($key,$num,300);
		}
		return $num;
	}
	public function testAction() {
		Gionee_Service_Log::userCenterDayDataReport();
		exit('0k');
	}

	public function bindphoneAction() {
		$user = Gionee_Service_User::ckLogin();
		$this->assign('user', $user);
		$this->assign('f', $this->getInput('f'));
		$this->assign('redirect', $this->getInput('redirect'));
	}

	public function verifyrndcodeAction() {
		$phone    = $this->getInput('mobile');
		$code     = $this->getInput('smscode');
		$f        = $this->getInput('f');
		$callback = $this->getInput('redirect');
		$callback = html_entity_decode($callback);
		$user     = Gionee_Service_User::ckLogin();
		if (empty($user['username'])) {
			$this->output(-1, '用户未登陆');
		}

		if (empty($phone)) {
			$this->output(-1, '手机号码非法');
		}

		if (empty($code)) {
			$this->output(-1, '验证码错误');
		}

		$url = sprintf('%s/user/index/index', Common::getCurHost());
		if ($f == 1) {
			$url = sprintf('%s/user/goods/list', Common::getCurHost());
		} else if ($f == 2) {
			$url = sprintf('%s/activity/clist', Common::getCurHost());
		} elseif ($f == 3) {
			$url = sprintf('%s/user/goods/changeState?username=%s&redirect=%s', Common::getCurHost(), $phone, $callback);
		} elseif ($f == 4) {
			$url = sprintf('%s', $callback);
		}

		$codeArr = $this->_getCode($phone);
		if (empty($codeArr['c']) || $code != $codeArr['c']) {
			$this->output(-1, '验证码错误');
		}

		Gionee_Service_User::updateUser(array('username' => $phone, 'mobile' => $phone), $user['id']);
		$user['username'] = $phone;
		Gionee_Service_User::cookieUser($user);
		$this->output(0, '验证成功', array('redirect' => $url));
	}

	public function sendrndcodeAction() {
		$phone = $this->getInput('mobile');
		if (!Common::checkIllPhone($phone)) {
			$this->output(-1, '手机号码非法');
		}

		$user = Gionee_Service_User::ckLogin();
		if (empty($user['username'])) {
			$this->output(-1, '用户未登陆');
		}

		/* 	if (Common::checkIllPhone($user['username'])) {
				$this->output(-1, '异常请求');
			} */

		$code = $this->_rndCode();
		if ($this->_expireCode($phone)) {
			$this->output(-1, '请等待接收短信');
		}

		$tel = new Vendor_Tel();
		$out = $tel->templateSMS($phone, 2934, implode(',', array($code, 10)));
		$this->_getCode($phone, $code);
		$this->output(0, '发送成功', array('interval' => $this->_codeExpireInterval, 'smscode' => $code, 'out' => $out));
	}

	private function _rndCode() {
		return rand(1000, 9999);
	}


	private function _expireCode($phone) {
		$codeArr = $this->_getCode($phone);
		$now     = time();
		$ret     = false;
		if (!empty($codeArr) && ($now - $codeArr['t']) < $this->_codeExpireInterval) {
			$ret = true;
		}
		return $ret;
	}

	private function _getCode($phone, $code = '') {
		$rcKey   = 'VERIFY_CODE:' . $phone;
		$now     = time();
		$codeArr = Common::getCache()->get($rcKey);
		if (!empty($code)) {
			$codeArr = array('t' => $now, 'c' => $code);
			Common::getCache()->set($rcKey, $codeArr, Common::T_TEN_MIN);
		}
		return $codeArr;
	}

}