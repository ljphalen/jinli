<?php 
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lichanghua
 *
 */
class TaskController extends Client_BaseController{
	
public $perpage = 10;


    /**
     * 福利任务说明
     */
	public function introAction() {
	
       $game_task_desc = Game_Service_Config::getValue('game_task_desc');
	
	   $this->assign('game_task_desc', $game_task_desc);
	}
	
	/**
	 * 我的A券说明页面
	 */
	
	public function aintroAction(){
		
		$game_acoupon_desc = Game_Service_Config::getValue('game_acoupon_desc');
		
		$this->assign('game_acoupon_desc', $game_acoupon_desc);
	}
	
	/**
	 * 我的A券的列表
	 */
	public function myticketAction() {
		$info = $this->getInput(array('page','puuid','sp'));
		$page = intval($info['page']) < 1?1:$info['page'];
		$uuid = $info['puuid'];
		if(empty($uuid)) $this->output('-1','UUID Params Value  Error!');
		
		$sp = Common::parseSp($info['sp']);	
		$online = Account_Service_User::checkOnline($uuid, $sp['imei'], 'uuid');
		//if($online){
			//取得组装数据
			$result = $this->ticketList($page, $uuid);
		    ///加密的密文
			$payment_arr = Common::getConfig('paymentConfig','payment_send');
			$ciphertext = $payment_arr['ciphertext'];
			$tmp['api_key'] = $payment_arr['api_key'];
			$tmp['uuid']    = $uuid;
			$tmp['token']   =  md5($ciphertext.$tmp['api_key'].$uuid);
		
			//post到支付服务器
			//A券的余额
			$json_data = json_encode($tmp);
			$rs = Util_Http::post($payment_arr['coin_url'], $json_data, array('Content-Type' => 'application/json'), 2);
			$result['ATick'] = 0;
			if( $rs->data){
				$rs_list = json_decode($rs->data,true);
				//A券
				if($rs_list['voucher'] > 0){
					$result['ATick'] = number_format($rs_list['voucher'], 2,'.', '');
				}
		   } 
	   //}	
        $this->assign('clientVersion', $sp['game_ver']);
		$this->assign('online', $online);
		$this->assign('uuid', $uuid);
		$this->assign('result', $result);
	}
	
	/**
	 * 我的A券列表
	 */
	public function getTicketListAction(){
		$info = $this->getInput(array('page','uuid'));
		$page = intval($info['page']) < 1?1:$info['page'];
		$uuid = $info['uuid'];
		if(empty($uuid)) $this->output('-1','UUID Params Value  Error!');
		$result = $this->ticketList($page, $uuid);
		$this->output(0,'',$result);
		
	}
	

	/**
	 * 连续登录说明页面
	 *
	 */
	public function continueLoginAction(){
		$info = $this->getInput(array('puuid','sp'));
		$uuid = $info['puuid'];
		if(empty($uuid)) $this->output('-1','UUID Params Value  Error!');
		if(empty($info['sp'])) $this->output('-1','sp Params Value  Error!');
	
		$sp = Common::parseSp($info['sp']);
		$online = Account_Service_User::checkOnline($uuid, $sp['imei'], 'uuid');
		
		//连续登录的配置
		$taskParams['id'] = 1;
		$taskParams['status'] = 1;
		$taskConfig = Client_Service_ContinueLoginCofig::getBy($taskParams);
		//基本奖励配置
		$basePrizeConfig = json_decode($taskConfig['award_json'], true);
		
		//连续登录的天数
		$cache = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($uuid) ; //获取用户的uuid
		$continueLoginDay = intval($cache->hGet($cacheKey,'continueLoginDay'));
		if(!$online){
			$continueLoginDay = 1;
		} 
		$data['data']['awardIndex']= $continueLoginDay;
		$result = $this->getContinueLoginDayItem($basePrizeConfig, $online, $continueLoginDay);
		
		//var_dump($result);
		//当次获得奖励
		list($currentPoint, $currentTicket) = $this->getContinueCurrentPrizeValue($basePrizeConfig, $online, $continueLoginDay);
	
		//获取已经获取的奖励总额																		 
		list($totalPoint, $totalTicket) = $this->getHadContinueLoginTotal($uuid, $continueLoginDay);
   
		//是否有活动
		$activityParams['status'] = 1;
		$activityParams['start_time'] = array('<=', Common::getTime());
		$activityParams['end_time']   = array('>=', Common::getTime());
		$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
		if($activityConfig){
			//节日活动中的图片
			$attachRoot = Common::getAttachPath();
			$imgUrl= $activityConfig['img']?$attachRoot.$activityConfig['img']:'';
		}
		//文字描述
		$desc = Game_Service_Config::getValue('game_weal_desc');
	
		$this->assign('continueLoginDay', $continueLoginDay);
		$this->assign('currentTicket', $currentTicket);
		$this->assign('currentPoint', $currentPoint);
		$this->assign('totalTicket', $totalTicket);
		$this->assign('totalPoint', $totalPoint);
		$this->assign('desc', $desc);
		$this->assign('imgUrl', $imgUrl);
		
		$this->assign('online', $online);
		$this->assign('uuid', $uuid);
		$this->assign('result', $result);

	}
	
	/**
	 * 获取当次奖励的的积分与A券
	 */
	private function getContinueCurrentPrizeValue($basePrizeConfig, $online, $continueLoinDay){
		//取得用户的七天登录周期
		$loginDateTime =  Common::loginDate($continueLoinDay);
		$basePrize = $basePrizeConfig[$continueLoinDay-1];
		$activityParams['status'] = 1;
		$activityParams['start_time'] = array('<=', strtotime($loginDateTime[$continueLoinDay-1]));
		$activityParams['end_time']   = array('>=', strtotime($loginDateTime[$continueLoinDay-1]));
		$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
		$currentTicket = 0;
		$currentPoint = 0;
		if($activityConfig){
			//award_type=1 加，2乘
			if($activityConfig['award_type'] == 1){
				//send_object=1 积分 2 A券
				if($basePrize['send_object'] == 1){
					$currentPoint = $basePrize['denomination'] + $activityConfig['award'];
				}else{
					$currentTicket = $basePrize['denomination'] + $activityConfig['award'];
				}
			}else{
				//send_object=1 积分 2 A券
				if($basePrize['send_object'] == 1){
					$currentPoint = $basePrize['denomination']*$activityConfig['award'];
				}else{
					$currentTicket = $basePrize['denomination']*$activityConfig['award'];
				}
			}
	
		}else{
			//send_object=1 积分 2 A券
			if($basePrize['send_object'] == 1){
				$currentPoint = $basePrize['denomination'];
			}else{
				$currentTicket = $basePrize['denomination'];
			}
		}
		return array($currentPoint, $currentTicket);
	}
	
	/**
	 * 已经获取的连续登录奖励值得
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
	 * 
	 * @param unknown_type $basePrizeConfig
	 * @param unknown_type $online
	 * @param unknown_type $continueLoinDay
	 * @return multitype:number string multitype:number string unknown
	 */
	private function getContinueLoginDayItem($basePrizeConfig, $online, $continueLoginDay){
		//取得用户的七天登录周期
		if($online){
			$loginDateTime = Common::loginDate($continueLoginDay);
		}else{
			$loginDateTime = Common::loginDate(1);
		}
	
		//取得配置日期的节目名称
		$festival_arr = Common::getConfig('paymentConfig','festival_config');
		$tmp = array();
		foreach ($basePrizeConfig as $key=>$val){
			if($val['send_object'] == 1){
				$str ='积分';
			}else{
				$str ='A券';
			}
			$name = date('n.j', strtotime($loginDateTime[$key]));
			$activityParams['status'] = 1;
			$activityParams['start_time'] = array('<=', strtotime($loginDateTime[$key]));
			$activityParams['end_time']   = array('>=', strtotime($loginDateTime[$key]));
			$activityConfig = Client_Service_ContinueLoginActivityConfig::getBy($activityParams);
			if($activityConfig){	
				$data[] =  array('festival' =>1,
						'value'    =>$val['denomination'].$str,
						'name'     =>array_key_exists($name, $festival_arr)?$festival_arr[$name]:$name,
						'type'    =>intval($activityConfig['award_type']),
						'number'  =>intval($activityConfig['award']),
						'isDone'  =>($continueLoginDay >= ($key+1))?1:0
							
				);
			}else{
				$data[] =  array('festival' =>0,
						'value'    =>$val['denomination'].$str,
						'name'     =>$name,
						'type'    =>0,
						'number'  =>0,
						'isDone'  =>($continueLoginDay >= ($key+1))?1:0
				);
			}
		}
		return $data;
	}
	
	/**
	 * 连续登录说明页面
	 * 
	 */
	public function continueLoginIntroAction(){

		$desc = Game_Service_Config::getValue('game_weal_desc');
		$this->assign('game_weal_desc', $desc);
		
	}
	
	

	/**
	 *每日任务说明
	 *
	 */
	public function dailyTaskIntroAction(){
		$gameDailyTaskDesc = Game_Service_Config::getValue('gameDailyTaskDesc');
		
		$this->assign('gameDailyTaskDesc', $gameDailyTaskDesc);
	
	}
	
	/**
	 *积分说明
	 *
	 */
	public function pointsIntroAction(){
		$gamePointsDesc = Game_Service_Config::getValue('gamePointsDesc');
		
		$this->assign('gamePointsDesc', $gamePointsDesc);
	
	}
	
	
	/**
	 * A券列表组装
	 * @param unknown_type $page
	 * @param unknown_type $uuid
	 * @return Ambigous <multitype:, string>
	 */
	
	private function ticketList($page, $uuid){
		
		list($total,$dataList) = Client_Service_TicketTrade::getList($page,$this->perpage,array('status'=>1,'uuid'=>$uuid),array('end_time'=>'DESC'));
		$params = $temp =  array();
		$str = '';
		foreach ($dataList as $v){
			$temp[]['ano'] = $v['out_order_id'];
			$str .=$v['out_order_id'];
		}	
		
		//取得配置
		$payment_arr = Common::getConfig('paymentConfig','payment_send');
		$pri_key = $payment_arr['ciphertext'];
		$api_key    = $payment_arr['api_key'];
		$url       = $payment_arr['ticket_list_url'];
		$params['api_key'] = $api_key;
		$params['token'] = md5($pri_key.$api_key.$str);
		$params['data'] = $temp;
			
		//post到支付服务器
		$response = Util_Http::post($url, json_encode($params), array('Content-Type' => 'application/json'), 2);
		$rs_list = json_decode($response->data,true);
		
		$list = array();
		// A券的余额
		foreach ($rs_list['data'] as $key=>$val){
			$list[$val['ano']] = $val['balance'];
		}
		
		$out= array();
		foreach($dataList as $m=>$n){
			$out[$m]['chargeMount'] =$n['denomination'];
			$out[$m]['status'] = ($n['start_time'] >= time() )?'no':( $n['end_time']<time()?'outdate':'available');
			$out[$m]['leftMount'] = $list[$n['out_order_id']]?$list[$n['out_order_id']]:'0';
			$out[$m]['origin'] =$n['description'];
			$out[$m]['startDate']= date('Y-m-d',$n['start_time']);
			$out[$m]['endDate'] = date('Y-m-d',$n['end_time']);
		}		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$result['hasNext'] = $hasnext ;
		$result['curPage'] = intval($page) ;
		$result['list'] = $out;
		return  $result;
	}
	
	
	


}



