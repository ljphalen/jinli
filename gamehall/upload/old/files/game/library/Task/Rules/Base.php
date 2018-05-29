<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务规则基类
 * @author fanch
 *
 */
abstract class Task_Rules_Base extends Util_LogForClass {
	protected  $mTaskRecord = array();
	protected  $mRequest = array();
	
	public function __construct() {
	    parent::__construct("task.log", get_class($this));
	}
	/**
	 * 
	 * @param array $taskRecord 数据库记录
     * @param array $request 活动请求参数
	 */
	public function initial($taskRecord, $request){
		$this->mTaskRecord = $taskRecord;
		$this->mRequest = $request;
	}

	
	/**
	 * 计算赠送的物品
	 */
	abstract public function onCaculateGoods();
	
	/**
	 * 用户对象过滤
	 * @param array $task  任务配置
	 * @param string $uuid 用户uuid
	 * @return boolean
	 */
	protected function filterUserByUuid($task, $uuid) {
		if($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_ALL){
			return true;
		}
		else if ($task['hd_object'] == Client_Service_TaskHd::TARGET_USER_USER_BY_UUID) {
		    $additionInfo = json_decode($task['hd_object_addition_info'], true);
		    $userList = $additionInfo['user_list'];
		    if (is_array($userList) && in_array($uuid, $userList)) {
		        $this->debug(array('msg' => "用户[{$uuid}]在定向用户名单中",
		                              'user_list' => $userList));
		        return true;
		    }
		    
		    $this->debug(array('msg' => "用户[{$uuid}]不在定向用户名单中",
		                          'user_list' => $userList));
		    return false;
		}
		
		$this->err(array('msg' => "hd_object字段[{$task['hd_object']}]非期望值"));
		return false;
	}
	
	/**
	 * 老的计算规则
	 * @param array  $task     任务配置
	 * @param string $uuid     用户uuid
	 * @param string $desc     描述信息
	 */
	protected function caculateGoodsInternal($task, $uuid){
	    $ruleConfig = json_decode($task['rule_content'], true);
	    $acouponItem = $this->createAcouponItem(
	            $task, 
	            $uuid, 
	            $ruleConfig['denomination'], 
	            Client_Service_TaskHd::A_COUPON_EFFECT_START_DAY, 
	            $ruleConfig['deadline']);
	    
	    $sendGoods = array('Acoupon'=>array($acouponItem));
	    return $sendGoods;
	}

	/**
	 * 组装单条A券数据
	 * @param int $activityTask
	 * @param array $task
	 * @param string $sendMoney
	 * @param string $uuid
	 * @param int $deadLine
	 */
	protected function createAcouponItem($task, $uuid, $sendMoney, $startDay, $endDay, $densection = ''){
	    $acouponItem = array();
	    $time = Common::gettime();
	    $sectionTime = Common::getSectionTime($time, $startDay, $endDay);
	    $acouponItem = array(
	            'aid'=> date('YmdHis') . uniqid(),
	            'denomination' => $sendMoney,
	            'startTime' => $sectionTime['start_time'],
	            'endTime' => $sectionTime['end_time'],
	            'consume_time' => $time,
	            'desc' => $task['title'],
	            'uuid' => $uuid,
	            'send_type' => Client_Service_TicketTrade::GRANT_CAUSE_A_COUPON_ACTIVITY,
	            'sub_send_type' => $task['id'],
	            'task_name' => $task['title'],
	            'densection'=> $densection,
	    );
	    return $acouponItem;
	}
	
	/**
	 * 检测登陆的游戏是否在活动任务范围内
	 * @param array $task       任务配置
	 * @param string $apiKey    游戏标识
	 * @return boolean
	 */
	protected function canPartiActivity($task, $apiKey) {

        $gameInfo = $this->getGameInfoByApiKey($apiKey);
        if (!$gameInfo) {
            $this->warning(array('msg' => "无效的apiKey[{$apiKey}]"));
            return false;
        } else {
            $this->debug(array('msg' => "查游戏信息，apiKey[{$apiKey}],".
	                "游戏ID[{$gameInfo['id']}], 游戏名[{$gameInfo['name']}]"));
        }

        $gameObject = $task['game_object'];
        if (Client_Service_TaskHd::TARGET_GAME_GAME_ALL == $gameObject) {  //全部游戏
            return true;
        }

        $gameId = $gameInfo['id'];
	    
	    if (Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT == $gameObject) {  //专题游戏
	        return $this->isGameIdInGameTopic($gameId, $task['subject_id'] );
	    } elseif (Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST == $gameObject) {  // 定向游戏
	        return $this->isGameIdInGameList($gameId, $task['game_object_addition_info'], "定向");
	    } elseif (Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST == $gameObject) {  // 排除游戏
	        return !$this->isGameIdInGameList($gameId, $task['game_object_addition_info'], "排除");
	    }
	
	    return false;
	}
	

	/**
	 * 获取游戏信息
	 * @param string $apiKey 游戏ID
	 * @return array
	 */
	private function getGameInfoByApiKey($apiKey){
	    if ( !$apiKey ) { return false; }
	
	    $game_params['api_key'] = $apiKey;
        $game_params['status'] = Resource_Service_Games::STATE_ONLINE;
	    $gameInfo = Resource_Service_Games::getBy($game_params);
	    return $gameInfo;
	}
	
	/**
	 * 验证游戏是否在专题中
	 * @param string $apiKey    游戏表示
	 * @param string $subjectId 专题游戏
	 * @return boolean
	 */
	private function isGameIdInGameTopic($gameId, $subjectId){
	    $params['subject_id']  = $subjectId;
	    $params['game_status'] = Resource_Service_Games::STATE_ONLINE;
	    list(, $subjectGames) = Client_Service_Game::getSubjectBySubjectId($params);
	    $subjectGames = Common::resetKey($subjectGames, 'resource_game_id');
	    $resourceGameIds = array_unique(array_keys($subjectGames));
	
	    $this->debug(array('msg' => "判断游戏[{$gameId}]是否在专题[{$subjectId}]中",
	    'resource_game_ids' => $resourceGameIds));
	
	    if(in_array($gameId, $resourceGameIds)){
	       return true;
	    }
	    return false;
	}
	
	/**
	* 验证游戏是否在游戏列表中
	* @param string $apiKey    游戏表示
	* @param string $subjectId 专题游戏
	* @return boolean
	*/
	private function isGameIdInGameList($gameId, $additionInfo, $info="定向") {
	    $additionInfo = json_decode($additionInfo, true);
	    $gameList = $additionInfo['game_list'];
	    if (is_array($gameList) && in_array($gameId, $gameList)) {
	        $this->debug(array('msg' => "游戏[{$gameId}]在{$info}游戏名单中",
                                  'user_list' => $gameList));
	        return true;
	    }
	    
	    $this->debug(array('msg' => "游戏[{$gameId}]不在{$info}游戏名单中",
	            'user_list' => $gameList));
	    return false;
	}
	
	/**
	 * 根据金额计算赠送区间多区间适配
	 * @param array  $task          表game_client_task_hd的任务配置
	 * @param string $money         金额
	 */
	private function matchSection($ruleConfig, $money){
	    $section = array();
	    
	    /*
	     * 查看当前消费金额是否超过最大的配置区间的结束金额，
	    * 如果是直接取最后一个配置区间作为返还区间
	    */
	    $ruleMaxConfig = end($ruleConfig);
	    if($money >= $ruleMaxConfig['section_end']){
	        //返还区间取配置中最后一条区间的配置参数。
	        $section = $ruleMaxConfig;
	        return $section;
	    }
	     
	    $findFirstSection = 0;
	    foreach($ruleConfig as $item){
	        //比较当前结束区间
	        if ($money >= $item['section_end']) {
	            continue;
	        }
	        //比较当前开始区间
	        if($money >= $item['section_start']){
	            $findFirstSection = 1;
	        }
	        //已找到用于赠送的返还区间
	        if($findFirstSection == 1) {
	            $section = $item;
	            break;
	        }
	    }
	    return $section;
	}
	
	private function getValidMoney($section, $money) {
		if($money >= $section['section_end']) { // 按产品要求，最大值采用的规则是"<=",而非"<" 
			return $section['section_end'];
		}
		return  $money;
	}
	
	/**
	 * 计算返利
	 * @param array  $grantSection  
	 * @param string $money         
	 */
	private function calcReturnACoupon($grantSection, $money) {
	    if (empty($grantSection)) {
	        return 0;
	    }
	    return round($money * $grantSection['backPercent']) / 100;
	}
	
	/**
	 * 返利
	 * @param string $uuid
     * @param array  $task          表game_client_task_hd的任务配置
     * @param string $money         金额 
	 */
	protected function returnACoupon($uuid, $task, $money) {
	    $ruleConfig = json_decode($task['rule_content'], true);
	    $grantSection = $this->matchSection($ruleConfig, $money);
	    $money = $this->getValidMoney($grantSection, $money);
	    $sendACoupon = $this->calcReturnACoupon($grantSection, $money);
	    $this->debug(array('msg' => "计算返券金额, 返还金额[{$sendACoupon}], 任务ID[{$task['id']}], 金额[{$money}]"));
	    if ($sendACoupon <= 0) {
	        return array();
	    }
	    
	    //$densection描述数据
	    $jsonData  = json_encode(array(
	            'section_start' => $grantSection['section_start'],
	            'section_end' => $grantSection['section_end']
	    ));
	    
	    $sendGoods["Acoupon"][] = $this->createAcouponItem($task, $uuid, $sendACoupon, 
	            $grantSection['effect_start_time'], $grantSection['effect_end_time'], $jsonData);
	    return $sendGoods;
	}
	
	/**
	 * 累计返利
	 * @param string $uuid
	 * @param array  $task          表game_client_task_hd的任务配置
	 * @param string $oldSumMoney   之前的金额总值
	 * @param string $oldSumMoney   当前的金额总值
	 */
	protected function returnACouponForSum($uuid, $task, $oldSumMoney, $newSumMoney) {
	    $ruleConfig = json_decode($task['rule_content'], true);
	    
	    $oldSection = $this->matchSection($ruleConfig, $oldSumMoney);
	    $oldSumMoney = $this->getValidMoney($oldSection, $oldSumMoney);
	    $oldSumACoupon = $this->calcReturnACoupon($oldSection, $oldSumMoney);
	    
	    $newSection = $this->matchSection($ruleConfig, $newSumMoney);
	    $newSumMoney = $this->getValidMoney($newSection, $newSumMoney);
	    $newSumACoupon = $this->calcReturnACoupon($newSection, $newSumMoney);
	    
	    $sendACoupon = $newSumACoupon - $oldSumACoupon;
	    $this->debug(array('msg' => "计算返券金额, 返还金额[{$sendACoupon}], 任务ID[{$task['id']}],"
	            . " 总金额[{$newSumMoney}], 之前的总金额[$oldSumMoney], 总返还金额[{$newSumACoupon}]"));
	    
	    if ($sendACoupon <= 0) {
	        return array();
	    }
	    
	    // $densection描述数据
	    $jsonData  = json_encode(array(
	            'section_start' => $newSection['section_start'],
	            'section_end' => $newSection['section_end']
	    ));
	    
	    // NOTE: effect_start_time、effect_end_time在每段配置中都是相同的，配置时已做了约束
	    $sendGoods["Acoupon"][] = $this->createAcouponItem($task, $uuid, $sendACoupon,
	            $newSection['effect_start_time'], $newSection['effect_end_time'], $jsonData);
	    return $sendGoods;
	}
}
