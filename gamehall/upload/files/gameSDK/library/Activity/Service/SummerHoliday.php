<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Activity_Service_SummerHoliday {
	// STATUS的值定义
	const ACTIVITY_OPEN_STATUS  = 1;
	const ACTIVITY_CLOSE_STATUS = 0;
	
	// 暑假活动中定义的值
	const LOGIN_TASK_TYPE    = 1;
	const DOWNLOAD_TASK_TYPE = 2;
	
	// 奖励类型
	const PRIZE_TICKETS = 1;
	const PRIZE_POINTS 	= 2;
	const PRIZE_ENTITY 	= 3;
	const PRIZE_MIN 	= 0;
	
	// 最低奖项类型有两种奖励
	const PRIZE_MIN_TYPE_NO_PRIZE = 0;	// 没有奖励
	const PRIZE_MIN_TYPE_POINT    = 1;	// 发送积分
	
	// 参与条件
	const REWARD_CONDITION_DAILY = 1;
	const REWARD_CONDITION_CONTINUE_FINISHED = 2;
	
	const LOG_FILE_NAME = 'summer.log';
	
	// APCU超时时间
	const CFG_APCU_CACHE_TIME = 60;

	// 概率空间
	const PROBABILITY_COUNT = 1000000;
	
	const SUMMER_TASK_LOCK = 'summer::task';
	
	

	
	public static function getEffectionActivity(){	
		$currTime = Util_TimeConvert::floor(Common::getTime(), Util_TimeConvert::RADIX_MINUTE);
		$params[Activity_Service_Cfg::PREHEAT_TIME] = array('<=', $currTime);
		$params[Activity_Service_Cfg::END_TIME] = array('>=', $currTime);
		$params[Activity_Service_Cfg::STATUS] = Activity_Service_SummerHoliday::ACTIVITY_OPEN_STATUS;
		$params[Activity_Service_Cfg::ACTIVITY_TYPE] = Activity_Service_Cfg::ACTTYPE_SUMMER;
		
		$apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
		$result = $apcu->get(Util_CacheKey::CACHE_ACTIVITY_CFG . $currTime);
		if ($result === false){
			$result = Activity_Service_Cfg::getBy($params, array(Activity_Service_Cfg::ID=>'DESC'));
			$apcu->set(Util_CacheKey::CACHE_ACTIVITY_CFG . $currTime, $result, self::CFG_APCU_CACHE_TIME);
		}
		return $result;
	}
	
	public static function checkClientVersion($currentClientVersion, $versionKey) {
		$clientVersion = Common_Service_Version::getClientVersion($versionKey);
		$result = Common::compareClientVersion($currentClientVersion, $clientVersion);
		return $result;
	}
	
	public static function doActivity($uuid,  $gameId, $taskType = Activity_Service_SummerHoliday::LOGIN_TASK_TYPE ){
		header("Content-type:text/html;charset=utf-8");
		$msg = array('uuid'=>$uuid, 'gameId'=>$gameId, '触发任务的类型taskType'=>$taskType);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$currentActivityId = $activityInfo[Activity_Service_Cfg::ID];
		if(!$currentActivityId){
			return false;
		}
		
		$curentTime = Common::getTime();
		$startTime  = $activityInfo[Activity_Service_Cfg::START_TIME];
		if($curentTime < $startTime){
			return false;
		}
		$msg = array('当前的活动ID'=>$currentActivityId, '活动的开始时间'=>$startTime, '当前时间'=>$curentTime);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
	
		//当前任务ID
		$currentDayTaskId = strtotime(date('Y-m-d 00:00:00'));
		//活动任务配置
		$taskConfig = json_decode($activityInfo[Activity_Service_Cfg::ACTIVITY], true);
		$taskList = $taskConfig['day_task'][$currentDayTaskId];
		if(!count($taskList)){
			return false;
		}
		$msg = array('当前的任务ID'=>$currentDayTaskId, '当期活动的任务配置'=>$taskList);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		$subTask = Activity_Service_SummerHoliday::getSubTaskInDayTask($taskList, $gameId);
		if (!$subTask) {
			return false;
		}
		
		if ($subTask['type'] == Resource_Service_Games::COMBINE_GAME) {
			if (Activity_Service_SummerHoliday::LOGIN_TASK_TYPE == $taskType) {
				self::logForStat("网游启动,appid={$gameId},daytask={$currentDayTaskId},uuid={$uuid}");
			} else {
				self::logForStat("网游完成下载,appid={$gameId},daytask={$currentDayTaskId},uuid={$uuid}");
			}
		} else {
			self::logForStat("单机完成下载,appid={$gameId},daytask={$currentDayTaskId},uuid={$uuid}");
		}
		
		//判断当前配置有此任务
		list($effectTaskNum, $effectTaskList) = self::getCurrentEeffectTaskNum ( $taskList, $taskType, $gameId );
		if(!$effectTaskNum){
			return false;
		}
		
		$msg = array('配置有效的数量effectTaskNum'=>$effectTaskNum, 'effectTaskList'=>$effectTaskList);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		$lockKey = self::getTaskLockKey($uuid);
		if (!self::onlock($lockKey)) {
			Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , '申请锁不成功，存在并发');
			return false;
		}
		
		$userData = self::getUserData ( $uuid, $currentActivityId);
		$userPrizeInfo = json_decode($userData[Activity_Service_UserData::DATA], true);
		$userTaskData = $userPrizeInfo['day_task'];
		
		 //完成当前的任务数
		 $isFinishedCurrentTask = self::isFinishedCurrentTask ( $taskType, $currentDayTaskId, $taskList, $effectTaskNum, $userTaskData );		 
         if($isFinishedCurrentTask){
         	self::unlock($lockKey);
         	return false;
         }
         
         $msg = array('isFinishedCurrentTask=>'=>$isFinishedCurrentTask);
         Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
         
		//用户完成任务数
		$userFinishTaskNum = self::getUserFinishTaskNum ($userTaskData[$currentDayTaskId] );
		if($userFinishTaskNum && count($userTaskData[$currentDayTaskId]) == $userFinishTaskNum){
			self::unlock($lockKey);
			return false;
		}
		$msg = array('userFinishTaskNum'=>$userFinishTaskNum, 'userData'=>$userData, 'userInfo'=>$userPrizeInfo);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		if ($userFinishTaskNum) {
			self::logForStat("今日任务完成,appid={$gameId},daytask={$currentDayTaskId},uuid={$uuid}");
		}

		//获取任务的组装数据
		$taskData = self::getTaskData ($gameId, $currentDayTaskId, $taskList,$userTaskData,$taskType);
		if(!$taskData){
			self::unlock($lockKey);
			return false;
		}
		
		$msg = array('taskData'=>$taskData);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		//发送奖励
        self::sendPrize($effectTaskList, $gameId, $uuid, $activityInfo, $userTaskData);
		
		//获取用户每天的任务数据
		$userPrizeInfo['day_task'] = self::getDayTaskData ( $userTaskData, $taskData, $currentDayTaskId);
		$msg = array('用户每天的任务数据'=>$userPrizeInfo['day_task']);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		
		//活动的奖励配置
		$rewardConfig = json_decode($activityInfo[Activity_Service_Cfg::REWARD], true);
		$reward = $rewardConfig['reward'];
		
		$userPrizeInfo['reward'] = self::initUserRewardData($reward, $userPrizeInfo, $currentDayTaskId);
		$msg = array('用户每天的任务奖励数据初始化'=>$userPrizeInfo['reward']);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
			
		self::saveUserData ($uuid, $currentActivityId, $userData, $userPrizeInfo);
		self::unlock($lockKey);
		
		return true;
	}
	
	private static function isFinishedCurrentTask($taskType, $currentDayTaskId, $taskList, $effectTaskNum, $userTaskData) {
		//检查任务数量
		$currentFinishedTaskNum = 0;
		foreach ($taskList as $key=>$val){
			if($val['type'] != $taskType ){
				continue;
			}
			if($userTaskData[$currentDayTaskId][$key] >= 1){
				$currentFinishedTaskNum ++;
			}
		}
		if($effectTaskNum == $currentFinishedTaskNum){
			return true;
		}
		return false;
	}

	
	public static function saveUserData($uuid, $currentActivityId, $userData, $userInfo) {
		//保存用户数据
		$data[Activity_Service_UserData::DATA] = json_encode($userInfo);
		if($userData){
			$queryParams[Activity_Service_UserData::UUID] = $uuid;
			$queryParams[Activity_Service_UserData::ACTIVITY_ID] = $currentActivityId;
			$ret = Activity_Service_UserData::updateBy($data, $queryParams);
		}else{
			$data[Activity_Service_UserData::UUID] = $uuid;
			$data[Activity_Service_UserData::ACTIVITY_ID] = $currentActivityId;
			$ret = Activity_Service_UserData::insert($data);
		}
		$msg = array('执行结果'=>$ret, 'data'=>$data);
		Util_Log::info(__CLASS__, Activity_Service_SummerHoliday::LOG_FILE_NAME , $msg);
		return $ret;
	}

	
	public static function initUserRewardData($reward, $userPrizeInfo, $currentDayTaskId){
		foreach ($reward as $key=> $val){
			if($userPrizeInfo['reward'][$key] == 2){
				continue;
			}
			if($val['condtion'] == Activity_Service_SummerHoliday::REWARD_CONDITION_DAILY){
				$status = self::calculateDailyRewardStatus($userPrizeInfo['day_task'][$currentDayTaskId]);
			}elseif($val['condtion'] == Activity_Service_SummerHoliday::REWARD_CONDITION_CONTINUE_FINISHED){
				$status = self::calculateContinueDailyRewardStatus($userPrizeInfo['day_task'], $val['conti_finish'], $currentDayTaskId);
			}
			$userReward[$key]  = $status?1:0;
		}
		return $userReward;
		
	}
	
	public  static function checkGameIdInActivity($gameId, $taskList) {
		$isExistGameId = 0;
		foreach ($taskList as $val){
			if($val['game_id'] == $gameId){
				$isExistGameId = 1;
				break;
			}
		}
		return $isExistGameId;
	}
	
	public static function getSubTaskInDayTask($taskList, $gameId) {
		foreach ($taskList as $val){
			if($val['game_id'] == $gameId){
				return $val;
			}
		}
	
		return false;
	}
	
	public static function sendPrize($effectTaskList, $gameId, $uuid, $activityInfo, $userTaskData){
		//发送奖励
		foreach ($effectTaskList as $key=>$val){
			if($val['game_id'] == $gameId){
				if(isset($userTaskData[$currentDayTaskId][$key])){
					continue ;
				}
				if ($val['award_type'] == Activity_Service_SummerHoliday::PRIZE_POINTS){
					return self::sendPointPrize($uuid, $activityInfo[Activity_Service_Cfg::ID], $val['award_count']);
				}else{
					return self::sendTicketPrize($uuid, $activityInfo, $val['award_count'],$val['start_time'] , $val['end_time'] );
				}
			}
		}
	}
	
	public static  function sendPointPrize($uuid, $activityId, $points){
		$data['uuid'] = $uuid;
		$data['gain_type'] = 9;
		$data['gain_sub_type'] = $activityId;
		$data['points'] = $points;
		$data['create_time'] = Common::getTime();
		$data['update_time'] = Common::getTime();
		$data['status'] = 1 ;
		return Point_Service_User::gainPoint($data);
	}
	
	public static function  sendTicketPrize($uuid, $activityInfo ,$ticket, $startTime, $endTime) {
		$sendData = array(
				'uuid'=>$uuid,
				'type'=> 9,
				'task_id'=>$activityInfo[Activity_Service_Cfg::ID],
				'section_start'=>$startTime,
				'section_end'=>$endTime,
				'denomination'=>$ticket,
				'desc'=>$activityInfo[Activity_Service_Cfg::NAME],
		);
		$exchange = new Util_Activity_Context(new Util_Activity_TicketSend($sendData));
		$ret = $exchange->sendTictket();	
		return  true;
	}
	
	
	
	
	public  static function calculateContinueDailyRewardStatus($dailyTaskData, $conditionDays, $currentDayTaskId){
		if(!$conditionDays){
			return  false;
		}
	
		$day = 0;
		for ($i = 0 ; $i < $conditionDays; $i++ ){
			$lastTaskId = strtotime(date('Y-m-d H:i:s',$currentDayTaskId)." -$i Day");
			if(!isset($dailyTaskData[$lastTaskId])){
				break ;
			}
			$status = self::calculateDailyRewardStatus($dailyTaskData[$lastTaskId]);
			if(!$status){
				break ;
			}
			$day ++;
		}
		if($day >= $conditionDays){
			return  true;
		}
		return false;
	}
	
	public  static function getUserFinishTaskNum( $userTaskData) {
		$userFinishTaskNum = 0;
		foreach ($userTaskData as $key=>$val){
			if($val >= 1){
				$userFinishTaskNum ++;
			}
		}
		return $userFinishTaskNum;
	}
	
	
	
	public  static function calculateDailyRewardStatus($currentTaskData){
		$finishNum = 0;
		foreach ($currentTaskData as $key=>$val){
			if($val >= 1){
				$finishNum ++;
			}
		}
		if(count($currentTaskData) == $finishNum){
			return true;
		}else{
			return false;
		}
	
	}
	
	private static function getDayTaskData($userTaskData, $taskData, $currentDayTaskId) {
		if(count($userTaskData)){
			$userTaskData[$currentDayTaskId] = $taskData[$currentDayTaskId];
		}else{
			$userTaskData = $taskData;
		}
		return $userTaskData;
	}
	
	
	private static function getCurrentEeffectTaskNum($taskList, $taskType, $gameId) {
		//检查任务数量
		$taskNum = 0;
		foreach ($taskList as $key=>$val){
			if( ($val['type'] != $taskType)){
				continue;
			}
			$taskNum++;
			if($val['game_id'] == $gameId){
				$effectTaskList[$key]=$val;
			}
		}
		return array($taskNum, $effectTaskList);
	}
	
	
   private static function getTaskData($gameId, $currentDayTaskId, $taskList, $userTaskData,$taskType) {
		foreach ($taskList as $key=>$val){		
			if($val['type'] != $taskType && !isset($userTaskData[$currentDayTaskId][$key])){
				$taskData[$currentDayTaskId][$key] = 0 ;
				continue;
			}elseif($val['type'] != $taskType){
				$taskData[$currentDayTaskId][$key] = $userTaskData[$currentDayTaskId][$key];
				continue;
			}
			if($val['game_id'] != $gameId){
				$taskData[$currentDayTaskId][$key] = intval($userTaskData[$currentDayTaskId][$key]) ;
				continue;
			}
			$taskData[$currentDayTaskId][$key] = 1 ;
		}
		return $taskData;
	}
	
	
	
	public  static function getUserData($uuid, $currentActivityId) {
		//用户完成的任务的配置
		$params[Activity_Service_UserData::ACTIVITY_ID] = $currentActivityId;
		$params[Activity_Service_UserData::UUID] = $uuid;
		$userTask = Activity_Service_UserData::getBy($params);
		return $userTask;
	}
	
	public static function getLogCustomKey($rewardId, $rewardItemId) {
		return $rewardId.":".$rewardItemId;
	}
	
	public static function isNoPrize($rewardItem) {
		return (Activity_Service_SummerHoliday::PRIZE_MIN == $rewardItem['type'])
		&& ($rewardItem['least_type'] == Activity_Service_SummerHoliday::PRIZE_MIN_TYPE_NO_PRIZE);
	}
	
	
	
	private static function getTaskLockKey($uuid){
		return self::SUMMER_TASK_LOCK.'::'.$uuid;
	}
		
	/**
	 * 需要锁定的KEY
	 * @param unknown_type $key
	 * @param unknown_type $lockExpireTime
	 */
	private static function onlock($key, $lockExpireTime=2) {
		$cache = Cache_Factory::getCache();
		return $cache->lock($key, $lockExpireTime);
	}
	
	private static function unlock($key) {
		$cache = Cache_Factory::getCache();
		$cache->unlock($key);
	}
	
	/**
	 * 用于统计的日志
	 */
	const STAT_FILE_NAME = 'summer_stat.log';
	public static function logForStat($msg) {
		Util_Log::info(__CLASS__, self::STAT_FILE_NAME, $msg);
	} 
	
}