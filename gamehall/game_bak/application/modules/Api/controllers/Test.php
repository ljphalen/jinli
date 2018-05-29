<?php
class TestController extends Api_BaseController {
	
	private $_version = array(
			1 => '1.5.4以上',
			2 => '1.5.4',
			3 => '1.5.5',
			4 => '1.5.6',
	);
	function getLCS($str1, $str2, $len1 = 0, $len2 = 0) {
		$this->str1 = $str1;
		$this->str2 = $str2;
		if ($len1 == 0) $len1 = strlen($str1);
		if ($len2 == 0) $len2 = strlen($str2);
		$this->initC($len1, $len2);
		return $this->printLCS($this->c, $len1 - 1, $len2 - 1);
	}
	
	/*返回两个串的相似度
	 */
	function getSimilar($str1, $str2) {
		$len1 = strlen($str1);
		$len2 = strlen($str2);
		$len = strlen($this->getLCS($str1, $str2, $len1, $len2));
		return $len * 2 / ($len1 + $len2);
	}
	function initC($len1, $len2) {
		for ($i = 0; $i < $len1; $i++) $this->c[$i][0] = 0;
		for ($j = 0; $j < $len2; $j++) $this->c[0][$j] = 0;
		for ($i = 1; $i < $len1; $i++) {
			for ($j = 1; $j < $len2; $j++) {
				if ($this->str1[$i] == $this->str2[$j]) {
					$this->c[$i][$j] = $this->c[$i - 1][$j - 1] + 1;
				} else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
					$this->c[$i][$j] = $this->c[$i - 1][$j];
				} else {
					$this->c[$i][$j] = $this->c[$i][$j - 1];
				}
			}
		}
	}
	function printLCS($c, $i, $j) {
		if ($i == 0 || $j == 0) {
			if ($this->str1[$i] == $this->str2[$j]) return $this->str2[$j];
			else return "";
		}
		if ($this->str1[$i] == $this->str2[$j]) {
			return $this->printLCS($this->c, $i - 1, $j - 1).$this->str2[$j];
		} else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
			return $this->printLCS($this->c, $i - 1, $j);
		} else {
			return $this->printLCS($this->c, $i, $j - 1);
		}
	}
	
	
	function input_csv($handle) {
		$out = array ();
		$n = 0;
		while ($data = fgetcsv($handle, 10000)) {
			$num = count($data);
			for ($i = 0; $i < $num; $i++) {
				$out[$n][$i] = $data[$i];
			}
			$n++;
		}
		return $out;
	}
	
	public function test2Action() {
		var_dump(Yaf_Session::getInstance());
		Yaf_Session::getInstance()->get('test');
	}
	
	public function msectime() {
		list($tmp1, $tmp2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
	}
	
	public function getClientIDAction() {
		
		$file = fopen("/home/ljp/www/motion/trunk/game/docs/games-user.csv","r");
		$data = $this->input_csv($file);
		fclose($file);
		
		header("Content-type:text/html;charset=utf-8");
		
		foreach ($data as $key=>$val){
			$clientID = Common::encryptClientData(trim($val[1]), trim($val[0]));		
			echo  'uname='.$val[0].',uuid='.$val[1].',clientID='.md5($clientID)."<br />";	
			
		}
		
		
	}
	function quarterFirstDay($day)
	{
		$_day = getdate(strtotime($day));
		$_thism = ceil($_day[mon]/3);
		return $_day[year].'-'.$_thism.'-1';
	}
	
	function randPrize($proArr) {
		$result = '';
		//概率数组的总概率精度
		$proSum = array_sum($proArr);
		//概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset ($proArr);
		return $result;
	}
	/**
	 * 登录初始化
	 */
	private function loginInitial($uuid, $clientVersion, $time, $imei, $uname){
		//1.5.4加入用户登录次数
		$cache = Cache_Factory::getCache();
		$cacheKey = $uuid.'_user_info' ; //获取用户的uuid
		//用户名
		$cache->hSet($cacheKey, 'uname', $uname); //用户名
		$cache->hSet($cacheKey, 'imei', $imei);//用户的imei
		$cache->hSet($cacheKey, 'clientVersion', $clientVersion);//客户端的版本
		if(strnatcmp($clientVersion, '1.5.5') < 0 ){
			$cache->hdel($cacheKey, 'continueLoginDay');
			return false;
		}
		//连续登录天数初始化
		$this->contineLoginDaysIntial($cache, $cacheKey, $time);
		//最后登录时间
		$cache->hSet($cacheKey, 'lastLoginTime', date('Y-m-d H:i:s',$time) );
	
	
	
	}
	
	
	private function contineLoginDaysIntial(&$cache, $cacheKey, $time){
		//连续登录的配置
		$taskParams['id'] = 1;
		$taskParams['status'] = 1;
		$taskConfig = Client_Service_ContinueLoginCofig::getBy($taskParams);
		if(!$taskConfig){
			return false;
		}
		//连续登录天数
		$continueLoginDay = $cache->hGet($cacheKey, 'continueLoginDay');
		if($continueLoginDay === false){
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
	
	}
	
	
	private function getContinueLoginDays(&$cache, $cacheKey, $time) {
		//最后的登录时间
		$lastLoginTime = $cache->hGet($cacheKey, 'lastLoginTime');
		$days = Common::diffDate($lastLoginTime,  date('Y-m-d H:i:s', $time));
		return $days;
	}
	
	
	private function  saveFestivalGameIdToCache($type, $uuid, $version, $gameId){
		$cache = $this->getCache();
		$key = $uuid.'_festival_'.$type ;
		$gameIDArr = $cache->get($key);
		if($gameIDArr){
			if(!in_array($gameId, $gameIDArr)){
				array_push($gameIDArr, $gameId);
				$cache->set($key, $gameIDArr, 864000);
			}
		}else{
			$cache->set($key, array($gameId), 864000);
		}
	}
	
	private function getCache(){
		$cache = Cache_Factory::getCache();
		//正式环境记得替换
		//$cache = Common::getCache();
		return $cache;
	
	}
	
	private function isCanSenduserProps($festivalId, $currentSendPropId) {
		
		$msg = array('进入方法'=>__METHOD__, '参数'=>func_get_args());
		Util_Log::info(__CLASS__, 'festival.log', $msg);
		
		$prizeParams[Festival_Service_Prizes::FIELD_FESTIVAL_ID] = $festivalId;
		$prizeInfo = Festival_Service_Prizes::getsBy($prizeParams);
		$pirzeData = array();
		foreach ($prizeInfo as $prize){
			$propIds = $prize[Festival_Service_Prizes::FIELD_CONDITION];
			$propIdArr = explode(',', $propIds);
			$total = 1 * $prize[Festival_Service_Prizes::FIELD_TOTAL];
			foreach ($propIdArr as $propId){
				$pirzeData[$propId] += $total; 
			}
		}
	
		var_dump($pirzeData);
		exit;
	
		$propParams[Festival_Service_PropsGrant::FIELD_FESTIVAL_ID] = $festivalId;
		$propResult = Festival_Service_PropsGrant::getsBy($propParams);
		$propData = array();
		foreach ($propResult as $val){
			$propData[$val[Festival_Service_PropsGrant::FIELD_PROP_ID]] += 1;
		}
	
		if(intval($propData[$currentSendPropId]) >= intval($pirzeData[$currentSendPropId])){
			return false;
		}
		return true;
	}
	
	public function getMyGiftIdKeyName($data) {
		if(!$data) {
			return ;
		}
		$api = Util_CacheKey::getApi(Util_CacheKey::GIFT, Util_CacheKey::MY_GIFT_ID);
		if(!is_array($api)){
			return ;
		}
		$keyName = $api[Util_CacheKey::CLASS_NAME] . '::' . $api[Util_CacheKey::METHOD_NAME];
		$keyName = $keyName.'_'.$data;
		return $keyName;
	}
	
	public function testAction() {
		$hashKey = "gamePackage";
		$packages=array('pagekage1','ttttr');
		$cache = Cache_Factory::getCache();
		
		$ret = $cache->hMget($hashKey, $packages);
		var_dump($ret);
		exit;
		$packages=array('pagekage1'=>11,
						'pagekage2'=>12,
						'pagekage3'=>13,
						'pagekage4'=>14,
						'pagekage5'=>15);
		
		$cache = Cache_Factory::getCache();
		$hashKey = "gamePackage";
		
		$ret = $cache->set(test,1111);
		exit;
		
		$packages=array('pagekage1','pagekage2','pagekage3','pagekage4','pagekage5');
		$cache = Cache_Factory::getCache();
		$hashKey = "gamePackage";
		$ret = $cache->hgetall($hashKey);
		var_dump($ret);
		exit;
		
		$keyName = self::getMyGiftIdKeyName('test');
		$cache = Cache_Factory::getCache();
		$giftIdData  = $cache->get($keyName);
		$giftIdData  = array();
		$result = in_array(11, $giftIdData);
		var_dump(!$result);
		exit;
		
		$str = 'com.boyaa.lordland.gionee_yz%3A1524%3A5.1%3A0ff5121ea3a05dffea6e815c44d0dc52%7Ccom.iflytek.inputmethod%3A1700%3A5.0.1700%3A074f30c3d470472326d14481ee4299e1%7Ccom.Qunar%3A85%3A7.4.5%3Abeb9d5e8420ac8b5eaead940c600e7de%7Ccom.wandoujia.phoenix2%3A8053%3A4.51.1%3Aab83e02bc9547fff06284062886c83ae%7Ccom.example.android.apis%3A0%3Anull%3Ae257a166c35b6fd70d5d14d0dec94ce3%7Ccom.wandoujia.phoenix2.usbproxy%3A6170%3A3.51.1%3Aaed8cbd556d8fdc7cd4aeb1fd978b7ac%7Ccom.achievo.vipshop%3A1278%3A8.2.10.2%3A5e075f2ff0cb4b0fac64c9d68f439927%7Ccom.qihoo.appstore%3A300030223%3A3.2.23%3A85d88be2b1363cc37dc3b18688775527%7Ccom.UCMobile%3A160%3A10.2.0%3A660beea0a9ce4b528b3537a2db0f55e2%7Ccom.example.azheng_test%3A1%3A1.0%3Afd971c6743d5574f9d90fb6a97ae1d29%7Ccom.assistant.icontrol%3A314%3A3.1.4%3A57dffdc1a4d32a9d6efa5b4dc4c764e5%7Ccom.gn.munion.apk%3A10005008%3A1.0.5.8%3A8abb7c76017335322bea34989a49bb7c%7Ccom.tencent.mobileqq%3A178%3A5.0.0%3A6be9b8cd165538b6eec8f992c7113593%7Ccom.boyaa.lordland.sina%3A41%3A5.1.2%3A4bd4aee9f068b35b20dd5945bc1eb180%7Ccom.gionee.gsp%3A30001008%3A3.0.1.i%3A85079c09f929ad9d9955b386e3dc1fa1%7Ccom.baidu.searchbox_gionee%3A16783887%3A5.0%3Ab66ee0803699ce5e16543bf66f41e07b%7Ccom.ceshi2.am%3A1%3A1.0.0.a%3A6160208f628506b33c07b0d240a04928%7Ccom.gionee.KingOf.am%3A30101003%3A3.1.1.c%3A1fa3f99cddf10c122a9709293460e76c%7Ccom.baidu.BaiduMap%3A517%3A7.0.0%3Ac025dbb108ad654fdb092ec5d472309e%7Ccom.tencent.qqpimsecure%3A1065%3A5.3.0%3A1924b8c73223219c488ceafe1d8d905a%7Ccom.tencent.mm%3A542%3A6.1.0.104_r1082914%3A8f51824f3c33b9412de5c224169c9bbd%7Ctv.peel.app%3A82008%3A8.2.5%3A944d1464974d4e38c7aa504e0d7d2b71%7Ccom.cnvcs.junqi%3A149%3A1.49%3A1fcef7fc5697bc6ff1a8a0e335c06549%7Ccom.sohu.inputmethod.sogou%3A411%3A7.1.1%3A3d5a5fa678037c1a4de87ce512f7bb95%7Ccom.tudou.android%3A41%3A4.4%3A5ff87d16a3e44e015e9e78cc1f9d83c2%7Ccom.ss.android.article.news%3A448%3A4.4.8%3A056b3e55c0e46da8fb2e56a8968f4cdf%7Cgn.com.android.gamehall%3A1020001803%3A1.5.7.c%3A04ac3e754d2ac8b9c5b518288c0680a6%7C5.0%7C720*1280';
		echo urldecode($str);
		exit;
		
		$packages=array('pagekage1'=>11, 
				        'pagekage2'=>12,
				        'pagekage3'=>13,
				        'pagekage4'=>14,
				        'pagekage5'=>15);		
		$cache = Cache_Factory::getCache();
		$hashKey = "gamePackage";
		$ret = $cache->hMset($hashKey, $packages, 1800);
		
		exit;

		$time = strtotime('-1 Day');
		var_dump(date('Y-m-d', $time));
		exit;
 		$time = Common::getTime();
		$imei = 'FD34645D0CF3A18C9FC4E2C49F11C510';
		$uname = '18344146709';
		$this->loginInitial('E0E28031DD9F4750B595285443B425F4', '1.5.6.b', $time, $imei, $uname);
		
		
		exit;  
		/*
		 $festivalId = 14;
		$this->isCanSenduserProps($festivalId, 53);
		
		exit;
		$this->saveFestivalGameIdToCache(1, 'E0E28031DD9F4750B595285443B425F4', '1.5.4', 11);
		
		*/
		/* header("Content-type:text/html;charset=utf-8");
		$proArr = array('一等奖'=>60,'二等奖'=>40);
		$result = $this->randPrize($proArr);
		
		echo $result;
		exit(); */
		
		/* $configArr = array('uuid'=>'526FE998932D49D8B44F64FD66CCC772',
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();
		exit;
		
		
		 $cache = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
		 
		 //$test = $cache->get('test');
		 //var_dump($test);
		 $test = $cache->set('test','ttttttttttttttttttt');
		//var_dump($test);
		$test = $cache->get('test');
		var_dump($test);
		 //$cache->flush();
		 
var_dump(apc_cache_info());
exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'game_id'=>69,
				'type'=>2,
				'task_id'=>2);
		$downloadObject = new Util_Activity_Context(new Util_Activity_Download($configArr));
		$downloadObject->sendTictket();
		
		exit; */
		
		
	/* 	require '/home/ljp/www/motion/trunk/swoole/libs/lib_config.php';
		
		$AppSvr = new Swoole\Protocol\AppServer();
		$AppSvr->loadSetting(__DIR__."/swoole.ini"); //加载配置文件
		$AppSvr->setAppPath(__DIR__.'/apps/'); //设置应用所在的目录
		$AppSvr->setLogger(new Swoole\Log\EchoLog(false)); //Logger
		
		$server = new \Swoole\Network\Server('0.0.0.0', 9501);
		$server->setProtocol($AppSvr);
		$server->daemonize(); //作为守护进程
		$server->run(array('worker_num' => 1, 'max_request' => 5000));
		exit; */
		/* $configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'apiKey'=>'A2AA4D67E6D942BD802C31C90FDD57B9',
				'logTime'=>Common::getTime());
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity ->sendTictket();
		exit;
 */
/* 		$eventName = 'user_consume';
		$request = array(
					'uuid' => 'test',
					'money' => 1,
					'api_key' => 'test'
		);
		Client_Service_TaskHd::runTask($eventName, $request);
		 */
		
	/* 
		// 日常任务初始化
		
	// 福利任务任务初始化

		$wealTaskId = array(1,2,3,4,5,6);
		foreach ($wealTaskId as $val){
			$wealTicketsResult = $wealTotalNumResult = 0;
			//总A券
			$wealTaskPointsParams['status'] = 1;
			$wealTaskPointsParams['send_type'] = 1;
			$wealTaskPointsParams['sub_send_type'] = $val;
			$wealTicketsResult = Client_Service_TicketTrade::getCount($wealTaskPointsParams);
			if($wealTicketsResult){
				Client_Service_TaskStatisticReport::updateStatisticReporTickets(1, $val, $wealTicketsResult);
			}
			//总人数
			$wealTotalNumResult = Client_Service_TicketTrade::getCount($wealTaskPointsParams);
			if($wealTotalNumResult){
				Client_Service_TaskStatisticReport::updateStatisticReporPeopleNum(1, $val, $wealTotalNumResult);
			}
		}
		exit;
		
		/* $dailyTaskParams['status'] = Client_Service_DailyTaskLog::FINISHED_STATUS;
	 	$time = Common::getTime();
		$startTime = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day',$time)));
		$endTime = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day',$time)));
		$dailyTaskParams['create_time'][0] = array('>=', $startTime);
		$dailyTaskParams['create_time'][1] = array('<=', $endTime); 
		$dailyResult = Client_Service_DailyTaskLog::getReportList($dailyTaskParams);
		
		var_dump($dailyResult);
	exit; 
		exit;*/ 
		//exit;
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='6B0E3BB36E474E0F9B0A5C0FC5CBA2B8';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '500',
						'desc' =>  '赠送',
						'startTime' =>  '20150615113321',
						'endTime' =>  '20150629235959',
						'uuid' =>  '70405C2BCF0A474296DD7890F0804563'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		exit;
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
						   'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
						   'apiKey'=>'A2AA4D67E6D942BD802C31C90FDD57B9',
						   'logTime'=>Common::getTime());
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity ->sendTictket();
		exit;
		
		
		$sp = 'E3_1.4.9.e_4.2.1_Android4.2.1.abs_720*1280_I01000_wifi_FD34645D0CF3A18C9FC4E2C49F11C510';
		$spArr = Common::parseSp($sp);
		$device  = $spArr['device'];
		$clientVersion = Common::getClientVersion($spArr['game_ver']);
		$systemVersion = Common::getSystemVersion($spArr['android_ver']);
		
		var_dump($clientVersion, $systemVersion);
		exit;
		//exit();
		/* $seasonTimeRange['endTime'] = '2015-04-28';
		
		//每个季度的最后一天
		$seasonEndDay = date('Y-m-d', strtotime($seasonTimeRange['endTime']));
		
		//过期提醒的前三天
		$messageRemindThirdDay = date('Y-m-d', strtotime('-3 day',  strtotime($seasonEndDay)));
		
		
		var_dump( $seasonEndDay, $messageRemindThirdDay);
		
		/* //福利任务消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_CONSUME_TASK_ID,
				'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33' );
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit; */
		
		
	
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '500',
						'desc' =>  '赠送',
						'startTime' =>  '20150420113321',
						'endTime' =>  '20150520235959',
						'uuid' =>  '70405C2BCF0A474296DD7890F0804563'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		
		//活动消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
							'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
							'money'=>10,
							'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33');
		$activity = new Util_Activity_Context(new Util_Activity_Payment($configArr));
		$activity->sendTictket();
		exit;
		
		header("Content-type:text/html;charset=utf-8");
		$season = ceil((date('n'))/3);//当月是第几季度
		echo '<br>本季度:<br>';
		echo $season.'<br />';
		echo date('Y-m-d H:i:s', mktime(0, 0, 0, $season*3-3+1,1,date('Y'))),"<br />";
		echo date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y'))),"<br />";
		exit;
		var_dump(getdate());
		echo $this->quarterFirstDay('2015-01-06');
		exit;
		$date = getdate();
		$month = $date['mon']; //当前第几个月
		$year = $date['year']; //但前的年份
		
		$strart = floor($month/3) * 3; //单季第一个月
		$strart = mktime(0,0,0,$strart,1,$year); //当季第一天的时间戳
		
		$end = mktime(0,0,0,$strart+3,1,$year); //当季最后一天的时间戳
		
		var_dump( date('Y-m-d H:i:s', $strart ), date('Y-m-d H:i:s', $end ));
		exit;
		
		//活动消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'money'=>10,
				'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33');
		$activity = new Util_Activity_Context(new Util_Activity_Payment($configArr));
		$activity->sendTictket();
		exit;
exit;
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '500',
						'desc' =>  '赠送',
						'startTime' =>  '20150405113321',
						'endTime' =>  '20150415235959',
						'uuid' =>  '12DAECC78BF3448C95F6261CD163ADD8'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		
			
		//增加迅搜索引
		$data = array(
				'gameId'=>'4565',
				'gameName'=>'蛋糕物语-删档测试',
				'resume'=>'甜蜜的治愈社交手游，经营你的幸福小铺！',
				'label'=>'蛋糕，甜点，蛋糕物语，蛋糕师，烘焙，社交，全民餐厅，梦幻蛋糕店，甜蜜，治愈'
		);
		//Api_XunSearch_Search::addIndex($data);
		//Api_XunSearch_Search::deleteIndex(4566);
		exit;
		
		$this->loginInitial('526FE998932D49D8B44F64FD66CCC772', '1.5.5', Common::getTime(), 'FD34645D0CF3A18C9FC4E2C49F11C510', '18344146709');
		$this->continueLoginSend('526FE998932D49D8B44F64FD66CCC772');
		exit;
		var_dump(strnatcmp('1.5.4', '1.5.5') < 0);
		$days = Common::diffDate('2015-04-01 23:05:55', '2015-04-02 02:24:01');
		
		
		var_dump($days) ;
		
	exit;
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '500',
						'desc' =>  '赠送',
						'startTime' =>  '20150401113321',
						'endTime' =>  '20150415235959',
						'uuid' =>  '95F8CF8BE72D4F93906181E8D9F67BF8'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		
		
		$items='[{"id":"146","htype":"3","title":"testtest","hd_start_time":"1427126400","hd_end_time":"1427299200","status":"1","hd_object":"1","condition_type":"2","condition_value":"","rule_type":"0","game_version":null,"game_object":"1","rule_content":"{\"denomination\":1,\"deadline\":1,\"restoration\":1}","rule_content_percent":null,"create_time":"1427249924","subject_id":"0"}]';
		$items = json_decode($items, true);
		
		var_dump($items, empty($items));		
		exit;
		
	
		$data = array(
				'gameId' => 44444444444444,
				'gameName' => 2,
				'resume' => 2,
				'label' =>2,
				'create_time' => time(),
		);
		$rs = Api_XunSearch_Search::deleteIndex($data);
		
		var_dump($rs);
		exit;
		
		
		$rsa = new Util_Rsa();
		$sign =  $rsa->encrypt('gionee2015', Common::getConfig("siteConfig", "rsaPemFile"));
		var_dump($sign);
		
		$sign = 'jaHwwYv5QOyYmJMb1U02rm8W9C1vRtIZ7V3CwVOnM3KQpkPEoJf6oTxpySaLt2kFWhkQc5iwJC89f884WsTWipwDqaB41ajJzI49BmNgjUHBxi7TCizdyBMLai+pnVuEBOUEporKn6QJwfIlfGTTimdKHQWHNvtIjwUDc87hP4U=';
		$str = $rsa->decrypt($sign, Common::getConfig("siteConfig", "rsaPubFile"));
		var_dump($str);
		exit;
		
		
		
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '100',
						'desc' =>  '赠送',
						'startTime' =>  '20150301113321',
						'endTime' =>  '20150331235959',
						'uuid' =>  '12DAECC78BF3448C95F6261CD163ADD8'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		
		$result = Api_XunSearch_Search::getSearchList(1, 10, '斗地主');
		
		var_dump($result);
		exit;
		//Client_Service_GiftHot::updateBy(array('game_status'=>0), array('game_id'=>244));
		exit;
		//echo bindec('1111 1111 1111 1111');
		//echo decbin(700000); 1010 1010 1110 0110 0000
		echo bindec(' 1111 1111 1111 1111');
		echo "<br>";
		echo decbin(4294967295);
		echo "<br>";
		echo strlen(decbin(4294967295));
		echo "<br>";
		
		echo bindec('111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111 1111');
		echo "<br>";
		echo decbin(9223372036854775807);
		echo "<br>";
		echo strlen(decbin(9223372036854775807));
		echo "<br>";
		exit;
		
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '200',
						'desc' =>  '赠送',
						'startTime' =>  '20150301113321',
						'endTime' =>  '20150331235959',
						'uuid' =>  '70405C2BCF0A474296DD7890F0804563'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		var_dump($rs_list);
		exit;
		
		header("Content-type:text/html;charset=utf-8");
		$payment_arr = Common::getConfig('paymentConfig','payment_send');
		
		var_dump(ENV);
		exit;
		
	$t1 = microtime(true);
     sleep(2);
$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒';

exit;
		echo microtime(true)*1000;
		exit;
		//活动消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'money'=>10,
				'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33');
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		
		var_dump(strnatcmp('1.5.4.a', '1.5.5'));
		exit;
		echo strtotime('- 1 day');
		
		

		$params = array('status' => 1, 'game_status'=>1);
		
		$params['effect_end_time'][0] = array('>=', strtotime('- 1 day'));
		$params['effect_end_time'][1] = array('<=', Common::getTime());
		$ret = Client_Service_Gift::getsBy($params);
		exit;
		
		$info = Client_Service_Gift::getGiftBaseInfo(27);
		var_dump($info);
		exit;
		//Client_Service_Gift::deleteGiftInfoCache(11);
		//exit();
		
		//$num = Client_Service_Gift::getGiftRemainNum(11);
		
		//var_dump($num);
		
		Client_Service_Gift::updateGiftTotalCache(11, 1);
		exit;
		 
		//$num = Client_Service_Gift::getGiftTotal(11);
		
		var_dump($num == 0);
		
		
		exit;
		
		
		
		header("Content-type:text/html;charset=utf-8");
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '500',
						'desc' =>  '赠送',
						'startTime' =>  '20150301113321',
						'endTime' =>  '20150310235959',
						'uuid' =>  'E0E28031DD9F4750B595285443B425F4'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		
		var_dump($rs_list);
		exit; 
		
		$total = 0;
		
		var_dump( $total > $result['max_win']);
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>6,
				'task_id'=>1,
				'section_start'=>1,
				'section_end'=>1,
				'denomination'=>1,
				'desc'=>'游戏大厅赠送111',
		);
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
		$activity->sendTictket();
		exit;
		
		
		$activityConfig = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				                'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				                'version'=>'1.5.6');
		$activity = new Util_Activity_Context(new Util_Activity_Login($activityConfig));
		$activity ->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>6,
				'task_id'=>1,
				'section_start'=>1,
				'section_end'=>1,
				'denomination'=>1,
				'desc'=>'游戏大厅赠送111',
		);
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'apiKey'=>'A2AA4D67E6D942BD802C31C90FDD57B9',
				'logTime'=>Common::getTime());
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity ->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();
		exit;
		
		
		//福利任务登录赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_LOING_TASK_ID);
		$activity = new Util_Activity_Context(new Util_Activity_Login($configArr));
		//$login ->sendTictket();
		//活动登录
		$activityConfig = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'version'=>'1.5.4');
		$activity = new Util_Activity_Context(new Util_Activity_Login($activityConfig));
		$activity ->sendTictket();
		exit;
		
		
		$uuid = Api_Gionee_Account::getUuidByName('18344146709');
		echo $uuid;
		exit;
		
	   /* $uname = '18344146709';
		$config = Api_Gionee_Account::getAccountConfig();
		$apiId = $config['AppId'];
		$appKey = $config['AppKey'];
		$account = array($uname);
		$result = Api_Gionee_Account::getAccountUuid($account, $apiId, $appKey);
		foreach ($result['data'] as $val){
			$uuid[$uname] = $val['+86'.$uname]['u'];
		} */
		$config = Api_Gionee_Account::getAccountConfig();
		//var_dump($config);
		$account = array('18344146709','183441467011','18344146703');
		$apiId = $config['AppId'];
		$appKey = $config['AppKey'];
	
		$result = Api_Gionee_Account::getAccountUuid($account, $apiId, $appKey);
		echo '<pre>';
		print_r($result);
		
		exit;
		
		$account = array('18344146709','18344146709','18344146709');
		$this->clientOutput($arr);
		exit;
		//活动消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'money'=>10,
				'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33');
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		 
		var_dump(Common::encryptClientData('E0E28031DD9F4750B595285443B425F4', '18344146709'));
		exit;
		
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>6,
				'task_id'=>1,
				'section_start'=>1,
				'section_end'=>1,
				'denomination'=>1,
				'desc'=>'游戏大厅赠送111',
		);
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
		$activity->sendTictket();
		exit;
	
	    $this->encryptClientData('E0E28031DD9F4750B595285443B425F4','18344146709');	
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>5,
				'task_id'=>0,
				'section_start'=>1,
				'section_end'=>1,
				'denomination'=>1,
				'desc'=>'游戏大厅赠送',
		);
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
		$activity->sendTictket();
		exit;
		
		//活动消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'money'=>10,
				'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33');
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		//福利任务消费赠送
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_CONSUME_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'game_id'=>69,
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_COMMENT_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_Comment($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
						   'type'=>5,
							'task_id'=>0,
							'section_start'=>1,
							'section_end'=>1,
							'denomination'=>1,
							'desc'=>'游戏大厅赠送',
				);
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
		$activity->sendTictket();
		exit;

		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TAST_UNIONLOGIN_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
							'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
							'apiKey'=>'A2AA4D67E6D942BD802C31C90FDD57B9',
							'logTime'=>Common::getTime());
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity ->sendTictket();
		exit;

		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'version'=>'1.5.3');
		$activity = new Util_Activity_Context(new Util_Activity_Login($configArr));
		$activity ->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				          'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_UNIONLOGIN_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity->sendTictket();
		exit;
		
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'content_type'=>Util_Activity_Context::CONTENT_TYPE_SHARE_GAME,
				'game_id'=>69,
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID);
		$activity = new Util_Activity_Context(new Util_Activity_Share($configArr));
		$activity ->sendTictket();
		exit;
		
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
						   'game_id'=>69,
						   'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
						    'task_id'=>Util_Activity_Context::DAILY_TASK_COMMENT_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_Comment($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_CONSUME_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
							'game_id'=>69,
							'type'=>2,
							'task_id'=>2);
		$downloadObject = new Util_Activity_Context(new Util_Activity_Download($configArr));
		$downloadObject->sendTictket();
		
		exit;
	

		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
							'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
							'task_id'=>Util_Activity_Context::WEAL_TAST_LOING_TASK_ID);
		$activity = new Util_Activity_Context(new Util_Activity_Login($configArr));
		$activity ->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TAST_UNIONLOGIN_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity->sendTictket();
		exit;
		
		
		
		$days = Common::diffDate('2015-01-30 11:04:53',  date('Y-m-d H:i:s', strtotime('2015-01-31 00:00:01')));
		
		echo $days;
		
		exit;
		
		header("Content-type:text/html;charset=utf-8");
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				           'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				           'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
                          );
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();
		exit;
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TAST_UNIONLOGIN_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_UnionLogin($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_CONSUME_TASK_ID,
				'api_key'=>1 );
		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
		$activity->sendTictket();
		exit;
		
		$configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',	
				           'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				           'task_id'=>Util_Activity_Context::WEAL_TAST_LOING_TASK_ID);
		$activity = new Util_Activity_Context(new Util_Activity_Login($configArr));
		$activity ->sendTictket();
		exit;
		
	    $configArr = array('uuid'=>'E0E28031DD9F4750B595285443B425F4',
						'content_type'=>Util_Activity_Context::CONTENT_TYPE_SHARE_GAME,
						'game_id'=>69,
						'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
						'task_id'=>Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID);
	    $activity = new Util_Activity_Context(new Util_Activity_Share($configArr));
		$activity ->sendTictket();
		exit;
		$ENCODE = Common::encrypt('E0E28031DD9F4750B595285443B425F4','ENCODE');
		
		$DECODE = Common::encrypt('4nnOgs97HfGwdM9OrzyhqxhBVCT3YQ1gTlaFdc3oK2tgW55pWNB68A', 'DECODE');
		var_dump($ENCODE, $DECODE);
		exit;
		$uuid = Util_Aes::encryptText('E0E28031DD9F4750B595285443B425F4');
		
		var_dump($uuid);
		//7CVqJi3LHNWVaL0LI1mgYPqouYjofILYHosGQyyzQ26USAP35bw9wf97Jri0zr8Q
		$decrypt = Util_Aes::decryptText('7CVqJi3LHNWVaL0LI1mgYPqouYjofILYHosGQyyzQ26USAP35bw9wf97Jri0zr8Q0');
		var_dump($decrypt);
		exit;
		//每日任务
		$down_class = new Util_Activity_Context(new Util_Activity_Comment(array('uuid'=>'E0E28031DD9F4750B595285443B425F4', 'task_id'=>3, 'game_id'=>68, 'type'=>2)));
		$down_class ->sendTictket();
		exit;
		$uuid='E0E28031DD9F4750B595285443B425F4';
		$cache = Common::getCache();
		$cacheKey = $uuid.'_user_info' ; //获取用户的uuid
		$last_login_time = $cache->hGet($cacheKey, 'lastLoginTime');
		$time = Common::getTime();
		if($last_login_time == false) {
			echo 'ddddddddddddd';
		}
		//$last_login_time ='2015-01-09 08:12:12';
		$days = Common::diffDate($last_login_time,  date('Y-m-d H:i:s', $time));
		var_dump($days,$last_login_time);
		exit;
		//每日任务
		$down_class = new Util_Activity_Context(new Util_Activity_Comment(array('uuid'=>'E0E28031DD9F4750B595285443B425F4', 'task_id'=>3, 'game_id'=>69, 'type'=>2)));
		$down_class ->sendTictket();
		exit;
		//每日任务
		$down_class = new Util_Activity_Context(new Util_Activity_Download(array('uuid'=>'E0E28031DD9F4750B595285443B425F4', 'task_id'=>2, 'game_id'=>69, 'type'=>2)));
		$down_class ->sendTictket();
		exit;
		
		$down_class = new Util_Activity_Context(new Util_Activity_Download(array('uuid'=>'E0E28031DD9F4750B595285443B425F4', 'task_id'=>2, 'game_id'=>69, 'type'=>1)));
		$down_class ->sendTictket();
		exit;
		/* 
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
				0 =>array(
						'aid' =>$aid ,
						'denomination' => '50',
						'desc' =>  '赠送',
						'startTime' =>  '20150101113321',
						'endTime' =>  '20150131235959',
						'uuid' =>  'E0E28031DD9F4750B595285443B425F4'
				),
		
		);
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		}
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);
		
		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		
		var_dump($rs_list); */
		
		
		Yaf_Session::getInstance()->set('test','this is test');
	//	$_SESSION['username'] = 'ljp';
		var_dump($_SESSION);
		
		
		

		$down_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1)));
		echo $down_class ->sendTictket();
		exit;
		
		 /*header("Content-type:text/html;charset=utf-8");
		 
		 
		 echo date('YmdHis', '2014-12-24 00:00');
		 exit;
		 
		 $fileName = '/home/ljp/www/motion/trunk/game/data/test.csv';
		 $csv = new Util_Excel_Csv();
		 $result = $csv->import($fileName);
		 
		 var_dump($result);
		 
		 //$excel = new Util_Excel_XmlExcel();
		 
		//$result =  $excel->import('/home/ljp/www/motion/trunk/game/data/label.csv');
		exit; 
		$file = fopen("/home/ljp/www/motion/trunk/game/data/test.csv","r");
		var_dump($this->input_csv($file));
		fclose($file);
		
		var_dump($result);
		 exit;*/
		/*//E0E28031DD9F4750B595285443B425F4
		$json_arr = array();
		$str ='{"msg":"\u8054\u8fd0\u6e38\u620f\u8d60\u9001","api_key":"D7DA8C82155246DCB015AE58DBD1620F","token":"c2d4a139bd4ab08311f9a114b7b16a6d","data":[{"aid":"201412231133215498e281bc3d5","denomination":"25","desc":"\u8054\u8fd0\u6e38\u620f\u8d60\u9001","startTime":"20141223113321","endTime":"20141223235959","uuid":"DAA4D6D7B9B642DEB8578590E604401C"},{"aid":"201412231133215498e281c00a5","denomination":"25","desc":"\u8054\u8fd0\u6e38\u620f\u8d60\u9001","startTime":"20141224000000","endTime":"20141227235959","uuid":"DAA4D6D7B9B642DEB8578590E604401C"}]}';
		$json_arr = json_decode($str,true);
		
		var_dump($json_arr);
		exit; */
		
	/*
		$api_key    ='D86DD031ECA04F49B65A9CFE2FD27F52';
		$url       = 'http://pay.gionee.com/voucher/apply';
		$ciphertext= 'goineegame20141201';
		$aid = date('YmdHis').uniqid();
		$prize_arr = array(
					0 =>array(
							'aid' =>$aid ,
							'denomination' => '50',
							'desc' =>  '赠送',
							'startTime' =>  '20141226113321', 
							'endTime' =>  '20141229235959',
							'uuid' =>  'E0E28031DD9F4750B595285443B425F4'
							),

		);	
		foreach ($prize_arr as $val){
			$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
		} 
		
		
		//加密的密文
		$data['api_key'] = $api_key;
		$data['msg'] = '赠送';
		$token = md5($ciphertext.$api_key.$data['msg'].$str);
		$data['token'] = $token;
		$data['data'] = $prize_arr;
		$json_data = json_encode($data);

		//post到支付服务器
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		
		var_dump($rs_list);
		*/
		exit;
		$down_class = new Util_Activity_Context(new Util_Activity_Consume(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1,'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33')));
		echo $down_class ->sendTictket();
		exit;
		
	
		if(Common::getTime() >= 1419350400  && Common::getTime() <= 1419436799){
			echo '12-24';
		}elseif (Common::getTime() >= 1419436800 && Common::getTime() <= 1419523199){
			echo '12-25';
		}elseif (Common::getTime() >= 1419523200 && Common::getTime() <= 1419609599){
			echo '12-26';
		}
		
		exit;
		$down_class = new Util_Activity_Context(new Util_Activity_Consume(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>3, 'money'=>1,'api_key'=>'C182B2152A414E8E8BA0CC8434AA2D33')));
		echo $down_class ->sendTictket();
		exit;
	
		$down_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1)));
		echo $down_class ->sendTictket();
		exit;
		
		//$down_class = new Util_Activity_Context(new Util_Activity_Download(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'task_id'=>3, 'game_id'=>120 )));
		//echo $down_class ->sendTictket();
		//exit;
		//$login_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C','type'=>1)));
		//$login_class ->sendTictket();
		//exit;
		//$login_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C','type'=>1, 'version'=>'1.5.4.a')));
		//$login_class ->sendTictket();
		//exit;
		
		$down_class = new Util_Activity_Context(new Util_Activity_Consume(array('uuid'=>'E30BAE6B8F9A4602899B93D0CA32ABFF', 'type'=>3, 'money'=>1)));
		echo $down_class ->sendTictket();
		exit;
		
		//$down_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1, 'api_key'=>'test')));
		//echo $down_class ->sendTictket();
		//exit;
		//$down_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>3,'logTime'=>Common::getTime())));
		//echo $down_class ->sendTictket();
		//exit;
		//com.skygame3.sh_jinli.am
		
		//$down_class = new Util_Activity_Context(new Util_Activity_Download(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'task_id'=>2, 'game_id'=>120 )));
		//$down_class ->sendTictket();
		
		//$login_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C','type'=>1)));
		//$login_class ->sendTictket();
		
		//$down_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1 )));
		//echo $down_class ->sendTictket();
		
		exit;
		$limit = 10;
		$params['LogTime'][0] = array('>=', date('Y-m-d H:i:s',Common::getTime() - 3 * 24 * 60 * 60));
		$params['LogTime'][1] = array('<=', date('Y-m-d H:i:s',Common::getTime()));
		$params['Type'] = 5;
		print_r($params);
		list(,$logs) = Client_Service_ACCLog::getList(1, $limit, $params); //每次获取前5分钟登陆日志做处理
		exit;
		
		
		//$down_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>1 )));
		//$down_class ->sendTictket();
		//exit;
		/*
		for ($i=0; $i< 3; $i++){
			$tmp[$i]['uuid'] = 1;
			$tmp[$i]['aid'] = 1;
		
		}	
		
		
		$rs = Client_Service_TicketTrade::mutiFieldInsert($tmp);
		
		
		var_dump($rs);
		
		exit;*/
		//登录赠送
		//$login_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C','type'=>1,)));
		//$login_class ->sendTictket();
		
		//$down_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>3, 'api_key'=>'', 'game_version'=>$this->_version, 'version'=>'1.5.4' )));
		//echo $down_class ->sendTictket();
		
		//$down_class = new Util_Activity_Context(new Util_Activity_UnionLogin(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C', 'type'=>3 )));
		//echo $down_class ->sendTictket();
		
		$down_class = new Util_Activity_Context(new Util_Activity_Consume(array('uuid'=>'0D12D785E17044C1A3965FCCDD1C8C3B', 'type'=>3, 'money'=>'25.6' )));
		echo $down_class ->sendTictket();
		
		exit;
		//$info = Client_Service_Subject::getSubject(intval(13));
		
		$params['subject_id'] = 13;
		
		list(, $subject_games) = Client_Service_Game::getSubjectBySubjectId($params);
		$subject_games = Common::resetKey($subject_games, 'resource_game_id');
		$resource_game_ids = array_unique(array_keys($subject_games));
		//$this->assign('info', $info);
		var_dump($resource_game_ids);
		exit;
		$cache = Common::getCache();
		$cacheKey = 'DAA4D6D7B9B642DEB8578590E604401C_user_info' ;
		
		echo $cache->hSet($cacheKey,'finishTaskid',json_encode(array(1)));
		
		echo 
		exit;
		//var_dump($start_time,$end_time);
		$startdate = 1417605108;
		$enddate   = 1417605571;
	echo (1417605571 - 1417605108);
		$days      = round(($enddate-$startdate)/3600/24);
		echo $days;
		
		
		exit;
		var_dump($this->_loginDate(4));
		
		$weal_task_prize = array(1=>array(
				                  'denomination'=>10,
				                  'deadline'=>2
				                  ),
								 2=>array(
									'denomination'=>10,
									'deadline'=>5
								 )
				
				);
		
		$prize_arr = array();
    	$time = Common::getTime();
    	$datetime = date('Y-m-d H:i:s',$time);
    	$str ='';
    	//取得福利任务的配置奖励
    	foreach ($weal_task_prize as $val){
    		if($val){
    			$end_time = strtotime( $datetime." + ".$val['deadline']." day" );
    			$aid = date('YmdHis').uniqid();
    			$desc = '福利任务-首次登录';
    			$prize_arr = array(
    							   'aid'=>$aid,
    							   'startTime'=>$time,
    							   'endTime'=>$end_time,
    							   'desc'=>$desc
    			);
    			$str.=$aid.$time.$end_time.$desc;
    		}
    	}
		var_dump($str);
		exit;
		$time = Common::getTime();
		$datetime = date('Y-m-d H:i:s',$time);
		var_dump($datetime);
		echo date('YmdHis')."<br>";
		//echo microtime();
		for($i=1; $i < 3 ; $i++){
			echo uniqid()."<br>";
		}
	
		var_dump(Common::getConfig('paymentConfig','payment_send'));
		
		echo strtotime(date('Y-m-d 00:00:01'))."<br>";
		echo strtotime(date('Y-m-d 23:59:59'))."<br>";
		
		
		//$cachekey = 'weal_task_num';
		//$cache = Common::getCache();
		//$cache->set($cachekey, 6);
		
		
		//var_dump($cache->get($cachekey));
		
		
		//echo date('Y-m-d H:i:s');
		/* $days = Common::diffDate('2014-11-30 06:50:50', '2014-11-30 07:50:50');
		
		var_dump($days);
		$time = Common::getTime();
		$cache = Common::getCache();
		$cacheKey = 'test' ; //获取用户的uuid
		//连续登录天数
		$continue_login_day = $cache->hGet($cacheKey,'continueLoginDay');
		$cache->hIncrBy($cacheKey,'continueLoginDay',1);
		$cache->hSet($cacheKey,'lastTime',date('Y-m-d H:i:s',$time));
		$cache->hIncrBy($cacheKey,'continueLoginTotal',1); */
		
		//var_dump($continue_login_day);
		//echo date('Y-m-d H:i:s');
		//$days = Common::diffDate(date(), date());
		
		//echo $days; //days为得到的天数;

		// $ret = Common::getCache()-> select(1);
		
		
		//$cache = Common::getCache()->hGetAll('222');
		//var_dump($ret);
		$login_class = new Util_Activity_Context(new Util_Activity_Login(array('uuid'=>'DAA4D6D7B9B642DEB8578590E604401C')));
		$login_class ->sendTictket();		
		//$activity->setStrategy(new Util_Activity_Download($config = array('total'=>20) ));
		//$activity->sendTictket();
		
		exit();
		
	}
	
	/**
	 * 算出用户的登录日期显示
	 */
	
	private function _loginDate($continue_login_day){
	
		$date_arr = Common::getConfig('paymentConfig','festival_config');
		$tmp = array();
	
		$z = 0;
		for ($i=0; $i < $continue_login_day-1; $i++){
			$z--;
			$tmp[$i] = date('m-d',strtotime(''.$z.' day'));
		}
	var_dump($tmp);
	exit;
		$j = 0;
		for($i = 7 - $continue_login_day; $i < 7; $i++ ){
			$tmp[$i] = date('m.d',strtotime('+ '.$j.' day'));
			$j++;
		}
		sort($tmp) ;
		return $tmp ;
	}
	
	public function t11Action(){
		$result = Account_Service_User::checkOnline('E1099068D82F47C58BFB2F340485C664','E75ACADBA956E3705D8813CFD8189E1C','uuid');
		var_dump($result);
	}
	
	public function gameAction(){
		header("Content-type: text/html; charset=utf-8");
		$gameId =$this->getInput('id');
		$gameData = Resource_Service_GameData::getGameAllInfo($gameId);
		if(!$gameData) exit("not found game");
		echo "游戏缓存数据<br/>";
		echo "<pre/>";
		print_r($gameData);
		echo "游戏重点关注字段数据<br/>";
		echo "name-游戏名称<br/>";
		echo "resume-游戏简述<br/>";
		echo "img-游戏icon<br/>";
		echo "package-游戏包名<br/>";
		echo "descrip-游戏说明<br/>";
		echo "tgcontent-小编八卦<br/>";
		echo "online_time-上线时间<br/>";
		echo "link-游戏上线版本下载地址<br/>";
		echo "version_code-游戏上线版本versionCode<br/>";
	}
	
	/**
	 * 登录赠送接口
	 * @return boolean
	 */
	public function loginSendAction(){
	
		$info = $this->getInput(array('puuid', 'uname', 'sp', 'clientId'));
	
		$data['success'] = 'true';
		$data['msg'] = '';
		$data['sign'] = 'GioneeGameHall';
		$data['returnState'] = 'Fail';
		if(!$info['puuid'] || !$info['uname'] || !$info['clientId'] || !$info['sp']){
			$this->clientOutput($data);
		}
	
		$clientVersion = Common::parseSp($info['sp'], 'game_ver');
		//验证客户上报的数据
		$rs = Common::verifyClientEncryptData($info['puuid'], $info['uname'], $info['clientId']);
		if(!$rs){
			$this->clientOutput($data);
		}
		/* 	if($info['puuid']){
		 $cache = Common::getCache();
		$cacheHash = $info['puuid'].'_user_info' ;
		$loginClientVertion = $cache->hGet($cacheHash, 'clientVersion');
		if(!strnatcmp($loginClientVertion, '1.5.5') >= 0 ){
		$this->clientOutput($data);
		}
		} */
	
		//福利任务登录赠送
		$configArr = array('uuid'=>$info['puuid'],
				'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				'task_id'=>Util_Activity_Context::WEAL_TASK_LOING_TASK_ID);
		$login = new Util_Activity_Context(new Util_Activity_Login($configArr));
		$login ->sendTictket();
		//活动登录
		$activityConfig = array('uuid'=>$info['puuid'],
				'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
				'version'=>$clientVersion);
		$activity = new Util_Activity_Context(new Util_Activity_Login($activityConfig));
		$activity ->sendTictket();
	
		//连续登录赠送
		$this->continueLoginSend($info['puuid']);
	
		$data['returnState'] = 'Success';
		$this->clientOutput($data);
	
	}
	private function continueLoginSend($uuid){
		$configArr = array('uuid'=> $uuid,
				'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
				'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
		);
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();
	}
	
	
	public function htmlAction(){
		header("Content-type: text/html; charset=utf-8");
		$html=<<<str
		<head><meta charset="utf-8"/></head>
		<div id="page">
		<div class="article">
		  <div class="content intro ui-editor">
			 <p style="text-indent:2em;">
				<span style="line-height:2;">抽不到奖品？没关系，这次我们概率翻倍！</span> 
			 </p>
			 <p style="text-indent:2em;">
				<span style="line-height:2;">金立最新最酷最潮手机S5.5，200张移动充值卡随你挑！</span>
		     </p>
			 <p style="text-indent:2em;"><span style="line-height:2;">……</span> </p>
			 <p style="text-indent:2em;">
				<span style="line-height:2;text-indent:2em;">我们疯了吗？…….的确，已然疯魔了！</span> 
			 </p>
			 <p>
				<span style="line-height:1.5;"><img src="http://assets.gionee.com/attachs/game//hd/201405/537c13368f8ff.jpg" alt="" /><br /></span> 
			 </p>
			 <p style="text-indent:2em;">
				<span style="line-height:24px;">
				<strong><a href="http://baidu.com"/>如何关注金立</a></strong>
				<span style="line-height:24px;text-indent:24px;white-space:normal;">
				<strong>游戏大厅官方微信</strong>
				</span>
				<strong>？</strong>
				</span>
			 </p>
			 <p style="text-indent:2em;">
				<span style="line-height:24px;">1.
				<span style="white-space:nowrap;">打开微信-朋友们-添加朋友-查找微信工作账号，输入“金立游戏大厅”，点击关注即可成功关注金立游戏大厅。</span>
				</span>
			 </p>
			 <p style="text-indent:2em;">
				<span style="line-height:2;">2.扫描二维码关注金立游戏大厅官方微信，参与我们的活动中来！</span> 
			 </p>
			 <p style="text-indent:2em;">
				<span style="line-height:2;">3.将二维码保存到手机，使用扫描软件（如微信的“扫一扫”），从相册选择二维码，即可关注金立游戏大厅官方微信。</span> 
			 </p>
			 <p><img src="http://assets.gionee.com/attachs/game//hd/201405/537c12f53fe94.jpg" alt="" /></p>
			 
		  </div>
		</div>
	</div>
str;
		echo base64_encode($html);
		die;
	}
	
  public function Test3Action(){	
  	
  	 header("Content-type:text/html;charset=utf-8");
  	$api_key    ='6B0E3BB36E474E0F9B0A5C0FC5CBA2B8';
  	$url       = 'http://pay.gionee.com/voucher/apply';
  	$ciphertext= 'goineegame20141201';
  	$aid = date('YmdHis').uniqid();
  	$prize_arr = array(
  			0 =>array(
  					'aid' =>$aid ,
  					'denomination' => '500',
  					'desc' =>  '赠送',
  					'startTime' =>  '20150615113321',
  					'endTime' =>  '20150629235959',
  					'uuid' =>  '70405C2BCF0A474296DD7890F0804563'
  			),
  	
  	);
  	foreach ($prize_arr as $val){
  		$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
  	}
  	
  	
  	//加密的密文
  	$data['api_key'] = $api_key;
  	$data['msg'] = '赠送';
  	$token = md5($ciphertext.$api_key.$data['msg'].$str);
  	$data['token'] = $token;
  	$data['data'] = $prize_arr;
  	$json_data = json_encode($data);
  	
  	//post到支付服务器
  	$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
  	$rs_list = json_decode($result->data,true);
  	var_dump($rs_list);
  	exit; 
    	$payment_arr = Common::getConfig('paymentConfig','payment_send');
    	$api_key    = $payment_arr['api_key'];
    	$url       = $payment_arr['url'];
    	$ciphertext= $payment_arr['ciphertext'];
    	
    	$aid = date('YmdHis').uniqid();
    	$prize_arr = array(
    			array(
    					'aid' =>$aid ,
    					'denomination' => '500',
    					'desc' =>  '赠送',
    					'startTime' =>  date('YmdHis'),
    					'endTime' =>  date('Ymd235959'),
    					'uuid' =>  $this->getInput('uuid')
    			),
    	);
    	foreach ($prize_arr as $val){
    		$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
    	}    
    	//加密的密文
    	$data['api_key'] = $api_key;
    	$data['msg'] = '测试';
    	$token = md5($ciphertext.$api_key.$data['msg'].$str);
    	$data['token'] = $token;
    	$data['data'] = $prize_arr;
    	$json_data = json_encode($data);
    
    	//post到支付服务器
    	$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
    	$rs_list = json_decode($result->data,true);
    	var_dump($rs_list);
    	exit;
    }
    public function sendTickeT1Action(){
    
    	$payment_arr = Common::getConfig('paymentConfig','payment_send');
    	$api_key    = $payment_arr['api_key'];
    	$url       = $payment_arr['url'];
    	$ciphertext= $payment_arr['ciphertext'];
    
    	$aid = date('YmdHis').uniqid();
    	$prize_arr = array(
    			array(
    					'aid' =>$aid ,
    					'denomination' => '500',
    					'desc' =>  '赠送',
    					'startTime' =>  date('YmdHis'),
    					'endTime' =>  date('Ymd235959'),
    					'uuid' =>  $this->getInput('uuid')
    			),
    	);
    	foreach ($prize_arr as $val){
    		$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
    	}
    
    	//加密的密文
    	$data['api_key'] = $api_key;
    	$data['msg'] = '测试';
    	$token = md5($ciphertext.$api_key.$data['msg'].$str);
    	$data['token'] = $token;
    	$data['data'] = $prize_arr;
    	$json_data = json_encode($data);
    
    	//post到支付服务器
    	$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
    	$rs_list = json_decode($result->data,true);
    	var_dump($rs_list);
    	exit;
    }
}