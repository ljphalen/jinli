<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 具体策略类 评论
*/
class Util_Activity_Common{
	private $mPath =null;
	private $mFileName =null;
	/**
	 *
	 * @param unknown_type $config
	 */
	public function __construct($path, $fileName){
		$this->mPath = $path;
		$this->mFileName = $fileName;
	}
	
	
	 
    /**
     * 保存赠送消息到消息队列中
     */
    public  function saveMsg($uuid, $denomination, $desc){
    	if(!$uuid || empty($desc)) return false;
    	$switch = Game_Service_Msg::checkAddMsg(103);
    	if(!$switch) return false;
    	$time = Common::getTime();
    	$message = array(
    			'type' =>  103,
    			'top_type' =>  100,
    			'totype' =>  1,
    			'title' =>  '您获得'.$denomination.'A券',
    			'msg' =>  $desc,
    			'status' =>  0,
    			'start_time' =>  $time,
    			'end_time' =>  strtotime('2050-01-01 23:59:59'),
    			'create_time' =>  $time,
    			'sendInput' =>  $uuid,
    	);
    	return Common::getQueue()->push('game_client_msg',$message);
    }
    
    /**
     * 获得配置信息
     * @return multitype:unknown
     */
    public  function  getPaymentConfig(){
    	$payment_arr = Common::getConfig('paymentConfig','payment_send');
    	$api_key    = $payment_arr['api_key'];
    	$url       = $payment_arr['url'];
    	$ciphertext= $payment_arr['ciphertext'];
    	return array($api_key,$url,$ciphertext);
    }
    
    
    /**
     * 组装奖励配置数组
     * @param unknown_type $awardArr
     * @return boolean
     */
     public  function getAwardResult($awardArr){
    	if(!is_array($awardArr)){
    		return false;
    	}
    	
    	//取得福利任务的配置奖励
    	$time = Common::gettime();
    	foreach ($awardArr as $val){
    		if($val['denomination']){
    			$arr = Common::getSectionTime($time, $val['section_start'] , $val['section_end']);
    			$start_time = $arr['start_time'];
    			$end_time = $arr['end_time'];
    			$aid = date('YmdHis').uniqid();
    			if(isset($val['sub_send_type']) && isset($val['task_name']) ){
    				$prizeArr[] = array(
    						'aid'=>$aid,
    						'denomination'=>(string)$val['denomination'],
    						'startTime'=>(string)date('YmdHis',$start_time),
    						'endTime'=>(string)date('YmdHis',$end_time),
    						'desc'=>$val['desc'],
    						'uuid'=>$val['uuid'],
    						'sub_send_type'=>$val['sub_send_type'],
    						'task_name'=>$val['task_name'],
    						'densection'=>$val['densection']
    				);
    			}else{
    				$prizeArr[] = array(
    						'aid'=>$aid,
    						'denomination'=>(string)$val['denomination'],
    						'startTime'=>(string)date('YmdHis',$start_time),
    						'endTime'=>(string)date('YmdHis',$end_time),
    						'desc'=>$val['desc'],
    						'uuid'=>$val['uuid']
    				);
    			}
    		
    		}
    	}
    	return $prizeArr;
    }
    
   /**
    * 组装post到支付的数据
    * @param unknown_type $awardArr
    * @return boolean|multitype:unknown
    */
    public function postToPaymentData($awardArr){
    	if(!is_array($awardArr)){
    		return false;
    	}
    	//取得福利任务的配置奖励
    	$time = Common::gettime();
    	foreach ($awardArr as $val){
	    	$prizeArr[] = array(
	    			'aid'=>$val['aid'],
	    			'denomination'=>$val['denomination'],
	    			'startTime'=>$val['startTime'],
	    			'endTime'=>$val['endTime'],
	    			'desc'=>$val['desc'],
	    			'uuid'=>$val['uuid']
	    	);
    	}
    	return $prizeArr;
    }
    
    
    /**
     * post请求到支付
     */
      public  function postToPayment($prizeArr){	
      	$desc='游戏大厅赠送';
    	//取得配置信息
    	list($api_key,$url,$ciphertext) = $this->getPaymentConfig();
    	$joinStr = $this->getJoinStr($prizeArr);
    	//加密的token
    	$token = md5($ciphertext.$api_key.$desc.$joinStr);
    	//加密的密文
    	$data['api_key'] = $api_key;
    	$data['msg'] = $desc;
    	$data['token'] = $token;
    	$data['data'] = $prizeArr;
    	$json_data = json_encode($data);
    	//写日志
    	$logData= '进入父类postToPayment方法，PSOT请求到支付组服务器url='.$url.',json_data='.$json_data;
    	Common::WriteLogFile($this->mPath, $this->file_name, $logData);
    	//post到支付服务器
    	$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
    	$responseList = json_decode($result->data,true);
    	return $responseList;
    }
    
    /**
     *获取连接的字符
     * @param unknown_type $prizeArr
     * @return boolean|string
     */
     public  function getJoinStr($prizeArr){
    	if(!is_array($prizeArr)){
    		return false;
    	}
    	foreach ($prizeArr as $val){
    		$str.=$val['aid'].$val['denomination'].$val['desc'].$val['endTime'].$val['startTime'].$val['uuid'];
    	}
    	return $str;
    }
    
    
    
    /**
     * 验证支付返回的结果
     */
    public   function verifyPaymentResult($paymentResult){
    	if(!is_array($paymentResult)){
    		return false;
    	}
    	//取得配置信息
    	list(,,$ciphertext) =$this->getPaymentConfig();
    	//处理支付服务器返回来的信息
    	if($paymentResult['success'] == true){
    		$reponseToken = $paymentResult['token'];
    		$reponseData  = $paymentResult['data'];
    		$reponseStr   = '';
    		foreach ($reponseData as $val){
    			$reponseStr.=$val['aid'].$val['ano'];
    		}
    		//验证支付服务器返回加密的密文
    		$token = md5($ciphertext.$paymentResult['msg'].$paymentResult['success'].$reponseStr);
    
    		//写日志
    		$logData= '进入父类，验证支付返回的token，reponseToken ='.$reponseToken.',token='.$token;
    		Common::WriteLogFile($this->mPath, $this->mFileName, $logData);
    		//验证
    		if(strtolower($reponseToken) == strtolower($token)){
    			return $reponseData;
    		}else{
    			//写入日志
    			$logData= '进入父类,返回的签名失败reponseToken ='.$reponseToken.',token='.$token;
    			Common::WriteLogFile($this->mPath, $this->mFileName, $logData);
    			return false;
    		}
    	}else{
    		//写入日志
    		$logData= '进入父类,支付返回的数据paymentResult='.$paymentResult['success'];
    		Common::WriteLogFile($this->mPath, $this->mFileName, $logData);
    		return false;
    	}
    }
    
    /**
     * 保存赠送的A券
     * @param unknown_type $send_arr
     */
    public  function saveSendTickets($prizeArr){
    	if(!is_array($prizeArr)){
    		return false;
    	}
    	//保存赠送A券记录
    	foreach ($prizeArr as $key=>$val){
    		$tmp[$key]['uuid'] = $val['uuid'];
    		$tmp[$key]['aid'] = $val['aid'];
    		$tmp[$key]['denomination'] = $val['denomination'];
    		$tmp[$key]['status'] = 0;
    		$tmp[$key]['send_type'] = $val['send_type'];
    		$tmp[$key]['sub_send_type'] = $val['sub_send_type'];
    		$tmp[$key]['consume_time'] = $val['consume_time'];
    		$tmp[$key]['start_time'] = $val['start_time'];
    		$tmp[$key]['end_time'] = $val['end_time'];
    		$tmp[$key]['description'] = $val['description'];
    		if($val['densection']){
    			$tmp[$key]['densection'] = $val['densection'];
    		}
    		if($val['third_type']){
    			$tmp[$key]['third_type'] = $val['third_type'];
    		}
    	}
    	$rs = Client_Service_TicketTrade::mutiFieldInsert($tmp);
    	return $rs;
    }
    
    /**
     * 处理支付返回结果，更新赠送A券的状态
     */
    public function updateSendTickets($responseData = array()){
    	if(!count($responseData)){
    		return false;
    	}
    	foreach ($responseData as $val){
    		$tradeParams['aid'] = $val['aid'];
    		$tradeData['status'] = 1;
    		$tradeData['out_order_id'] = $val['ano'];
    		$tradeData['update_time'] = Common::getTime();
    		//修改赠送A券的状态
    		$trade_rs = Client_Service_TicketTrade::updateBy($tradeData, $tradeParams);
    		if(!$trade_rs){
    			return false;
    		}
    	}
    	return true;
    }
    
    /**
     * 验证游戏的apiKey是否在专题中
     * @param unknown_type $apiKey
     * @param unknown_type $subjectId
     * @return boolean
     */
    public function checkApiKeyIsSuject($apiKey, $subjectId){
    	//对应专题
    	$game_params['api_key'] = $apiKey;
    	$game_info = Resource_Service_Games::getBy($game_params);
    	$params['subject_id']  = $subjectId;
    	$params['game_status'] = 1;
    	list(, $subject_games) = Client_Service_Game::getSubjectBySubjectId($params);
    	$subject_games = Common::resetKey($subject_games, 'resource_game_id');
    	$resource_game_ids = array_unique(array_keys($subject_games));
    	//写日志
    	$logData= '进入父类，游戏IDgame_id='.$game_info['id'].',resource_game_ids='.json_encode($resource_game_ids);
    	Common::WriteLogFile($this->mPath, $this->mFileName, $logData);
    
    	if(in_array($game_info['id'], $resource_game_ids)){
    		return true;
    	}
    	return false;
    }
    
    /**
     * 检查游戏ID是否在专题中
     * @param unknown_type $game_id
     * @param unknown_type $subject_id
     */
    public function checkGameIdIsSuject($gameId, $subjectId){
    	$params['subject_id']  = $subjectId;
    	$params['game_status'] = 1;
    	list(, $subject_games) = Client_Service_Game::getSubjectBySubjectId($params);
    	$subject_games = Common::resetKey($subject_games, 'resource_game_id');
    	$resource_game_ids = array_unique(array_keys($subject_games));
    	//写日志
    	$logData= '进入父类，gameId='.$gameId.',resource_game_ids='.json_encode($resource_game_ids);
    	Common::WriteLogFile($this->mPath, $this->mFileName, $logData);
    	if(in_array($gameId, $resource_game_ids)){
    		return true;
    	}
    	return false;
    
    }
	
	public function __destruct(){     //应用析构函数自动释放连接资源
		
	}
	 
	
}