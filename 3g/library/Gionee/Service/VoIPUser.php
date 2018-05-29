<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class  Gionee_Service_VoIPUser {

	//获得某一期内总共领取的用户数
	public static function getCount($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	//检测用户是否已领取
	public static function isObtained($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	//成功抢到资格
	public static function add($params) {
		self::_getDao()->insert($params);
		return self::_getDao()->getLastInsertId();
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}


	/**
	 *
	 * @param array $data
	 * @param int   $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) {
			return false;
		}
		$ret  = self::_getDao()->update($data, intval($id));
		$info = self::_getDao()->get($id);
		Gionee_Service_VoIPUser::getInfoByPhone($info['user_phone'], true);
		return $ret;
	}

	public static function  update($params,$id){
		return self::_getDao()->update($params, $id);
	}
	
	public static function  giveSecondsToUser(){
		
	}
	
	public static function addSecondsToUserAccount($uid,$mobile,$seconds){
		$voipUser = Gionee_Service_VoIPUser::getBy(array('user_phone'=>$mobile));
		$where = array();
		$where['total_seconds'] = $voipUser['total_seconds']+ $seconds;
		$where['m_sys_sec']		=$voipUser['m_sys_sec'] + $seconds;
		$ret = self::update($where, $voipUser['id']);
	
		$params = array(
			'user_phone'		=>$mobile,
			'sec'						=>$seconds,
			'created_at'		=>time()
		);
		Gionee_Service_VoIP::getGiveLogDao()->insert($params);
		Common_Service_User::sendInnerMsg(array('status'=>1,'classify'=>'11','minutes'=>bcdiv($seconds, 60),'uid'=>$uid), 'give_experience_call_times');
	}
	
	/**
	 *
	 * @param unknown $params
	 * @param unknown $order
	 *
	 * @return boolean
	 */
	public static function exchangeSeconds($order = array()) {

	}

	public static function getBy($params, $order = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $order);
	}

	public static function getInfoByPhone($mobile, $sync = false) {
		$rcKey = 'VOIP_USER:' . $mobile;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false || $sync) {
			$ret = Gionee_Service_VoIPUser::getBy(array('user_phone' => $mobile));;
			Common::getCache()->set($rcKey, $ret, 600);
		}
		return $ret;

	}

	public static function upgradeToVipUser($uid){
		$user = Gionee_Service_User::getUser($uid);
		$voipUser = Gionee_Service_VoIPUser::getInfoByPhone($user['username'],true);
		if($voipUser){
			Gionee_Service_VoIPUser::update(array("is_vip"=>1,'get_vip_date'=>date('Ymd',time())), $voipUser['id']);
		}
	}
	
	//赠送通话时长
	
	//
	public static function getDataList($page, $pageSize, $where, $orderBy) {
		if ($page < 1) {
			$page = 1;
		}

		$total = self::_getDao()->count($where);
		$data  = self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $orderBy);
		foreach ($data as $key => $val) {

			$userVoipInfo             = Gionee_Service_VoIPUser::getInfoByPhone($val['user_phone']);
			$diff                     = $userVoipInfo['m_sys_sec'] + $userVoipInfo['exchange_sec'];
			$data[$key]['left_time']  = $diff;
			$data[$key]['total_time'] = Gionee_Service_VoIPLog::getTotalTimeByMonth($val['user_phone'], -1);
		}
		return array($total, $data);
	}

	//统计
	public static function getCountByDate($page, $pageSize, $params = array(), $groupBy = array(), $orderBy = array()) {
		if (!is_array($params) || !is_array($groupBy)) return false;
		$total = self::getTotalActivitedNumber($params, $groupBy);
		$data  = self::_getDao()->getCountByDate($page, $pageSize, $params, $groupBy, $orderBy);
		return array(count($total), $data);
	}

	//
	public static function getTotalActivitedNumber($parms = array(), $group = array()) {
		if (!is_array($parms) || !is_array($group)) return false;
		$data = self::_getDao()->getTotalActivatedNumber($parms, $group);
		return $data;
	}

	private function _checkData($parms) {
		$temp = array();
		if (isset($parms['uid'])) $temp['uid'] = $parms['uid'];
		if (isset($parms['pid'])) $temp['pid'] = $parms['pid'];
		if (isset($parms['get_time'])) $temp['get_time'] = $parms['get_time'];
		return $temp;
	}

	/**
	 * @return Gionee_Dao_VoIPUser
	 */
	private function _getDao() {
		return Common::getDao('Gionee_Dao_VoIPUser');
	}

	static public function formatSec2Min($sec) {
		return floor($sec / 60);
	}

	static public function checkLoginTime($mobile) {
		if (empty($mobile)) {
			return false;
		}
		$nowM         = date('Ym');
		$nowD         = date('Ymd');
		$userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($mobile);
		$conf         = Gionee_Service_Config::getValue('voip_config');
		$cfg          = json_decode($conf, true);

		if (!empty($userVoipInfo['id']) && $userVoipInfo['m_date'] != $nowM) {
			$userVoipInfo['m_sys_sec'] = 0;
			$upData                    = array('m_date' => $nowM, 'm_sys_sec' => 0);
			Gionee_Service_VoIPUser::set($upData, $userVoipInfo['id']);
		}

		//VIP过期判断
		if($userVoipInfo['is_vip']){
			$expireData = date("Ymd",(strtotime('+1 month',strtotime($userVoipInfo['get_vip_date']))));
			if($nowD > $expireData){
				Gionee_Service_VoIPUser::set(array('is_vip'=>0), $userVoipInfo['id']);
			}
		}
		
		$loginGiftSec = array(1800, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600, 600);
		if (!empty($cfg['login_per_time'])) {
			$loginGiftSec = explode(',', $cfg['login_per_time']);
		}

		$maxNum       = count($loginGiftSec);
		$where        = array(
			'user_phone' => $mobile,
			'created_at' => array('>', strtotime(date('Ym') . '01 00:00:00'))
		);
		$loginLogList = Gionee_Service_VoIP::getGiveLogDao()->getsBy($where, array('created_at' => 'desc'));
		$num          = count($loginLogList);
		if (empty($userVoipInfo['firstMin']) && date('Ymd', $loginLogList[0]['created_at']) != $nowD && isset($loginGiftSec[$num])) {
			$perSec = $loginGiftSec[$num];
			if ($num == 0) {
				$rewardSeconds = self::_getExperienceRewardSeconds($mobile); //经验等级赠送
				$userVoipInfo['firstMin'] = Gionee_Service_VoIPUser::formatSec2Min($perSec + $rewardSeconds);
				
			} else if ($num + 1 == $maxNum) {
				$userVoipInfo['maxMin'] = Gionee_Service_VoIPUser::formatSec2Min(array_sum($loginGiftSec));
			} else {
				$userVoipInfo['perMin'] = Gionee_Service_VoIPUser::formatSec2Min($perSec);
			}

			$upData = array(
				'm_sys_sec'     => $userVoipInfo['m_sys_sec'] + $perSec,
				'total_seconds' => $userVoipInfo['total_seconds'] + $perSec
			);
			Gionee_Service_VoIPUser::set($upData, $userVoipInfo['id']);
			$userVoipInfo['m_sys_sec'] = $upData['m_sys_sec'];
			$addData                   = array(
				'user_phone' => $mobile,
				'sec'        => $perSec,
				'created_at' => time()
			);
			Gionee_Service_VoIP::getGiveLogDao()->insert($addData);

		}
		$userVoipInfo['login_num'] = $num;

		return $userVoipInfo;
	}
	

	private static  function _getExperienceRewardSeconds($username){
		$minutes  = 0;
		$user = Gionee_Service_User::getBy(array('username'=>$username),array("id"=>"DESC"));
		$minutes   = User_Service_ExperienceInfo::getLevelRewardsData($user['experience_level'],3);
		return $minutes *60; 
	}

	public static  function collectPerDayExchangeData(){
		$sumData = self::computeEveryDayData();//兑换统计
		$exData  	= User_Service_ScoreLog::getPerDayVoipExchangeData();//当天兑换
		$data = array();
		$data['v__total_user'] 								= $sumData['v__total_user'];
		$data['v_total_seconds'] 							= $sumData['v_total_seconds'];
		$data['v_exchange_total_seconds']		= $sumData['v_exchange_total_seconds'];
		$data['v_exchange_total_user']				= $exData['v_exchange_total_user'];
		$data['v_exchange_cost_scores']			=abs( $exData['v_exchange_cost_scores']);
	
		foreach ($data as $k=>$v){
			$params = array(
				'type' =>'user',
				'key'	=>$k,
				'val'		=>$v,
				'date'	=>date('Ymd',strtotime('-1 day')),
			);
			$logData = Gionee_Service_Log::getBy(array('type'=>$params['type'],'key'=>$params['key'],'date'=>$params['date']));
			if(empty($logData)){
				 Gionee_Service_Log::add($params);
			}else{
				 Gionee_Service_Log::set($params, $logData['id']);
			}
			
		}
	}
	
	public static function computeEveryDayData(){
		return   self::_getDao()->computeEveryDayData();
	}
}