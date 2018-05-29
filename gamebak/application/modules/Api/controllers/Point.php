<?php
if (! defined('BASE_PATH')) exit('Access Denied!');
class PointController extends Api_BaseController {
    
    const LOG_FILE = 'yan';
    
    private $gainTypes = array(
                    '1'=>'福利任务',
                    '2'=>array(
                                    '1'=>'连续登录',
                                    '2'=>'每日任务-下载',
                                    '3'=>'每日任务-评论',
                                    '4'=>'每日任务-分享'
                                    ),
                    '3'=>'活动任务',
                    '4'=>'金立游戏大厅赠送',
                    '5'=>'积分抽奖',
                    '6'=>'商城兑换',
    				'7'=> '生日礼物',
    		        '8'=>'活动兑换'
    );
    
    private $consumeTypes = array(
                    '1'=>'积分兑换',
                    '2'=>'积分抽奖'
    );
    
    /**
     * 我的每日任务列表
     */
    public function myDailyTaskListAction() {		
    	$info = $this->getInput(array('puuid', 'uname', 'imei','sp','serverId','clientId'));   
    	$data = $this->getTaskData($info);
	
    }
    
    /**
     * 组装任务数据
     * @param unknown_type $info
     */
    private function getTaskData($info){
    	
    	$data['success']     = true;
    	$data['msg']         = '';
    	$data['sign']        = 'GioneeGameHall';
    	$data['data']        = array();
    	
     	$clientVersion = Common::parseSp($info['sp'], 'game_ver');
		if(strnatcmp($clientVersion, '1.5.5') < 0){
			$this->clientOutput(array());
		}
		
		$online = false;
		if($info['puuid'] && $info['imei']){
			$online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');
		}
		
    	if(!$online){
    		$data['code']    ='0';
    	}
    	
    	$webRoot = Common::getWebRoot();
    	$data['data']['explainUrl']= $webRoot.'/client/task/dailyTaskIntro';
    	$data['data']['uname']     = $online?$info['uname']:'';
    	
  
    	//连续登录任务的数据
    	$continueLoginData = $this->getContinueLoginData($info, $online);
    	//其他日常任务的数据
    	$dailyTaskListData =  $this->getDailyListData($info['puuid'], $online);
    	
    	if(is_array($continueLoginData) && count($continueLoginData)){
    		$data['data']['items'][] = $continueLoginData;
    		foreach ($dailyTaskListData as $key=>$val){
    			$data['data']['items'][$key+1] = $val;
    		}
    	}else if (count($dailyTaskListData) == 0) {
    		$data['data']['items'] = array();
    	}else{
    		foreach ($dailyTaskListData as $key=>$val){
    			$data['data']['items'][$key] = $val;
    		}
    	}
    	
    	$this->clientOutput($data);
    }
   
    
    /**
     * 获取日常任务的组装数据
     */
    private function getDailyListData($uuid, $online){
    	
    	$dailyConfig = Client_Service_DailyTaskConfig::getsBy(array('status'=>1));
    	$data = array();
    	$cache = Cache_Factory::getCache();
        foreach ($dailyConfig as $key=> $val){
        	$data[$key]['id'] = $val['id'];
        	$data[$key]['title'] = html_entity_decode($val['task_name'], ENT_QUOTES);
        	$data[$key]['detail'] = html_entity_decode($val['resume'], ENT_QUOTES);
        	$data[$key]['giveType']=$val['send_object'] == 1?'point':'aticket';
        	
        	$dailyLimit = intval($cache->hGet('dailyTask'.$val['id'], 'dailyLimit'));
        	//当前奖励值
        	if($val['send_object'] == 1){
        		$data[$key]['value'] = intval($val['points']);
        		$data[$key]['pointTotal'] =$val['points']*$dailyLimit;
        	}else{	
        		$denomination = intval($cache->hGet('dailyTask'.$val['id'], 'denomination'));
        		$data[$key]['value'] = $denomination;  
        		$data[$key]['aticketTotal'] =$denomination*$dailyLimit;
        	}
        	$data[$key]['pointTotal']   =0;
    		$data[$key]['aticketTotal'] =0;
    		$logRs = array();
            if($online){
            	$logParams['uuid']    = $uuid ;
            	$logParams['status'] = 1;
            	$logParams['task_id'] = $val['id'];
            	$logParams['create_time'] = array(array('>=', strtotime(date('Y-m-d 00:00:01')) ),array('<=', strtotime(date('Y-m-d 23:59:59')))) ;
            	$logRs = Client_Service_DailyTaskLog::getsBy($logParams);
            	foreach ($logRs as $va){
            		if($va['send_object'] == 1){
            			$data[$key]['pointTotal'] += $va['denomination'];
            		}else{
            			$data[$key]['aticketTotal'] +=$va['denomination'];
            		}
            	}
           }      
        	$data[$key]['progress'] =$online?((count($logRs) > $dailyLimit)?$dailyLimit:count($logRs)):0;
        	$data[$key]['totalProgress'] = $dailyLimit;
        	if($val['id'] == 4){
        		$data[$key]['viewType'] = 'CategoryDetailView';
        		$category_id = Game_Service_Config::getValue('daily_task_share_category_id');
        		$data[$key]['param']['contentId'] = $category_id ;
        		$data[$key]['param']['gameId'] ='' ;
        	}elseif($val['id'] == 2){
        		$data[$key]['viewType'] = 'CategoryDetailView';
        		$category_id = Game_Service_Config::getValue('daily_task_download_category_id');
        		$data[$key]['param']['contentId'] = $category_id ;
        		$data[$key]['param']['gameId'] ='' ;
        	}elseif($val['id'] == 3){
        		$data[$key]['viewType'] = 'MyGameView';
        		$data[$key]['param']['contentId'] ='' ;
        		$data[$key]['param']['gameId'] ='' ;
        	}
        }
          
      return $data;	
    }
    
    /**
     * 连续登录七天的总数
     */
    private function getContinueLoginTotal($baseAwardConfig, $online, $continueLoginDay){
    	if($online){
    		$loginDateTime = Common::loginDate($continueLoginDay);
    	}else{
    		$loginDateTime = Common::loginDate(1);
    	}
    	foreach ($baseAwardConfig as $key=>$val){
    		$activityParams['status'] = 1;
    		$activityParams['start_time'] = array('<=', strtotime($loginDateTime[$key]));
    		$activityParams['end_time']   = array('>=', strtotime($loginDateTime[$key]));
    		$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
    		if($activityConfig){
    			if($val['send_object'] == 1){
    				if($activityConfig['award_type'] == 1){
    					$tmpPoint+= $val['denomination'] + $activityConfig['award'];
    				}else{
    					$tmpPoint+= $val['denomination'] * $activityConfig['award'];
    				}
    			}else{
    				if($activityConfig['award_type'] == 1){
    					$tmpTicket+= $val['denomination'] + $activityConfig['award'];
    				}else{
    					$tmpTicket+= $val['denomination'] * $activityConfig['award'];
    				}
    			}
    		}else{
    			if($val['send_object'] == 1){
    				$tmpPoint+= $val['denomination'];
    			}else{
    				$tmpTicket+= $val['denomination'];
    			}
    		}
    	}
    	return array($tmpPoint, $tmpTicket);
    }
    
    /**
     * 获取连续登录的组装数据
     */
    private function getContinueLoginData($info, $online){	
    	$uuid = $info['puuid'];
    	if(!$online){
    		return array();
    	}  
    	$time = Common::getTime();
    	$loginConfig = Client_Service_ContinueLoginCofig::getBy(array('status'=>1));
    	$data = array();
    	if($loginConfig){
    		$cache = Cache_Factory::getCache();
        	$cacheHash = Util_CacheKey::getUserInfoKey($uuid) ;
        	$continueLoginDay = $cache->hGet($cacheHash, 'continueLoginDay');
    		$baseAwardConfig = json_decode($loginConfig['award_json'], true);

    		if(intval($continueLoginDay) < 1){
    			$this->continueLoginSend($info, $cache, $cacheHash, $time, $continueLoginDay);
    			$continueLoginDay = 1;
    		}
    		//当前的奖励数组
    		$currentAwardArr = $baseAwardConfig[$continueLoginDay-1];
    		
    		//连续登录7天总额
	    	list($tmpPoint, $tmpTicket) = $this->getContinueLoginTotal($baseAwardConfig, $online, $continueLoginDay);
    	    
	    	if($tmpPoint > 0 && $tmpTicket > 0){
	    		$data['title'] = '连续登录七天送'.$tmpPoint.'积分，'.$tmpTicket.'A券';
	    	}elseif($tmpPoint > 0 ){
	    		$data['title'] = '连续登录七天送'.$tmpPoint.'积分';
	    	}elseif($tmpTicket > 0 ){
	    		$data['title'] = '连续登录七天送'.$tmpTicket.'A券';
	    	}
    		
        	$data['id'] = '1';
    		$data['detail'] = '';
    		$data['giveType']=$currentAwardArr['send_object'] == 1?'point':'aticket';
    		
    		//当天奖励值
    		list($currentPoints, $currentTicket) = $this->getContinueCurrentPrizeValue($uuid, $loginConfig, $online, $continueLoginDay);
    		Common::log('$currentTicket='.$currentTicket.'$currentPoints='.$currentPoints, 'myPoint.log');
    		if($currentPoints > 0 ){
    			$data['value'] = $currentPoints;
    		}elseif($currentTicket > 0){
    			$data['value'] = $currentTicket;
    		}else{
    			$data['value'] =0;
    		}
    	
    		//已获取的奖励总值
    		$data['pointTotal'] = 0;
    		$data['aticketTotal'] = 0;
    		if($online){
    			list($totalPoint, $totalTicket) = $this->getHadContinueLoginTotal($uuid, $continueLoginDay);
    			$data['pointTotal']   = $totalPoint;
    			$data['aticketTotal'] = $totalTicket;
    		}
    		
    		//奖励进度
    		$webRoot = Common::getWebRoot();
    		$data['progress'] = $online?$continueLoginDay:0;
    		$data['totalProgress'] = 7;	
    		$data['viewType'] = 'WebView';
    		$data['param']['url'] = $webRoot.'/client/task/continueLogin'; 
    		$data['param']['title'] = '连续登录';
    		$data['param']['source'] = 'continuouslogin';
    		
    		//判断连续登录的节日活动是否开启,是否在有效期
    		$activityParams['status'] = 1;
    		$activityParams['start_time'] = array('<=', $time);
    		$activityParams['end_time']   = array('>=', $time);
    		$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
    		if($activityConfig){
    			$data['detail'] = html_entity_decode($activityConfig['task_name'], ENT_QUOTES);
    			//award_type=1 加，2乘
    			if($activityConfig['award_type'] == 1){
    				//send_object=1 积分 2 A券
	    			if($currentAwardArr['send_object'] == 1){
	    				$data['value'] = $currentAwardArr['denomination']+$activityConfig['award'];
	    			}else{
	    				$data['value'] = $currentAwardArr['denomination']+$activityConfig['award'];
	    			}
    			}else{
    				//send_object=1 积分 2 A券
    				if($currentAwardArr['send_object'] == 1){
    					$data['value'] = $currentAwardArr['denomination']*$activityConfig['award'];
    				}else{
    					$data['value'] = $currentAwardArr['denomination']*$activityConfig['award'];
    				}
    			}
    		}
    	}
    	return $data;
    }
    
    /**
     * 连续登录赠送
     * @param unknown_type $uuid
     */
    private function continueLoginSend($info, $cache, $cacheHash, $time, $continueLoginDay){
    	
    	//验证客户上报的数据
    	$rs = Common::verifyClientEncryptData($info['puuid'], $info['uname'], $info['clientId']);
    	if(!$rs){
    		return array();
    	}
    	
    	$this->contineLoginDaysIntial($cache, $cacheHash, $time, $continueLoginDay);
    	$configArr = array('uuid'=> $info['puuid'],
    			'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
    			'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
    	);
    	$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
    	$activity->sendTictket();
    }
    
    private function contineLoginDaysIntial(&$cache, $cacheKey, $time, $continueLoginDay){

    	if($continueLoginDay == false){
    		$cache->hSet($cacheKey, 'continueLoginDay', 1); //连续登录天数
    		$cache->hSet($cacheKey, 'continueLoginTotal', 1);//累计连续登录天数
    		$cache->hSet($cacheKey, 'everyLoginTotal', 1);   //每天登录的次数
    
    	}else{
    		$days = $this->getContinueLoginDays ($cache, $cacheKey, $time);
    		//当前登录
    		if($days == 0){
    			$cache->hIncrBy($cacheKey, 'everyLoginTotal', 1);
    			//连续登录
    		}elseif($days == 1){
    			//七天一个周期
    			if($continueLoginDay >= 7){
    				$cache->hSet($cacheKey, 'continueLoginDay', 1);
    			}else{
    				$cache->hIncrBy($cacheKey, 'continueLoginDay', 1);
    			}
    			$cache->hIncrBy($cacheKey, 'continueLoginTotal', 1);
    			$cache->hSet($cacheKey, 'everyLoginTotal',1);
    			//其他的
    		}else{
    			$cache->hSet($cacheKey, 'continueLoginDay', 1);
    			$cache->hSet($cacheKey, 'continueLoginTotal', 1);
    			$cache->hSet($cacheKey, 'everyLoginTotal', 1);
    		}
    	}
    	//最后登录时间
    	$cache->hSet($cacheKey, 'lastLoginTime', date('Y-m-d H:i:s',$time) );
    	
    
    }
    
    
    private function getContinueLoginDays(&$cache, $cacheKey, $time) {
    	//最后的登录时间
    	$lastLoginTime = $cache->hGet($cacheKey, 'lastLoginTime');
    	$days = Common::diffDate($lastLoginTime,  date('Y-m-d H:i:s', $time));
    	return $days;
    }
    
   
    
    /**
     * 获取当次奖励的的积分
     */
    private function getContinueCurrentPrizeValue($uuid, $basePrizeConfig, $online, $continueLoinDay){
    
    	$basePrizeConfig = json_decode($basePrizeConfig['award_json'], true);
    	//取得用户的七天登录周期
    	$loginDateTime =  Common::loginDate($continueLoinDay);
    	$basePrize = $basePrizeConfig[$continueLoinDay-1];
    	$activityParams['status'] = 1;
    	$activityParams['start_time'] = array('<=', strtotime($loginDateTime[$continueLoinDay-1]));
    	$activityParams['end_time']   = array('>=', strtotime($loginDateTime[$continueLoinDay-1]));
    	$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
    	$currentTicket = 0;
    	$currentPoints = 0;
    	
    	
      
        $logParams['uuid']    = $uuid ;
        $logParams['status'] = 1;
        $logParams['task_id'] = 1;
        $logParams['days'] = $continueLoinDay;
        $logParams['create_time'] = array(array('>=', strtotime(date('Y-m-d 00:00:01')) ),array('<=', strtotime(date('Y-m-d 23:59:59')))) ;
        $logRs = Client_Service_DailyTaskLog::getBy($logParams);
        if($logRs){
        	//send_object=1 积分 2 A券
        	if($logRs['send_object'] == 1){
        		$currentPoints += $logRs['denomination'];
        	}else{
        		$currentTicket += $logRs['denomination'];
        	}
        }else{
        	if($basePrize['send_object'] == 1){
        		$currentPoints = $basePrize['denomination'];
        	}else{
        		$currentTicket = $basePrize['denomination'];
        	}
        	//节日活动开启
        	if($activityConfig){
        		//award_type=1 加，2乘
        		if($activityConfig['award_type'] == 1){
        			//send_object=1 积分 2 A券
        			if($basePrize['send_object'] == 1){
        				$currentPoints = $basePrize['denomination'] + $activityConfig['award'];
        			}else{
        				$currentTicket = $basePrize['denomination'] + $activityConfig['award'];
        			}
        		}else{
        			//send_object=1 积分 2 A券
        			if($basePrize['send_object'] == 1){
        				$currentPoints = $basePrize['denomination']*$activityConfig['award'];
        			}else{
        				$currentTicket = $basePrize['denomination']*$activityConfig['award'];
        			}
        		}
        	}
        }
    	
    	
    	return array($currentPoints, $currentTicket);
    }
    
    
    /**
     * 已经获取的连续登录奖励值
     * @param unknown_type $uuid
     * @param unknown_type $days
     */
    private function getHadContinueLoginTotal($uuid, $days){
    	//累计的奖励
    	$params['uuid']   = $uuid ;
    	$params['status'] = 1;
    	$params['task_id'] = 1;
    	$orderBy = array('create_time'=>'DESC', 'id'=>'DESC','days'=>'DESC');
    	list(, $logResult) = Client_Service_DailyTaskLog::getList(1, $days, $params , $orderBy);
    	$totalPoint = 0;
    	$totalTicket = 0 ;
    	foreach ($logResult as $val){
    		//当前奖励值
    		if($val['send_object'] == 1){
    			$totalPoint += $val['denomination'];
    		}else{
    			$totalTicket += $val['denomination'];
    		}
    	}
    	return array($totalPoint, $totalTicket);
    }

    
    /**
     * 能领取的积分与A券
     */
    private function getCanDailyTaskTotal($uuid, $online = false){
    	
    	$cache = Cache_Factory::getCache();    	
    	//每日任务的总积分
    	$currentDailyTaskPointTotal  = $cache->get('dailyTaskPointTotal');
    	//每日任务的总A券
    	$currentDailyTaskTicketTotal = $cache->get('dailyTaskTicketTotal');
    	//登录
    	if($online){
    		$logParams['uuid']    = $uuid ;
    		$logParams['status'] = 1;
    		$logParams['task_id'] = array('in', array('2', '3', '4'));
    		$logParams['create_time'] = array(array('>=', strtotime(date('Y-m-d 00:00:01')) ),array('<=', strtotime(date('Y-m-d 23:59:59')))) ;
    		$logRs = Client_Service_DailyTaskLog::getsBy($logParams);
    		$tmpPoints = 0;
    		$tmpTicket = 0;
    		foreach ($logRs as $val){
    			//send_object=1 积分 2 A券
    			if($val['send_object'] == 1){
    				$tmpPoints += $val['denomination'];
    			}else{
    				$tmpTicket += $val['denomination'];
    			}
    		}
    		$currentDailyTaskPointTotal  = $currentDailyTaskPointTotal - $tmpPoints;
    		$currentDailyTaskTicketTotal = $currentDailyTaskTicketTotal - $tmpTicket;
    	}
    	return array($currentDailyTaskPointTotal, $currentDailyTaskTicketTotal);
    }
    
  
    
    /**
     * 积分消费记录列表
     * @author yinjiayan
     */
    public function consumeListAction() {
        $uuid = $this->getInput('puuid');
        $this->checkOnline($uuid);
        
        $page = $this->getPageInput();
        $param = array('uuid' => $uuid);
        list ( $total , $dbData ) = Point_Service_Consume::getList($page, self::PERPAGE, $param);
        
        $data = array();
        foreach ( $dbData as $key => $value ) {
            $dataItem['time'] = intval($value['create_time']);
            $dataItem['point'] = intval($value['points']);
            $dataItem['subject'] = $this->getConsumeMsg($value['consume_type'], $value['consume_sub_type']);
            $data[$key] = $dataItem;
        }
        $hasnext = $this->hasNext($total, $page);
        $this->localOutput(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }
    
    private function getConsumeMsg($consumeType, $consumeSubType) {
        $consumeMsg = $this->consumeTypes[$consumeType];
        if (! $consumeMsg) {
        	return '';
        }
        if ($consumeType == 1 && !$consumeSubType) {
        	$goodsId = $consumeSubType;
        	$goods = Mall_Service_Goods::get($goodsId);
        	$consumeMsg = $consumeMsg.$goods['title'];
        }
        return $consumeMsg;
    }
    
    /**
     * 积分获得记录列表
     * @author yinjiayan
     */
    public function gainListAction() {
        $uuid = $this->getInput('puuid');
        $this->checkOnline($uuid);
        
        $page = $this->getPageInput();
        $param = array('uuid' => $uuid);
        list ( $total , $dbData ) = Point_Service_Gain::getList($page, self::PERPAGE, $param);
        
        $data = array();
        foreach ( $dbData as $key => $value ) {
            $dataItem['time'] = intval($value['create_time']);
            $dataItem['point'] = intval($value['points']);
            $dataItem['subject'] = $this->getGainMsg($value['gain_type'], $value['gain_sub_type'], $value['days']);
            $data[$key] = $dataItem;
        }
        $hasnext = $this->hasNext($total, $page);
        $this->localOutput(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }
    
    private function getGainMsg($gainType, $gainSubType, $days) {
        $gainMsg = $this->gainTypes[$gainType];
        if (is_array($gainMsg)) {
        	$gainMsg = $gainMsg[$gainSubType];
        }
        if (! $gainMsg) {
        	return '';
        }
        
        if ($gainMsg == $this->gainTypes['2']['1']) {
        	$gainMsg = $gainMsg.'第'.$days.'天';
        }
    	return $gainMsg;
    }
    
    /**
     * 积分抽奖接口
     */
    public function prizeStartAction(){
    	$request = $this->getInput(array('prizeId', 'puuid', 'uname', 'serverId', 'sp'));
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	//参数处理
    	if(!$request['uname']|| !$request['puuid'] || !$request['prizeId']){
    		$this->output(-1, '参数非法');
    	}
    	//登录处理
    	$onlineStatus = Account_Service_User::checkOnline($request['puuid'], $imei, 'uuid');
    	if(!$onlineStatus) { //未登录处理
    		$this->output(0,'', array('isLogin'=>false));
    	}
    	//获取用户积分
    	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$request['puuid']));
    	$userPoint = $userInfo['points'] ? intval($userInfo['points']) : 0;
    	//查询该活动是否进行中
    	$time = time();
    	$prizeData = Point_Service_Prize::getByPrize(array('id' => $request['prizeId'], 'status'=>1, 'start_time' => array('<=', $time), 'end_time' => array('>=', $time)));
    	if(!$prizeData) { //非法请求
    		$this->output(-1, '活动已结束');
    	}
    	//抽奖活动版本处理
    	$version = Yaf_Registry::get("apkVersion");
    	$apkVersion = $version ? $version : '';
    	$prizeVesion = $prizeData['version'] ? $prizeData['version'] : '1.5.5';
    	if (strnatcmp($apkVersion, $prizeVesion) < 0) { 
    		$this->output(-1, '活动已结束');
    	}
    	//可用积分判断
    	if($userPoint < $prizeData['point']){
    		//积分不足
    		$this->output(0,'', array('underPoints'=>true, 'totalPoints'=>$userPoint));
    	}
    	$context = array(
    		'serverId' => $request['serverId'],
    		'sp' => $request['sp'],
    		'uuid' => $request['puuid'],
    		'uname'=> $request['uname'],
    		'prizeId' => $prizeData['id'],
    		'point' => $prizeData['point'],
     		'time' => $time,
    	);
    	list($flag, $result)= Point_Service_Prize::runPrize($context);
    	//数据操作失败
    	if(!$flag) {
    		$this->output(-1, '操作失败');
    	}
    	//抽过奖的最新积分数据
    	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$request['puuid']));
    	$userPoint = $userInfo['points'] ? intval($userInfo['points']) : 0;
    	if($result['type'] == 0){ //未抽中奖品
    		$attachPath = Common::getAttachPath();
    		$this->output(0,'', array('configId'=>$result['id'], 'prizeType'=>$result['type'], 'prizeIndex'=>$result['pos'], 'prizeImg'=>$attachPath . $result['img'],'totalPoints'=>$userPoint));
    	}
    	$context['type'] = $result['type'];
    	$context['day'] = $result['day'];
    	$context['amount'] = $result['amount'];
    	$context['configId'] = $result['id'];
    	$context['logId'] = $result['logId'];
    	//处理抽中的A券跟积分
    	$afterResult = Point_Service_Prize::afterPrize($context);
    	if(!$afterResult) { //执行错误
    		$this->output(-1, '操作失败');
    	}
    	$response = $this->responsePrize($result);
    	$this->output(0, '', $response);
    }
    
    /**
     * 用户中奖数据近10条
     * 
     */
    public function prizeWinAction(){
    	$prizeId = $this->getInput('prizeId');
    	//参数处理
    	if(!$prizeId) $this->output(-1, '参数非法');
    	list(, $logs)= Point_Service_Prize::getListLog(1, 10, array('prize_id'=>$prizeId, 'prize_status'=>1), array('create_time' => 'DESC'));
    	$result = array();
    	if(!$logs) {
    		$this->output(0,'', array('list'=>array()));
    	}
    	$configData = Point_Service_Prize::getConfig($prizeId);
    	$configData = Common::resetKey($configData, 'id');
    	foreach ($logs as $item){
    		$uname = substr_replace($item['uname'], '****', 3, 4);
    		$title = $this->prizeTitle($configData[$item['prize_cid']]);
    		$result[] = sprintf('恭喜 %s, 人品爆发，抽中%s', $uname, $title);
    	}
    	$this->output(0,'', array('list'=>$result));
    }
    
    /**
     * 提交中奖用户收获地址
     */
    public function prizeSubmitAction(){
    	$request = $this->getInput(array('logId', 'ticket', 'name', 'phone', 'address'));
    	if(!$request['logId'] || !$request['ticket'] || !$request['name'] || !$request['phone'] || !$request['address']){
    		$this->output(-1, '参数非法');
    	}
    	$sign = strtr($request['ticket'], '-_', '+/');
    	$text = Common::encrypt($sign, 'DECODE');
    	$params = explode('|', $text);
    	$flag = $this->checkTicket($request['logId'], $params);
    	if(!$flag) $this->output(-1, '请求非法');
    	$data = array(
    		'receiver'=> $request['name'],
    		'mobile' => $request['phone'],
    		'address'=>	$request['address'],	
    	);
    	$result = Point_Service_Prize::updateLog($data, array('id'=>$request['logId']));
    	if(!$result) $this->output(-1, '提交失败');
    	$userInfo = array(
    			'receiver'=> $request['name'],
    			'receiverphone' => $request['phone'],
    			'address'=>	$request['address'],
    	);
    	$result = Account_Service_User::updateUserInfo($userInfo, array('uuid'=>$params[0]));
    	if(!$result) $this->output(-1, '提交失败');
    	//操作成功
    	$this->output(0, '提交成功');
    }
    
    /**
     * 中奖数据输出封装
     * @param array $data
     * 
     * @return 
     */
    private function responsePrize($data){
    	//获取最新积分
    	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$data['uuid']));
    	$attachPath = Common::getAttachPath();
    	$webroot = Common::getWebRoot();
    	$result = array(
    			'configId' => $data['id'],
    			'prizeIndex' => $data['pos'],
    			'prizeImg' => $attachPath . $data['img'],
    			'totalPoints' => $userInfo['points'] ? intval($userInfo['points']) : 0
    	);
    	switch ($data['type']){
    		case 1:
    			//抽中实物
    			$result['logId'] = $data['logId'];
    			$result['prizeType'] = 1;
    			$result['prizeName'] = $data['title'];
    			$result['receivingName'] = $userInfo['receiver'] ? $userInfo['receiver'] : '';
    			$result['receivingPhone'] = $userInfo['receiverphone'] ? $userInfo['receiverphone'] : '';
    			$result['receivingAddress'] = $userInfo['address'] ? $userInfo['address'] : '';
    			$sign = Common::encrypt("{$data['uuid']}|{$data['prize_id']}|{$data['id']}");
    			$sign = strtr($sign, '+/', '-_');
    			$result['ticket'] = $sign;
    			break;
    		case 2:
    			//A券赠送
    			$result['prizeType'] = 2;
    			$result['prizeName'] = $data['amount'];
    			$result['indate'] = $data['day'];
    			break;
    		case 3:
    			//积分赠送
    			$result['prizeType'] = 3;
    			$result['prizeName'] = $data['amount'];
    			break;
    	}
    	return $result;
    }
    
    private function prizeTitle($config){
    	$result = '';
    	switch ($config['type']){
    		case 1:
    			$result = sprintf(' %s ', $config['title']);
    			break;
    		case 2:
    			$result = sprintf(' %d A券', $config['amount']);
    			break;
    		case 3:
    			$result = sprintf(' %d 积分', $config['amount']);
    			break;
    	}
    	return $result;
    }
    
    private function checkTicket($logId, $params){
    	$log = Point_Service_Prize::getByLog(array('id'=> $logId));
    	if(!$log) return false;
    	if($log['uuid'] != $params['0']) return false;
    	if($log['prize_id'] != $params['1']) return false;
    	if($log['prize_cid'] != $params['2']) return false;
    	return true;
    }
    
}

?>