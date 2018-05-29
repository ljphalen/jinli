<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 赠送系统-返利活动-登录客户端-首次登录
 * @author fanch
 *
 */
 class Task_Rules_LoginClientFirst extends Task_Rules_Base {

     const USER_LOGIN_ONCE = 1;
 	
 	/**
 	 * 解析规则方法
 	 * @see Task_Rules_Base::parseRule()
 	 */
	public function onCaculateGoods(){
		//是否赠送过
		$filterResult = $this->isSendOver();
		
		$debugMsg = array(
		        'msg' => "判断用户[{$this->mRequest['uuid']}]是否赠送过",
		        'taskId' => $this->mTaskRecord['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if($filterResult){ return array();}
		
		//参与用户过滤
		$filterResult = $this->filterUserByUuid();
		
		$debugMsg = array(
		        'msg' => "判断用户[{$this->mRequest['uuid']}是否允许参加本次任务",
		        'taskId' => $this->mTaskRecord['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if(!$filterResult){ return array();}
		
		//游戏大厅版本判断
		$filterResult = $this->isGameClientVersion();
		
		$debugMsg = array(
		        'msg' => "判断用户[{$this->mRequest['uuid']}]请求的客户端是否允许参加本次任务",
		        'taskId' => $this->mTaskRecord['id'],
		        'clientVersion' => $this->mRequest['clientVersion'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		//首次登录判断
		$filterResult = $this->isFirstLogin();
		
		$debugMsg = array(
		        'msg' => "判断用户[{$this->mRequest['uuid']}]是否是首次登录",
		        'taskId' => $this->mTaskRecord['id'],
		        'filterResult' => $filterResult
		);
		$this->debug($debugMsg);
		
		if(!$filterResult){ return array();}
		
		//计算规则
		$sendGoods = $this->caculateGoodsInternal();
		
		$debugMsg = array(
		        'msg' =>  "判断用户[{$this->mRequest['uuid']}]本次任务赠送的物品",
		        'taskId' => $this->mTaskRecord['id'],
		        'sendGoods' => $sendGoods
		);
		$this->debug($debugMsg);
		
		return $sendGoods;
	}


	/**
	 * 游戏客户端版本过滤
	 * @return boolean
	 */
	private function isGameClientVersion(){
	    $taskVersion = json_decode($this->mTaskRecord['game_version'], true);
	    $clientVersion = $this->isClientVersion($taskVersion, $this->mRequest['clientVersion']);
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
	    if($taskVersion[$configVersion[$clientVersion]]){
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
	private function isFirstLogin(){
        $task = $this->mTaskRecord;
        $uuid = $this->mRequest['uuid'];
	    $startTime = $task['hd_start_time'];
	    $endTime = $task['hd_end_time'];
        $clientVer = $this->mRequest['clientVersion'];
	    $cache = Cache_Factory::getCache();
	    $cacheKey = Util_CacheKey::getUserInfoKey($uuid) ; //获取用户的uuid
	    $lastLoginTime = strtotime($cache->hGet($cacheKey, 'lastLoginTime' )); //最后登录时间
	    $loginLogParams['create_time'][0] = array('>=', $startTime);
	    $loginLogParams['create_time'][1] = array('<=', $endTime);
	    $loginLogParams['uuid'] = $uuid;
        list($flag, $allowVersions) = $this->getAllowVersion();
        if($flag){
            $loginData = Account_Service_User::getsUserLoginLog($loginLogParams);
            $loginTimes = $this->loginTimes($loginData, $allowVersions);
            $loginVer = Common::getClientVersion($clientVer);
            $everyLoginTotal = isset($loginTimes[$loginVer]) ? $loginTimes[$loginVer] : 0;
        } else {
            $everyLoginTotal = Account_Service_User::countUserLoginLog($loginLogParams);
        }
	    //判断该用户在活动期间是否是首次登陆
	    if($lastLoginTime >= $startTime && $lastLoginTime <= $endTime && $everyLoginTotal == self::USER_LOGIN_ONCE){
	        return true;
	    }
	    return false;
	}

     private function loginTimes($loginData, $allowVersion){
         $loginTimes = array();
         foreach($loginData as $item){
             $gameVer = Common::getClientVersion($item['game_ver']);
             if(in_array($gameVer, $allowVersion)){
                 $loginTimes[$gameVer] += 1;
             }
         }
         return $loginTimes;
     }

     private function getAllowVersion(){
         $taskVersion = json_decode($this->mTaskRecord['game_version'], true);
         if($taskVersion[1]){
             array(false, array());
         }
         unset($taskVersion[1]);
         $configVersion =  Common::getConfig('taskConfig', 'version');
         foreach ($configVersion as $key => $value) {
             if(in_array($value, $taskVersion)){
                 $allVersions[] = $key;
             }
         }
         return array(true, $allVersions);
     }

	/**
	 * 查看该账号A券交易（该任务是否对与对应用户已将赠送完毕）
	 * @return boolean
	 */
	private  function isSendOver(){
		$params = array();
		$params['uuid'] = $this->mRequest['uuid'];
		$params['send_type'] = Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY;
		$params['sub_send_type'] = $this->mTaskRecord['id'];
		$sendResult = Client_Service_TicketTrade::getBy($params);
		if($sendResult){
			return true;
		}
		return false;
	}
}
