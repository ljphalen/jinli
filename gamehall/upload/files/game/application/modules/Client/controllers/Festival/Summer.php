<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author luojiapeng
 *
 */
class Festival_SummerController extends Client_BaseController{	
	
	const  ONE_PAGE = 1;
	const  PAGE_LIMIT = 20;
	
	const  PRIZE_NOT_CAN_EXCHANGE_STATUS = 0;
	const  PRIZE_CAN_EXCHANGE_STATUS = 1 ;
	const  PRIZE_EXCHAGED_STATUS = 2;
	
	const  GET_PRIZE_FILE_LOG  = 'get_prize.log';
	const  AUTO_PRIZE_FILE_LOG = 'auto_prize.log';
	const  GET_TASK_STATUS_LOG = 'get_tast_status.log';
	  
	/**
	 * 
	 */
	public function indexAction(){
		$info =  $this->getInput(array('puuid','uname','sp'));
		
		
		$info['imei'] = Common::parseSp($info['sp'], 'imei');	
		$online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');	
		$webRoot = Common::getWebRoot();
		
		//活动信息
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$currentActivityId =  $activityInfo[Activity_Service_Cfg::ID];
		
		//限制的客户端版本
		$ClientVersionKey = $activityInfo[Activity_Service_Cfg::CLIENT_VER];
		$limitClientVersion = Common_Service_Version::getClientVersion($ClientVersionKey);
		
		if(!$activityInfo){
			exit;
		}
		$currentTime = Common::getTime();
		$startTime = $activityInfo[Activity_Service_Cfg::START_TIME];
		if($currentTime < $startTime){
			$this->displayToPrehead ( $webRoot, $activityInfo, $limitClientVersion );
			exit;
		}
		
		
		if($online){
			//用户数据
			$userData = Activity_Service_SummerHoliday::getUserData($info['puuid'], $activityInfo[Activity_Service_Cfg::ID]);
			$userInfo = json_decode($userData[Activity_Service_UserData::DATA], true);
			$userTaskData = $userInfo['day_task'];
		}
	
		//奖品信息
		$currentDayTaskId = strtotime(date('Y-m-d 00:00:00'));
		$prizeInfo = $this->getPrizeInfo ( $activityInfo, $online, $info['puuid'], $currentDayTaskId, $userInfo);

		//每天任务
		$taskConfig = json_decode($activityInfo[Activity_Service_Cfg::ACTIVITY], true);
		$taskList = $this->getDailyTaskList ( $currentDayTaskId, $taskConfig, $userTaskData);
	
		//完成进度
		list($todayFinished, $daysList) = $this->getDaysList ( $activityInfo, $taskConfig, $info['uuid'], $userTaskData, $online);
		$finishedDays = $points = $tickets = $continueDays= 0;
		if($online){
			list($finishedDays, $points, $tickets, $continueDays) = $this->getFinishedDayAndTotal ( $info['uuid'], $taskConfig ,$userTaskData);
		}
 
		//奖品领取榜
		$prizeRank = $this->getRewardLog ($currentActivityId);
		$holidayData = $this->makeholidayDataForJs ( $currentActivityId, $limitClientVersion, $taskList, $webRoot);
		
		$this->assign('taskName', $taskConfig['name']);
		$this->assign('taskImg',  $taskConfig['img']);
		$this->assign('taskList', $taskList);
		$this->assign('daysList', $daysList);
		$this->assign('finishedDays', $finishedDays);
		$this->assign('todayFinished', $todayFinished);
		$this->assign('points', $points);
		$this->assign('tickets', $tickets);
		$this->assign('continueDays', $continueDays);
		$this->assign('holidayData', json_encode($holidayData));
		$this->assign('limitClientVersion', $limitClientVersion);
		$this->assign('activityInfo', $activityInfo);
		$this->assign('webRoot', $webRoot);
		$this->assign('prizeInfo',$prizeInfo);
		$this->assign('online', $online);
		$this->assign('uuid', $info['puuid']);
		$this->assign('nickName', $userInfo['nickname']);
		$this->assign('prizeRank', $prizeRank);
		$this->assign('source', $this->getSource());
	}
	
	private function displayToPrehead($webRoot, $activityInfo, $limitClientVersion, $holidayData) {
		$clientGameId = $this->getCleinetGameId ();
		$gameInfo = Resource_Service_GameData::getBasicInfo($clientGameId);
		$holidayData['clientVersion']=$limitClientVersion?$limitClientVersion:'1.5.8';
		$holidayData['downurl']=$gameInfo['link'];
		$holidayData['clientGameId']=$clientGameId;
		$holidayData['packagename']=$gameInfo['package'];
		$holidayData['filesize']=$gameInfo['size'];
		$holidayData['clientGameDetailUrl'] = $webRoot.'/client/index/detail/?id='.$clientGameId.'&t_bi=';
		$holidayData['timeStart']=Common::getTime();
		$holidayData['timeEnd']  = strtotime(date('Y-m-d H:i:s',$activityInfo[Activity_Service_Cfg::START_TIME]).' + 100 second');
		$holidayData['activityUrl'] =$webRoot.'/client/Festival_Summer/index/';
		$this->assign('preTime', $activityInfo[Activity_Service_Cfg::PREHEAT_TIME]);
		$this->assign('preImg',  $activityInfo[Activity_Service_Cfg::PREHEAT_IMG]);
		$this->assign('holidayData', $holidayData);
		$this->assign('limitClientVersion', $limitClientVersion);
		$this->getView()->display("festival/summer/preindex.phtml");
		exit;
	}

	
	private function makeholidayDataForJs($currentActivityId, $limitClientVersion, $taskList, $webRoot) {
		$clientGameId = $this->getCleinetGameId ();
		$gameInfo = Resource_Service_GameData::getBasicInfo($clientGameId);
		$holidayData['game'] = $taskList;
		$holidayData['clientVersion']=$limitClientVersion;
		$holidayData['downurl']=$gameInfo['link'];
		$holidayData['clientGameId']=$clientGameId;
		$holidayData['packagename']=$gameInfo['package'];
		$holidayData['filesize']=$gameInfo['size'];
		$holidayData['clientGameDetailUrl'] = $webRoot.'/client/index/detail/?id='.$clientGameId.'&t_bi=';
		$holidayData['ajaxUrlEntity']=$webRoot.'/client/Festival_Summer/exhangeEntityPost/?activityId='.$currentActivityId;
		$holidayData['autoFinishTaskUrl'] =$webRoot.'/client/Festival_Summer/autoFinishTask/';
		$holidayData['getTaskStatusUrl'] =$webRoot.'/client/Festival_Summer/getUserTaskStatus/';
		return $holidayData;
	}
	
	
	private function getCleinetGameId() {
		$clientGameId = (ENV == 'product')? '117':'66';
		return $clientGameId;
	}

	public function autoFinishTaskAction(){
		$info =  $this->getInput(array('puuid',
									   'sp',
				                       'taskData')
				               );
		
	    $info['taskData'] = html_entity_decode($info['taskData']);
	    $msg = array('info' => $info);
		Util_Log::info(__CLASS__, self::AUTO_PRIZE_FILE_LOG,  $msg);
		
		$data['status'] = 0;
	    if(!$info['puuid']){
			$this->output(0, '用户非法', $data);
		}
		if(!$info['sp']){
			$this->output(0, '用非法', $data);
		}
		if(!$info['taskData']){
			$this->output(0, '任务非法', $data);
		}

		//活动信息
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$currentActivityId =  $activityInfo[Activity_Service_Cfg::ID];
		if(!$currentActivityId){
			$this->output(1, '活动已过期', $data);
		}
		
		$sp = common::parseSp($info['sp']);
		$info['imei'] = $sp['imei'];
		$currentClientVersion = $sp['game_ver'];

		//客户端版本判断
		$result = Activity_Service_SummerHoliday::checkClientVersion ($currentClientVersion, $activityInfo[Activity_Service_Cfg::CLIENT_VER] );
		if(!$result){
			$this->output(0, '客户端的版本过低，请升级', $data);
		}
		
		$online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');
		if(!$online){
			$this->output(0, '未登录', $data);
		}
		
		$resquestTaskData = json_decode($info['taskData'], true);		
		if(!is_array($resquestTaskData)){
			$this->output(0, '任务非法1', $data);
		}
		foreach ($resquestTaskData as $val){
			if($val['taskType'] != Activity_Service_SummerHoliday::DOWNLOAD_TASK_TYPE ){
				$this->output(0, '任务非法2', $data);
			}
			if(!isset($val['taskType'] )){
				$this->output(0, '任务非法3', $data);
			}
		}
		
		//当前的任务ID
		$currentDayTaskId = strtotime(date('Y-m-d 00:00:00'));
		$taskConfig     = json_decode($activityInfo[Activity_Service_Cfg::ACTIVITY], true);
		$taskConfigData = $taskConfig['day_task'][$currentDayTaskId];
		$currentConfigTaskNum = count($taskConfigData);
		
		if(!$currentConfigTaskNum){
			$this->output(1, '任务非法', $data);
		}
		
		foreach ($resquestTaskData as $val){
			$taskType = $taskConfigData[$val['taskId']]['type'];
			if($taskType != $val['taskType']){
				$this->output(0, '任务类型不对', $data);
			}
			$isExistGameId = Activity_Service_SummerHoliday::checkGameIdInActivity($val['gameId'], $taskConfigData);
		    if(!$isExistGameId){
		    	$this->output(0, '游戏不存在', $data);
		    }
		}
		
		//用户完成的任务数据
		$userData = Activity_Service_SummerHoliday::getUserData($info['puuid'], $activityInfo[Activity_Service_Cfg::ID]);
		$userPrizeInfo = json_decode($userData[Activity_Service_UserData::DATA], true);
		$userTaskData = $userPrizeInfo['day_task'][$currentDayTaskId];
		
		foreach ($resquestTaskData as $val){
			if($userTaskData[$val['taskId']] >= 1){
				$this->output(0, '任务已完成', $data);
			}
		}
		
		$msg = array('userPrizeInfo' => $userPrizeInfo);
		Util_Log::info(__CLASS__, self::AUTO_PRIZE_FILE_LOG,  $msg);
		//活动的奖励配置
		$rewardConfig = json_decode($activityInfo[Activity_Service_Cfg::REWARD], true);
		$reward = $rewardConfig['reward'];
		foreach ($resquestTaskData as $val){
			$userPrizeInfo['day_task'][$currentDayTaskId] = $this->initUserTask($currentDayTaskId, $taskConfigData, $userPrizeInfo, $val['taskId']);
			$userPrizeInfo['reward'] = Activity_Service_SummerHoliday::initUserRewardData($reward, $userPrizeInfo, $currentDayTaskId);
		}	
		$msg = array('userPrizeInfo' => $userPrizeInfo);
		Util_Log::info(__CLASS__, self::AUTO_PRIZE_FILE_LOG, $msg);
		$ret = Activity_Service_SummerHoliday::saveUserData($info['puuid'], $currentActivityId, $userData, $userPrizeInfo);
	    if($ret){
	    	Activity_Service_SummerHoliday::logForStat("单机直接完成,appid={$val['taskId']},daytask={$currentDayTaskId},uuid={$info['puuid']}");
	    	$data =  $this->analyseUserTaskData($userPrizeInfo, $currentDayTaskId, $taskConfig, $info['puuid']);
	    	$msg = array('返回的json数据' => $data);
	    	Util_Log::info(__CLASS__, self::AUTO_PRIZE_FILE_LOG,  $msg);
	    	$this->output(0, '任务成功', $data);
	    }else{
	    	$this->output(0, '任务完成失败', $data);
	    }
	}
	
	private function analyseUserTaskData($userData, $currentDayTaskId, $taskConfig, $uuid){
		  $userTaskData = $userData['day_task'][$currentDayTaskId];
		  $userFinishTaskNum = Activity_Service_SummerHoliday::getUserFinishTaskNum($userTaskData);
		  $taskStatus = ($userFinishTaskNum && $userFinishTaskNum == count($userTaskData))?1:0;
		  $data['reward'] = $userData['reward'];
		  $data['taskStatus'] = $taskStatus;
		  list($finishedDays, $points, $tickets, $continueDays) = $this->getFinishedDayAndTotal ( $uuid, $taskConfig ,$userData['day_task']);
		  $data['finishedDays'] = $finishedDays;
		  $data['continueDays'] = $continueDays;
		  $data['points'] = $points;
		  $data['tickets'] = $tickets;
		  $data['status'] = 1;
		  return $data;
	}
	
	
	public function getUserTaskStatusAction(){
		$info =  $this->getInput(array('puuid','sp'));
		
		$msg = array('info' => $info);
		Util_Log::info(__CLASS__, self::GET_TASK_STATUS_LOG, $msg);
		
		
		$data['taskStatus'] = 0;	
		if(!$info['puuid'] || !$info['sp']){
			$this->output(0, '非法请求', $data);
		}
		//活动信息
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$currentActivityId =  $activityInfo[Activity_Service_Cfg::ID];
		if(!$currentActivityId){
			$this->output(1, '活动已过期', $data);
		}
		
		$sp = common::parseSp($info['sp']);
		$info['imei'] = $sp['imei'];
		$currentClientVersion = $sp['game_ver'];
		//客户端版本判断
		$result = Activity_Service_SummerHoliday::checkClientVersion ($currentClientVersion, $activityInfo[Activity_Service_Cfg::CLIENT_VER] );
		if(!$result){
			$this->output(0, '客户端的版本过低，请升级', $data);
		}
	
		$currentDayTaskId = strtotime(date('Y-m-d 00:00:00'));
		$taskConfig     = json_decode($activityInfo[Activity_Service_Cfg::ACTIVITY], true);
		
		//用户完成的任务数据
		$userData = Activity_Service_SummerHoliday::getUserData($info['puuid'], $activityInfo[Activity_Service_Cfg::ID]);
		$userPrizeInfo = json_decode($userData[Activity_Service_UserData::DATA], true);
		$userTaskData = $userPrizeInfo['day_task'][$currentDayTaskId];
		
		$userFinishTaskNum = Activity_Service_SummerHoliday::getUserFinishTaskNum($userTaskData);
		$taskStatus = ($userFinishTaskNum && $userFinishTaskNum == count($userTaskData))?1:0;
		$data['reward'] = $userPrizeInfo['reward'];
		$data['taskStatus'] = $taskStatus;
		list($finishedDays, $points, $tickets, $continueDays) = $this->getFinishedDayAndTotal ( $info['puuid'], $taskConfig ,$userPrizeInfo['day_task']);
		$data['finishedDays'] = $finishedDays;
		$data['continueDays'] = $continueDays;
		$data['points'] = $points;
		$data['tickets'] = $tickets;
		
		$msg = array('data' => $data);
		Util_Log::info(__CLASS__, self::GET_TASK_STATUS_LOG, $msg);
		$this->output(0, '成功', $data);
		
	}
	
	
	private function initUserTask($currentDayTaskId, $taskConfigData, $userPrizeInfo, $currentTaskId) {
		foreach ($taskConfigData as $key=> $val){
			if(!isset($userPrizeInfo['day_task'][$currentDayTaskId][$key])){
				$userPrizeInfo['day_task'][$currentDayTaskId][$key] = 0;
			}
			if($key == $currentTaskId){
				$userPrizeInfo['day_task'][$currentDayTaskId][$key] = 3;
			}
		}
		return $userPrizeInfo['day_task'][$currentDayTaskId];
	}
		
	private function getRewardLog($currentActivityId){
		$apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
		$result = $apcu->get(Util_CacheKey::CACHE_ACTIVITY_LOG . $currentActivityId);
		if ($result) return json_decode($result, true);
		
		$params[Activity_Service_RewardLog::ACTIVITY_ID] = $currentActivityId;
		$params[Activity_Service_RewardLog::REWARD] = array('<>', '');
		$orderBy = array(Activity_Service_RewardLog::CREAYTE_TIME=>'DESC');
		list(   ,$list) = Activity_Service_RewardLog::getList(self::ONE_PAGE, self::PAGE_LIMIT, $params, $orderBy);
		$rewardList =  array();
		foreach ($list as $val){
			$data = json_decode($val[Activity_Service_RewardLog::REWARD], true);
			$desc = '恭喜 '.$data['nickname'].' 获得 '.$data['name'].'！';
			$rewardList[] = $desc;
		}
		
		$apcu->set(Util_CacheKey::CACHE_ACTIVITY_LOG . $currentActivityId, json_encode($rewardList), 20);
		
		return $rewardList;
	}
	
	private function getFinishedDayAndTotal($uuid, $taskConfig, $userTaskData) {
	
		$finishedDays = 0 ;	
		foreach ($taskConfig['day_task'] as $key=>$val){		
			$points = $tickets = 0 ;
			foreach ($val as $ke=> $item){
				if($userTaskData[$key][$ke] != 1){
					continue ;
				}
				if($item['award_type'] == Activity_Service_SummerHoliday::PRIZE_POINTS){
					$points  += $item['award_count'];
				}else{
					$tickets += $item['award_count'];
				}
			}
			$pointList[$key] = $points;
			$ticketList[$key] = $tickets;
			
		
			$finshTaskNum = Activity_Service_SummerHoliday::getUserFinishTaskNum($userTaskData[$key]);
			$isFinished = (2 == $finshTaskNum)?1:0;
			if($isFinished){
				$continueDaysArr[] = $key;
				$finishedDays ++;
			}
		}
		$continueDays = 0;
		for ($i = 0; $i < count($continueDaysArr); $i++){	
			if(isset($continueDaysArr[$i-1])){
				$day = $this->diffDate($continueDaysArr[$i-1],$continueDaysArr[$i]);
			}
			if($day == 1 && isset($continueDaysArr[$i-1])){
				$continueDays++;
			}elseif(!isset($continueDaysArr[$i-1]) && !isset($continueDaysArr[$i])){
				$continueDays = 0;
			}elseif($continueDaysArr[$i]){
				$continueDays = 1;
			}
		}

		return  array($finishedDays, array_sum($pointList), array_sum($ticketList), $continueDays);
	}
	
	private function diffDate($startTime, $endTime){
		return round(($endTime - $startTime) / 86400);
	}
	
	private function getDaysList($activityInfo, $taskConfig, $uuid, $userTaskData, $online) {
		$daysList = array();
		$todayFinished = 0;
        foreach ($taskConfig['day_task'] as $key=>$val){
        	$day = date('d', $key);
        	$finshTaskNum = Activity_Service_SummerHoliday::getUserFinishTaskNum($userTaskData[$key]);
        	$isFinished = ($finshTaskNum && count($userTaskData[$key]) == $finshTaskNum)?1:0;
        	$daysList[] = array('day'=>$day,
        			            'isFinished'=>$isFinished&&$online?1:0,
        			            'isToday'=>$day == date('d')?1:0,
        			            'isAfterToday'=>$day > date('d')?1:0,
        			            'isBeforeToday'=>$day < date('d')?1:0,
        			      );
        	if(($day == date('d')) && $isFinished && $online){
        		$todayFinished = 1;
        	}
        }
        return array($todayFinished, $daysList);
	}
	
	 private function getDailyTaskList($currentDayTaskId, $taskConfig, $userTaskData) {
		$taskList = $taskConfig['day_task'][$currentDayTaskId];
		$dailyTask = array();
		foreach ($taskList as $key=> $val){
			$gameInfo = Resource_Service_GameData::getBasicInfo($val['game_id']);
			if(!$gameInfo) continue;
			$dailyTask[] = array('taskName'=>$val['name'],
					             //'taskInfo'=>$val['info'],
					             'taskId'  =>$key,
								 'taskType'=>$val['type'],
					             'taskStatus'=>intval($userTaskData[$currentDayTaskId][$key]),
					             'gameId'=>$gameInfo['gameid'],
					             'gameName'=>$gameInfo['name'],
					             'iconUrl'=>$gameInfo['img'],	
					             'packageName'=>$gameInfo['package'],
					             'source'=>'summer',
					             'gameSize'=>$gameInfo['size'],
					             'downUrl'=>$gameInfo['link'],
					             'freedl'=>$gameInfo['freedl']			
					);
			
		}
		return $dailyTask;
	}
	
	private function getPrizeInfo($activityInfo, $online, $uuid, $currentDayTaskId, $userInfo) {
	
		$rewardConfig = json_decode($activityInfo[Activity_Service_Cfg::REWARD], true);
		$reward = $rewardConfig['reward'];
		$prizeinfo = array('name'=> $rewardConfig['name'],
				           'info'=> html_entity_decode($rewardConfig['info']));
	
		$prizeList = array();
		foreach ($reward as $rewardId=> $val){
			$prizeStatus = 0;
			if($online){
				$prizeStatus = $this->getUserPirzeStatus($uuid, $rewardId, $currentDayTaskId, $userInfo);
			}
			$prizeCondition = ($val['condtion'] == 1)?"完成每日任务后，才能抽奖":"完成连续打工".$val['conti_finish']."天后，才能抽奖";		
			$prizeList[$rewardId] = array('prizeName'=>$val['name'],
									 'prizeImg'=>$val['img'],
									 'prizeInfo'=>$val['info'],
									 'prizeId'=>$rewardId,
									 'prizeStatus'=>intval($prizeStatus),
					                 'prizeCondition'=>$prizeCondition
			);
		}
		$prizeinfo['list'] = $prizeList;
		return $prizeinfo;
	}
	
	public function activityInfoAction(){
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$rewardConfig = json_decode($activityInfo[Activity_Service_Cfg::REWARD], true);
		$reward = $rewardConfig['reward'];
		$this->assign('info', $activityInfo['explain']);
		$this->assign('subTitle', $activityInfo['name']);
		
	}
	
	private function getUserPirzeStatus($uuid, $rewardId, $currentDayTaskId, $userInfo) {
		$prizeStatus = 0;
		$userRewardData = $userInfo['reward'];
		$userTaskData   = $userInfo['day_task'];
		if($userTaskData && count($userTaskData[$currentDayTaskId])){
			$prizeStatus = $userRewardData[$rewardId];
		}
		return $prizeStatus ;
	}
	
	public function exhangeEntityPostAction(){
	  	$info =  $this->getInput(array('activityId' ,
							  		   'prizeId',
	  			                       'prizeItemId',
							  		   'puuid',
	  			                       'address',
	  			                       'name',
	  			                       'phone'
							  			));
	  	
	  	$data['status'] = 0;
	  	$this->checkEntityPostParams ( $info, $data );
	  	
	  	$params[Activity_Service_RewardLog::UUID] = $info['puuid'];
	  	$params[Activity_Service_RewardLog::ACTIVITY_ID] = $info['activityId'];
	  	$result = Activity_Service_RewardLog::getBy($params);
	  	$customKey = $result[Activity_Service_RewardLog::CUSTOM_KEY];
	  	$customKey = explode(":", $customKey);
	 
	  	if($customKey[0] != $info['prizeId'] || $customKey[1] != $info['prizeItemId']){
	  		$this->output(1, '非法数据', $data);
	  	}
	  	$reward = json_decode($result[Activity_Service_RewardLog::REWARD], true);
	  	$reward['entity'] = array('address'=>$info['address'],
  								 'contact'=>$info['name'],
  								 'phone'=>$info['phone']);
	  	$rewardData[Activity_Service_RewardLog::REWARD] = json_encode($reward);
	  	$ret = Activity_Service_RewardLog::updateBy($rewardData, $params);
	  	if($result){
	  		$data['status'] = 1;
	  		$this->output(0, '提交成功', $data);
	  	}else{
	  		$this->output(0, '提交失败', $data);
	  	}
    }
	
	private function checkEntityPostParams($info, $data) {	
		if(trim($info['puuid']) == '' ){
			$this->output(0, '不能为空1', $data);
		}	
	  	if(trim($info['address']) == '' ){
	  		$this->output(0, '不能为空2', $data);
	  	}
	  	if(trim($info['name']) == '' ){
	  		$this->output(0, '不能为空3', $data);
	  	}
	  	if(trim($info['phone']) == '' ){
	  		$this->output(0, '不能为空4', $data);
	  	}
	  	if(!intval($info['activityId'])){
	  		$this->output(0, '非法数据1', $data);
	  	}
	  	if(!intval($info['prizeId'])){
	  		$this->output(1, '非法数据2', $data);
	  	}
	  	if(!intval($info['prizeItemId'])){
	  		$this->output(0, '非法数据3', $data);
	  	}
	}
	
	/**
	 * 用户刮奖
	 */
	public function getPrizeAction(){
		$info =  $this->getInput(array('prizeId', 
				                       'puuid',
				                        'sp'));
		
		$msg = array('接收的参数' => $info);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		
		
		$this->checkRequesParam ( $info );
		$sp = common::parseSp($info['sp']);
		$info['imei'] = $sp['imei'];
		$currentClientVersion = $sp['game_ver'];
		
		$data['status'] = 0;
		$activityInfo = Activity_Service_SummerHoliday::getEffectionActivity();
		$currentActivityId =  $activityInfo[Activity_Service_Cfg::ID];
		if(!$currentActivityId){
			$this->output(0, '活动已过期', $data);
		}
		$msg = array('活动ID' => $currentActivityId);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		
		//客户端版本判断
		$result = Activity_Service_SummerHoliday::checkClientVersion ($currentClientVersion, $activityInfo[Activity_Service_Cfg::CLIENT_VER] );
		if(!$result){
			$this->output(0, '客户端的版本过低，请升级', $data);
		}
		$msg = array('客户版本验证' => $result, 'currentClientVersion'=>$currentClientVersion);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		
		//用户的登录状态
	 	$online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');	
		if(!$online){
			$this->output(0, '用户未登录', $data);
		} 
		
		$userData = Activity_Service_SummerHoliday::getUserData($info['puuid'], $activityInfo[Activity_Service_Cfg::ID]);
		$userInfo = json_decode($userData[Activity_Service_UserData::DATA], true);	
		if($userInfo['reward'][$info['prizeId']] == 2){
			$this->output(0, '用户已兑奖', $data);
		}elseif(intval($userInfo['reward'][$info['prizeId']]) < 1){
			$this->output(0, '用户不可抽奖', $data);
		}
		$msg = array('userData' => $userData, 'userInfo'=>$userInfo);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		
		$taskConfig   = json_decode($activityInfo[Activity_Service_Cfg::ACTIVITY], true);
		$rewardConfig = json_decode($activityInfo[Activity_Service_Cfg::REWARD], true);
		$reward = $rewardConfig['reward'];
		$status = $this->isCanPrize ($userInfo ,$reward[$info['prizeId']]);
		if(!$status){
			$this->output(0, '用户没有兑奖资格', $data);
		}
		
		if (!self::_lock(self::USER_LOCK, $info['puuid'])) {
			$this->output(0, '检测到重复刮奖', $data);
		}
		
		$msg = array('userData' => $userData, 'userInfo'=>$userInfo);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		//根据概率获取奖品的ID
		$rewardItems = $reward[$info['prizeId']]['awardItem'];
		
		list($prizeItemId, $leastPrizeId) = $this->getPrizeItemId( $rewardItems );
		$msg = array('prizeItemId=>' => $prizeItemId, 'rewardItems=>'=>$rewardItems);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		
		$awardLockKey = false;
		$gainPrizeStatus = self::isUserCanGainPrize($currentActivityId, $info['prizeId'], $prizeItemId, $rewardItems);
		if (!$gainPrizeStatus) {
			$prizeItemId = $leastPrizeId;
		} else {
			$awardLockKey = $currentActivityId.":".$info['prizeId'].":".$prizeItemId;
			if (!self::_lock(self::AWARD_LOCK, $awardLockKey)) {
				$prizeItemId = $leastPrizeId;
			} else {
				$awardLockKey = true;
			}
		}
		
		$msg = array('$gainPrizeStatus=>' => $gainPrizeStatus);
		Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		$result = $this->saveRewardLog($info, $currentActivityId, $rewardItems, $prizeItemId);
		$this->updateUserDataReward ($info, $currentActivityId, $userInfo);
		self::_incPrizeCount($currentActivityId, $info['prizeId'], $activityInfo[Activity_Service_Cfg::END_TIME] + 3600*24);
		
		$prizeType   = $rewardItems[$prizeItemId]['type'];
		$data['prizeImg']  = Common::getAttachPath().$rewardItems[$prizeItemId]['img_big'];
		$data['prizeType'] =   $prizeType;
		if(Activity_Service_SummerHoliday::PRIZE_MIN == $prizeType){
			$data['prizeType'] = Activity_Service_SummerHoliday::PRIZE_POINTS;
		}
		$data['prizeId']     = $info['prizeId'];
		$data['prizeItemId'] = $prizeItemId;
		$data['title'] = $rewardItems[$prizeItemId]['name'];
		
		
		if (Activity_Service_SummerHoliday::isNoPrize($rewardItems[$prizeItemId]) ) {
        	$data['status'] = 1;
        	$data['prizeType'] = Activity_Service_SummerHoliday::PRIZE_MIN_TYPE_NO_PRIZE;
        	self::_unlock(self::USER_LOCK, $info['puuid']);
        	$this->output(0, '没有中奖', $data);
        }
        
        $msg = array('prizeType=>' => $prizeType, 'prizeItemId'=>$prizeItemId, 'rewardItems'=>$rewardItems);
        Util_Log::info(__CLASS__, self::GET_PRIZE_FILE_LOG, $msg);
		if($prizeType == Activity_Service_SummerHoliday::PRIZE_MIN){			
			Activity_Service_SummerHoliday::sendPointPrize($info['puuid'], $currentActivityId, $rewardItems[$prizeItemId]['amount']);
		}elseif ($prizeType == Activity_Service_SummerHoliday::PRIZE_TICKETS){
			$this->sendTicketReward ( $info['puuid'], $activityInfo, $rewardItems, $prizeItemId );
		}elseif($prizeType == Activity_Service_SummerHoliday::PRIZE_POINTS){
			Activity_Service_SummerHoliday::sendPointPrize($info['puuid'], $currentActivityId, $rewardItems[$prizeItemId]['amount']);
		}
		if ($awardLockKey) {
			self::_unlock(self::AWARD_LOCK, $awardLockKey);
		}
		self::_unlock(self::USER_LOCK, $info['puuid']);
		if($result){
			$data['status'] = 1;
			$this->output(0, '刮奖成功', $data);
		}else{
			$this->output(0, '刮奖失败', $data);
		}
	
	}
	
    private function updateUserDataReward($info, $currentActivityId, $userInfo) {
		$userInfo['reward'][$info['prizeId']] = 2;
		$updateData[Activity_Service_UserData::DATA] = json_encode($userInfo);
		$queryParams[Activity_Service_UserData::UUID] = $info['puuid'];
		$queryParams[Activity_Service_UserData::ACTIVITY_ID] = $currentActivityId;
		Activity_Service_UserData::updateBy($updateData, $queryParams);
	}
	
	private function sendTicketReward($uuid, $activityInfo, $rewardItems, $prizeItemId) {
		$amount      = $rewardItems[$prizeItemId]['amount'];
		$startTime   = $rewardItems[$prizeItemId]['start_time'];
		$endTime     = $rewardItems[$prizeItemId]['end_time'];
		return Activity_Service_SummerHoliday::sendTicketPrize($uuid, $activityInfo, $amount, $startTime, $endTime);
	}
	
	private static function isUserCanGainPrize($actId, $prizeId, $prizeItemId, $rewardItems) {
		if (-1 == $prizeItemId) return false; // 最低奖项
		
		//发送间隔时间控制
		$interval = $rewardItems[$prizeItemId]['interval']; // 发放最小间隔,单位：秒
		if ($interval > 0) {
			$currentTime  = Common::getTime();
			$lastPrizeTime = self::_getLastPrizeTime($actId, $prizeId, $prizeItemId);
			if ( ($currentTime - $lastPrizeTime) <= $interval ) {
				return false;
			}
		}
		
		// 最大发送数量控制
		$maxSendCount = $rewardItems[$prizeItemId]['count']; 
		if ($maxSendCount > 0) {
			$sendCount = self::_getPrizeItemCount($actId, $prizeId, $prizeItemId);
			if ($sendCount >= $maxSendCount) {
				return false;
			}
		}
		
		// 抽奖次数达到指定次数后，才开放此奖品
		$limitSendNum = $rewardItems[$prizeItemId]['control'];
		if ($limitSendNum > 0) {
			$prizedTimes = self::_getPrizeCount($actId, $prizeId);
			if($prizedTimes < $limitSendNum){
				return false;
			}
		}
		
		return true;
	}
	
	private function saveRewardLog($info, $currentActivityId, $rewardItems, $prizeItemId) {
        $amount      = $rewardItems[$prizeItemId]['amount'];
        $startTime   = $rewardItems[$prizeItemId]['start_time'];
        $endTime     = $rewardItems[$prizeItemId]['end_time'];
        $rewardLogData[Activity_Service_RewardLog::ACTIVITY_ID] = $currentActivityId;
        $rewardLogData[Activity_Service_RewardLog::UUID] = $info['puuid'];
        $rewardLogData[Activity_Service_RewardLog::CREAYTE_TIME] = Common::getTime();
        $rewardLogData[Activity_Service_RewardLog::CUSTOM_KEY]  = Activity_Service_SummerHoliday::getLogCustomKey($info['prizeId'], $prizeItemId);
        $rewardLogData[Activity_Service_RewardLog::CUSTOM_COUNT]= $amount;
        if (!Activity_Service_SummerHoliday::isNoPrize($rewardItems[$prizeItemId])) {
        	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$info['puuid']));
        	$rewardLogData[Activity_Service_RewardLog::REWARD] 
        		= json_encode(array(
        				'name'=>$rewardItems[$prizeItemId]['name'],	// 奖品名
        				'nickname'=>$userInfo['nickname']			// 用户昵称
        		));
        }
       	return  Activity_Service_RewardLog::insert($rewardLogData);
	}
	
	private function isCanPrize($userInfo, $rewardConfig) {
		$currentDayTaskId = strtotime(date('Y-m-d 00:00:00'));	
		if($rewardConfig['condtion'] == Activity_Service_SummerHoliday::REWARD_CONDITION_DAILY){
			$status = Activity_Service_SummerHoliday::calculateDailyRewardStatus($userInfo['day_task'][$currentDayTaskId]);
		}elseif($rewardConfig['condtion'] == Activity_Service_SummerHoliday::REWARD_CONDITION_CONTINUE_FINISHED){
			$status = Activity_Service_SummerHoliday::calculateContinueDailyRewardStatus($userInfo['day_task'], $rewardConfig['conti_finish'], $currentDayTaskId);
		}
		return $status;
	}
	
	private function getPrizeItemId($rewardItems) {
		//奖励数组
		$prizeArr = array();
		$leastPrizeId = null;
		foreach ($rewardItems as $key=>$val){
			if (Activity_Service_SummerHoliday::PRIZE_MIN == $val['type']) { // 前提条件：必然会有一个且只有一个最低奖项
				$leastPrizeId = $key;
			}
			else {
				$prizeArr[$key] = $val['probability']; 
			}
		}
		$prizeItemId = $this->randPrize($prizeArr);
		return array($prizeItemId, $leastPrizeId);
	}
	
	private function randPrize($prizeArr) {
		$result = -1;
		$randNum = mt_rand(1, Activity_Service_SummerHoliday::PROBABILITY_COUNT);
		$curRandNum = 0;
		
		foreach ($prizeArr as $key => $val) {
			$curRandNum += $val;
			if ($randNum <= $curRandNum) {
				$result = $key;
				break;
			}
		}
		return $result;
	}
	
	private function checkRequesParam($info) {	
		$data['status'] = 0;
		if(!$info['puuid']){
			$this->output(0, '非法请求2', $data);
		}
		if(!$info['sp']){
			$this->output(0, '非法请求3', $data);
		}
	}

	/**
	 * 统计跳转
	 */
	public function tjAction(){
		$url = html_entity_decode(html_entity_decode($this->getInput('_url')));
		if (strpos($url, '?') === false) {
			$url = $url.'?t_bi='.$this->getSource();
		} else {
			$url = $url.'&t_bi='.$this->getSource();
		}
		$this->redirect($url);
	}	
	
	/**
	 * 获取最后一个发送的奖品的时间
	 * @param unknown $actId
	 * @param unknown $prizeId
	 * @param unknown $prizeItemId
	 * @return number
	 */
	private static function _getLastPrizeTime($actId, $prizeId, $prizeItemId) {
		$params[Activity_Service_RewardLog::ACTIVITY_ID] = $actId;
		$params[Activity_Service_RewardLog::CUSTOM_KEY] = Activity_Service_SummerHoliday::getLogCustomKey($prizeId, $prizeItemId);
		$lastReward = Activity_Service_RewardLog::getBy($params, array('id'=>'DESC'));
		if ($lastReward[Activity_Service_RewardLog::CREAYTE_TIME]) {
			return $lastReward[Activity_Service_RewardLog::CREAYTE_TIME];
		} else {
			return 0;
		}
	}
	
	/**
	 * 获取某类奖品的发送数量
	 * @param unknown $actId
	 * @param unknown $prizeId
	 * @param unknown $prizeItemId
	 * @return string
	 */
	private static function _getPrizeItemCount($actId, $prizeId, $prizeItemId) {
		$params[Activity_Service_RewardLog::ACTIVITY_ID] = $actId;
		$params[Activity_Service_RewardLog::CUSTOM_KEY] = Activity_Service_SummerHoliday::getLogCustomKey($prizeId, $prizeItemId);
		return Activity_Service_RewardLog::count($params);
	}
	
	/**
	 * 已经发送的奖品
	 * @param unknown $actId
	 * @param unknown $prizeId
	 * @return number
	 */
	private static function _getPrizeCount($actId, $prizeId) {
		$cache = Cache_Factory::getCache();
		return $cache->get(self::_getPrizeCountCacheKey($actId, $prizeId));
	}
	
	/**
	 * 增加发送奖励的计数，以便于”做抽奖次数达到指定次数后，才开放奖品“的控制
	 * @param unknown $actId
	 * @param unknown $prizeId
	 * @param unknown $time
	 */
	private static function _incPrizeCount($actId, $prizeId, $time) {
		$cache = Cache_Factory::getCache();
		$key = self::_getPrizeCountCacheKey($actId, $prizeId);
		$result = $cache->increment($key);
		$cache->expireAt($key, $time);
		return $result;
	}
	
	private static function _getPrizeCountCacheKey($actId, $prizeId) {
		return "Sum_Act:".$actId.":".$prizeId;
	}
	
	/**
	 * 抽奖时有两种情况需要加锁：
	 * 1、防止一个恶意用户同时发送多次抽奖请求
	 * 2、防止不同用户同时获得限量奖励
	 * @param unknown $key
	 * @return boolean 是否获得锁
	 */
	
	const USER_LOCK = "summer:user:";
	const AWARD_LOCK = "summer:reward:";
	
	private function _lock($type, $key, $lockExpireTime=2) {
		$cache = Cache_Factory::getCache();
		return $cache->lock($type.$key, $lockExpireTime);
	}
	
	private function _unlock($type, $key) {
		$cache = Cache_Factory::getCache();
		$cache->unlock($type.$key);
	}
}