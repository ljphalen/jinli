<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Point_Service_Prize extends Common_Service_Base{
	//百万级概率
	private static $_maxRate = 1000000;
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params, $orderBy = array('id'=>'desc')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $result);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return multitype:unknown multitype:
	 */
	public static function getListLog($page = 1, $limit = 10, $params, $orderBy = array('id'=>'desc')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::_getLogDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getLogDao()->count($params);
		return array($total, $result);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param unknown $params
	 * @return string
	 */
	public static function getLogCount($params) {
	    return self::_getLogDao()->count($params);
	}
	
	/**
	 *
	 * 获取抽奖活动数据
	 * @param int $id
	 */
	public static function getPrize($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 获取所有抽奖活动
	 * @param array $orderBy
	 * @return 
	 */
	public static function getAllPrize($orderBy=array()) {
		return self::_getDao()->getAll($orderBy);
	}
	/**
	 *
	 * 根据提交搜索抽奖活动基本数据
	 * @param array $params
	 * @param array $orderBy
	 * @return
	 */
	public static function getByPrize($params, $orderBy=array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 *
	 * 根据提交搜索抽奖活动基本数据
	 * @param array $params
	 * @param array $orderBy
	 * @return
	 */
	
	public static function getsByPrize($params, $orderBy=array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	
	/**
	 *
	 * 根据提交搜索抽奖日志数据
	 * @param array $params
	 * @param array $orderBy
	 * @return
	 */
	public static function getsByLog($params, $orderBy=array()) {
		if(!is_array($params)) return false;
		return self::_getLogDao()->getsBy($params, $orderBy);
	}
	
	/**
	 *
	 * 根据条件查询抽奖日志数据
	 * @param array $params
	 * @param array $orderBy
	 * @return
	 */
	public static function getByLog($params, $orderBy=array()) {
		if(!is_array($params)) return false;
		return self::_getLogDao()->getBy($params, $orderBy);
	}
	
	/**
	 *
	 * 获取抽奖活动配置数据
	 * @param int $id
	 */
	public static function getConfig($id) {
		if (!intval($id)) return false;
		return self::_getConfigDao()->getsBy(array('prize_id'=>$id), array('pos'=>'asc'));
	}
	
	/**
	 * 根据id获取奖项
	 * @param int $id
	 */
	public static function getConfigById($id) {
	    if (!intval($id)) return false;
	    return self::_getConfigDao()->get(intval($id));
	}
	
	/**
	 * 按条件获取奖项配置数据
	 * @param array $params
	 * @param array $orderBy
	 * @return 
	 */
	public static function getsByConfig($params, $orderBy=array()){
		if(!is_array($params)) return false;
		return self::_getConfigDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 保存抽奖活动数据
	 * @param unknown $prize
	 * @param unknown $config
	 * @return boolean
	 */
	public static function save($prize, $config){
		if (!is_array($prize) || !is_array($config)) return false;
		$time =Common::getTime();
		//开始事务
		parent::beginTransaction();
		try {
			//事务提交
			$prize['create_time'] =$time;
			$prizeId = self::addPrize($prize);
			if(!$prizeId)  throw new Exception("add prizeData fail.", -202);
			foreach ($config as $item){
				$item['prize_id'] = $prizeId;
				$item['create_time'] = $time;
				$ret = self::addPrizeConfig($item);
				if(!$ret) throw new Exception("add prizeConfig fail.", -202);
			}
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 更新抽奖活动
	 * @param array $prize
	 * @param array $config
	 * @return
	 */
	public static function update($prize, $config) {
		if (!is_array($prize) || !is_array($config)) return false;
		//开始事务
		parent::beginTransaction();
		try {
			//事务提交
			$id = $prize['id'];
			unset($prize['id']);
			$ret = self::updatePrize($prize, array('id'=>$id));
			if(!$ret)  throw new Exception("update prizeData fail.", -202);
			foreach ($config as $item){
				$cid = $item['id'];
				unset($item['id']);
				$ret = self::updatePrizeConfig($item, array('id' => $cid));
				if(!$ret) throw new Exception("update prizeConfig fail.", -202);
			}
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	
	/**
	 * 
	 * 添加抽奖活动
	 * @param array $data 活动数据
	 * 
	 */
	public static function addPrize($data) {
		if (!is_array($data) ) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if(!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 *
	 * 添加抽奖活动奖项
	 * @param array $data 奖项数据
	 *
	 */
	public static function addPrizeConfig($data) {
		if (!is_array($data) ) return false;
		$data = self::_cookConfigData($data);
		return self::_getConfigDao()->insert($data);
	}
	
	/**
	 * 更新抽奖活动
	 * @param array $data
	 * @param int $id
	 * @return 
	 */
	public static function updatePrize($data, $params) {
		if (!is_array($data) ) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 更新抽奖配置选项
	 * @param array $data
	 * @param int $id
	 * @return
	 */
	public static function updatePrizeConfig($data, $params) {
		if (!is_array($data) ) return false;
		$data = self::_cookConfigData($data);
		return  self::_getConfigDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * 添加用户抽奖日志
	 * @param array $data 日志数据
	 *
	 */
	public static function addLog($data) {
		if (!is_array($data) ) return false;
		$data = self::_cookLogData($data);
		$ret = self::_getLogDao()->insert($data);
		if(!$ret) return false;
		return self::_getLogDao()->getLastInsertId();
	}
	
	/**
	 * 更新用户抽奖日志
	 * @param array $data
	 * @param array $params
	 * @return
	 */
	public static function updateLog($data, $params) {
		if (!is_array($data) ) return false;
		$data = self::_cookLogData($data);
		return self::_getLogDao()->updateBy($data, $params);
	}
	
	/**
	 * 执行抽奖方法
	 * @param array $data
	 * 
	 * @return array
	 */
	public static function runPrize($data){
		//开始事务
		parent::beginTransaction();
		try {
			//扣除抽奖积分
			$userPoint = Account_Service_User::getUserInfo(array('uuid'=>$data['uuid']));
			if(!$userPoint) throw new Exception("not found user fail.", -202);
			//扣除积分消费
			$result  = Account_Service_User::subtractUserPoint($data['point'], array('uuid'=>$data['uuid']));
			if(!$result) throw new Exception("subtract user points fail.", -202);
			//消费日志
			$consumeLog = array(
					'uuid' => $data['uuid'],
					'consume_type'=> 2,
					'consume_sub_type' => $data['prizeId'],
					'points' => $data['point'],
					'create_time' => $data['time'],
					'update_time' => $data['time'],
			);
			$logId = Point_Service_Consume::add($consumeLog);
			if(!$logId)  throw new Exception("add consumeLog fail.", -202);
			//开始抽奖
			$prizeResult = self::start($data['prizeId'], $data['time']);
			//记录抽奖日志
			$logData = array(
					'uuid' => $data['uuid'],
					'uname'=> $data['uname'],
					'prize_id' => $data['prizeId'],
					'prize_status' => ($prizeResult['type']==0) ? 0 : 1,
					'prize_cid' => $prizeResult['id'],
					'create_time' => $data['time']
			);
			$logResult = self::addLog($logData);
			if(!logResult) throw new Exception("add prize log fail.", -202);
			//活动参人数与中奖人数处理
			$userResult = self::prizeUser($prizeResult);
			if(!$userResult) throw new Exception("update prize user num fail.", -202);
			//增加输出字段
			$prizeResult['logId'] = $logResult;
			$prizeResult['uuid'] = $data['uuid'];
			parent::commit();
			return array(true, $prizeResult);
		} catch (Exception $e) {
			parent::rollBack();
			return array(false, array());
		}
	}
	
	/**
	 * 抽中后A券与积分处理
	 * @param array $result
	 * @param string $uuid
	 * @return boolean
	 */
	public static function afterPrize($data){
		$data = self::assembleSp($data);
		$ret = self::inspectorIsValidRequest($data);
		switch ($data['type']){
			case 2:
				//A券赠送
				$ticketData = array('uuid'=>$data['uuid'],
						'type'=>5,
						'task_id'=>$data['prizeId'],
						'section_start'=>1,
						'section_end'=>$data['day'],
						'denomination'=>$data['amount'],
						'desc'=>"抽奖活动",
				);
				if($ret){
					$result = self::sendTicket($ticketData);
					if($result){
						Point_Service_Prize::updateLog(array('send_status'=>1,'send_time'=>time()), array('id'=>$data['logId']));
					}
				}
				break;
			case 3:
				//积分赠送
				$gainData = array(
						'uuid' => $data['uuid'],
						'gain_type'=> 5,
						'gain_sub_type' => $data['prizeId'],
						'points' => $data['amount'],
						'create_time' => $data['time'],
						'update_time' => $data['time'],
						'stauts'=>1
				);
				if($ret){
					$result = self::sendPoint($gainData);
					if($result){
						Point_Service_Prize::updateLog(array('send_status'=>1,'send_time'=>time()), array('id'=>$data['logId']));
					}
				}
				break;
			default:
				$result = true;
		}
		return $result;
	}
	
	private static function assembleSp($data) {
		$spArr = Common::parseSp($data['sp']);
		$data['version'] = $spArr['game_ver'];
		$data['imei'] = $spArr['imei'];
		return $data;
	}
	
    private static function inspectorIsValidRequest($data) {
    	if(strnatcmp($data['version'], '1.5.7') < 0 ) {
    		return true;
    	} else {
    		if(!$data['serverId']){
    			return false;
    		}
    		
    		$apiName = strtoupper('index');
    		$verifyInfo = array();
    		$imeiDecrypt = Util_Imei::decryptImei($data['imei']);
    		$verifyInfo['apiName'] = $apiName;
    		$verifyInfo['uuid'] = $data['uuid'];
    		$verifyInfo['uname'] = $data['uname'];
    		$verifyInfo['imei'] = $imeiDecrypt;
    		$verifyInfo['version'] = $data['version'];
    		$verifyInfo['serverId'] = $data['serverId'];
    		$ret = self::verifyEncryServerId($verifyInfo);
    		if(!$ret){
    			return false;
    		}
    		
    		return true;
    	}
    }
    
	/**
	 * @param info
	 * @param imeiDecrypt
	 * @param ret
	 */
	private static function verifyEncryServerId($info) {
		$keyParam = array(
				'apiName' => $info['apiName'],
				'imei' => $info['imei'],
				'uname' => $info['uname'],
		);
		$ivParam = $info['uuid'];
		$serverId = $info['serverId'];
		$serverIdParam = array(
				'clientVersion' => $info['version'],
				'imei' => $info['imei'],
				'uname' => $info['uname'],
		);
		return Util_Inspector::verifyServerId($keyParam, $ivParam, $serverId, $serverIdParam);
	}
	
	/**
	 * 更新抽奖用户参数数量
	 * @param array $data
	 * @return 
	 */
	private static function prizeUser($data){
		$prize = self::getByPrize(array('id'=>$data['prize_id']));
		$user['join_num'] = $prize['join_num']+1;
		if(in_array($data['type'], array(1,2,3))){
			$user['win_num'] = $prize['win_num']+1;
		}
		$result = self::updatePrize($user, array('id'=>$prize['id']));
		return $result;
	}
	
	
	/**
	 * 送A券
	 * @param array $data
	 */
	private static function sendTicket($data){
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($data));
		$result = $activity->sendTictket();
		return $result;
	}
	
	/**
	 * 赠送用户积分
	 * @param array $data
	 */
	private static function sendPoint($data){
	  $sendResult = Point_Service_User::gainPoint($data);
	  if(!$sendResult) return false;
	  return true;
	}
	
	/**
	 * 时间间隔非法判断
	 * @param array $result
	 * @param int $time
	 * @return boolean
	 */
	private static function isTimeIntervalInvaild($result, $time){
	    //判断该奖项中奖间隔
	    $lastLog = self::getByLog(array('prize_cid' => $result['id']), array('create_time' => 'DESC'));
	    if(!$lastLog){
	        return false;
	    }
	    //时间间隔计算处理
	    $timeInterval = $lastLog['create_time'] + $result['min_space'];
	    if($timeInterval >= $time){
	       return true;
	    }
	    return false;
	}
	
	/**
	 * 每日发放数量非法判断
	 * @param array $result
	 * @return boolean
	 */
	private static function isSendTotalInvaild($result){
	    if(intval($result['max_win']) == 0){
	        return true;
	    } 
	    //查看该奖项今日是否已发送完毕
	    $curr_time = date('Y-m-d', Common::getTime());
	    $start_time = strtotime($curr_time.' 00:00:00');
	    $end_time = strtotime($curr_time.' 23:59:59');
	    $params = array(
	          'prize_cid' => $result['id'],
	          'create_time'=> array(array('>=', $start_time), array('<=', $end_time))
	    );
	    $logs = self::getsByLog($params, array('create_time' => 'DESC'));
	    $total = count($logs);
	    if($total >= intval($result['max_win'])){
	        //今日已发放完毕,改为未中。
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 数据无效判断
	 * @param array $result
	 * @param array $time
	 * @return boolean
	 */
	private function isDataInvaild($result, $time){
	    if(empty($result)){
	        return true;
	    }
	    
	    if(self::isTimeIntervalInvaild($result, $time)){
	       return true; 
	    }
	    
	    if( self::isSendTotalInvaild($result)){
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 抽奖主体方法
	 * @param int $prizeId
	 * @param int $time
	 * @return array
	 */
	private static function start($prizeId, $time){
		$configData = self::getsByConfig(array('prize_id'=>$prizeId));
		list($not, $use) = self::init($configData);
		$result = self::process($use);
		if(self::isDataInvaild($result, $time)){
			//不中的奖项随机返回一项
			$key = array_rand($not);
			$result = $not[$key];
		}
		return $result;
	}
	
	/**
	 * 初始化奖项
	 * @param array $config
	 * @return array
	 */
	private static function init($config){
		$use = $not = array();
		foreach ($config as $item){
			if($item['type'] == 0){
				$not[] = $item;
			}else {
				$use[] = $item;
			}
		}
		return array($not,$use);
	}
	
	/**
	 * 开始抽奖
	 * @param array $config
	 * @return int
	 * 
	 */
	private static function process($config){
		$max = self::$_maxRate-1;
		$luck = mt_rand(0, $max);
		$result = array();
		$range = 0;
		foreach( $config as $value ){
			if ($value['probability'] == 0) continue;
			if (($luck >= $range) && ($luck < $range + $value['probability'])) {
				$result = $value;
				break;
			} else {
				$range += $value['probability'];
			}
		}
		return $result;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['point'])) $tmp['point'] = $data['point'];
		if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['join_num'])) $tmp['join_num'] = $data['join_num'];
		if(isset($data['win_num'])) $tmp['win_num'] = $data['win_num'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookConfigData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['prize_id'])) $tmp['prize_id'] = $data['prize_id'];
		if(isset($data['pos'])) $tmp['pos'] = $data['pos'];
		if(isset($data['type'])) $tmp['type'] = $data['type'];
		if(isset($data['amount'])) $tmp['amount'] = $data['amount'];
		if(isset($data['day'])) $tmp['day'] = $data['day'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['probability'])) $tmp['probability'] = $data['probability'];
		if(isset($data['min_space'])) $tmp['min_space'] = $data['min_space'];
		if(isset($data['max_win'])) $tmp['max_win'] = $data['max_win'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookLogData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['prize_id'])) $tmp['prize_id'] = $data['prize_id'];
		if(isset($data['prize_status'])) $tmp['prize_status'] = $data['prize_status'];
		if(isset($data['prize_cid'])) $tmp['prize_cid'] = $data['prize_cid'];
		if(isset($data['receiver'])) $tmp['receiver'] = $data['receiver'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if(isset($data['address'])) $tmp['address'] = $data['address'];
		if(isset($data['send_status'])) $tmp['send_status'] = $data['send_status'];
		if(isset($data['send_time'])) $tmp['send_time'] = $data['send_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	
	/**
	 * 
	 * @return Point_Dao_Prize
	 */
	private static function _getDao() {
		return Common::getDao("Point_Dao_Prize");
	}
	
	/**
	 *
	 * @return Point_Dao_PrizeConfig
	 */
	private static function _getConfigDao() {
		return Common::getDao("Point_Dao_PrizeConfig");
	}
	
	/**
	 *
	 * @return Point_Dao_PrizeLog
	 */
	private static function _getLogDao(){
		return Common::getDao("Point_Dao_PrizeLog");
	}
}
