<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-登录客户端-首次登录
 * @author fanch
 * 
 * 请求参数内容如下
 * $reqeust =array(
 *   'uuid'=>$uuid, 
 *	 'clientVersion'=>$clientVersion, 
 * )
 */
 class Task_Rules_LoginClientFirst extends Task_Rules_Base {

     const USER_LOGIN_ONCE = 1;
 	
 	/**
 	 * 解析规则方法
 	 * @see Task_Rules_Base::parseRule()
 	 */
	public function onCaculateGoods(){
		$task = $this->mTaskRecord;
		$request = $this->mRequest;
		//是否赠送过
		$filterResult = $this->isSendOver($task, $request['uuid'], Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY);
		
		$debugMsg = array(
		        'msg' => "判断用户[{$request['uuid']}]是否赠送过",
		        'taskId' => $task['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if($filterResult){ return array();}
		
		//参与用户过滤
		$filterResult = $this->filterUserByUuid($task, $request['uuid']);
		
		$debugMsg = array(
		        'msg' => "判断用户[{$request['uuid']}]是否允许参加本次任务",
		        'taskId' => $task['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if(!$filterResult){ return array();}
		
		//游戏大厅版本判断
		$filterResult = $this->isGameClientVersion($task, $request['clientVersion']);
		
		$debugMsg = array(
		        'msg' => "判断用户[{$request['uuid']}]请求的客户端是否允许参加本次任务",
		        'taskId' => $task['id'],
		        'clientVersion' => $request['clientVersion'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		//首次登录判断
		$filterResult = $this->isFirstLogin($task, $request['uuid']);
		
		$debugMsg = array(
		        'msg' => "判断用户[{$request['uuid']}]是否是首次登录",
		        'taskId' => $task['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if(!$filterResult){ return array();}
		
		//计算规则
		$sendGoods = $this->caculateGoodsInternal($task, $request['uuid']);
		
		$debugMsg = array(
		        'msg' => "通过规则计算用户[{$request['uuid']}]本次任务赠送的物品",
		        'taskId' => $task['id'],
		        'sendGoods' => $sendGoods
		);
		$this->debug($debugMsg);
		
		return $sendGoods;
	}


	/**
	 * 游戏客户端版本过滤
	 * @param array $task
	 * @param string $clientVersion
	 * @return boolean
	 */
	private function isGameClientVersion($task, $clientVersion){
	    $taskVersion = json_decode($task['game_version'], true);
	    $clientVersion = $this->isClientVersion($taskVersion, $clientVersion);
	    if(!$clientVersion){
	        return false;
	    }
	    return true;
	}
	
	/**
	 * 验证客户端版本
	 * @param array   $taskVersion
	 * @param string  $clientVersion
	 * @return boolean
	 */
	private function  isClientVersion($taskVersion, $clientVersion){
	    if(!$taskVersion || !$clientVersion){
	        return false;
	    }
	
	    if($taskVersion[1]){
	        return true;
	    }
	
	    $clientVersion = Common::getClientVersion($clientVersion);
	    $configVersion =  Common::getConfig('taskConfig', 'version');
	    if($taskVersion[$configVersion]){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	
	/**
	 * 任务活动进行期间用户是否首次登录客户端
	 * @param array $task
	 * @param string $uuid
	 * @return boolean
	 */
	private function isFirstLogin($task, $uuid){
	    $taskStartTime = date('Y-m-d', $task['hd_start_time']);
	    $taskEndTime = date('Y-m-d', $task['hd_end_time']);
	    $startTime = strtotime($taskStartTime.' 00:00:00');
	    $endTime = strtotime($taskEndTime.' 23:59:59');
	    $cache = Cache_Factory::getCache();
	    $cacheKey = $uuid.'_user_info' ; //获取用户的uuid
	    $lastLoginTime = strtotime($cache->hGet($cacheKey, 'lastLoginTime' )); //最后登录时间
	    $loginLogParams['create_time'] = array('>=', $startTime);
	    $loginLogParams['create_time'] = array('<=', $endTime);
	    $loginLogParams['uuid'] = $uuid;
	    $everyLoginTotal = count(Account_Service_User::getsUserLoginLog($loginLogParams));
	    //判断该用户在活动期间是否是首次登陆
	    if($lastLoginTime >= $startTime && $lastLoginTime <= $endTime && $everyLoginTotal == self::USER_LOGIN_ONCE){
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 查看该账号A券交易（该任务是否对与对应用户已将赠送完毕）
	 * @param array $task
	 * @param string $uuid
	 * @param string $activityTask
	 * @return boolean
	 */
	private  function isSendOver($task, $uuid, $activityTask){
		$params = array();
		$params['uuid'] = $uuid;
		$params['send_type'] = $activityTask;
		$params['sub_send_type'] = $task['id'];
		$sendResult = Client_Service_TicketTrade::getBy($params);
		if($sendResult){
			return true;
		}
		return false;
	}
}
