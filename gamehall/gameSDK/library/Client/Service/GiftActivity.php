<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_GiftActivity extends Common_Service_Base{
    const GIFT_STATE_OPENED = 1;                //礼包开启
    const GIFT_STATE_CLOSEED = 0;               //礼包关闭
    const GAME_STATE_ONLINE = 1;                //游戏上线
    const GAME_STATE_OFFLINE = 0;               //游戏下线
    const INSTALL_DOWNLOAD_GAME_SEND_GIFT = 1;  //安装下载游戏送礼包
    const LOGIN_GAME_SEND_GIFT = 2;             //登陆游戏送礼包
    const SINGLE_GIFT = 1;                      //单个礼包
    const MULTIPLE_GIFT = 2;                    //多个礼包
    const INSTALL_GAME_SIGN = 1;                //下载安装游戏有奖标志
    const LOGIN_GAME_SIGN = 2;                  //安装登陆游戏有奖标志
    const ATICKET_SIGN = 3;                     //A券赠送有奖标志
    const INSTALL_LOGIN_GAME_SIGN = 4;          //下载安装登陆游戏有奖标志
    const INSTALL_GAME_ATICKET_SIGN = 5;        //下载安装游戏和A券赠送有奖标志
    const LOGIN_GAME_ATICKET_SIGN = 6;          //安装登陆游戏和A券赠送有奖标志
    const INSTALL_LOGIN_GAME_ATICKET_SIGN = 7;  //下载安装登陆游戏和A券赠送有奖标志
    const ONE_PRESENT  = 1;                     //一重礼
    const TWO_PRESENT  = 2;                     //二重礼
    const THREE_PRESENT  = 3;                   //三重礼
    const GIFT_EXPIRE = 7200;
    const LOG_FILE = "install_download.log";

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGiftActivity() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array(), $orderBy= array('sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList(intval($start), intval($limit), $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getBy($params, $orderBy= array('sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		return self::getDao()->getBy($params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsBy($params, $orderBy= array('sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		return self::getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftActivity($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
	    if (!is_array($data)) return false;
	    $data = self::cookData($data);
	    return self::getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGiftActivity($activityInfo, $giftActivationCodes) {
	    if (!is_array($activityInfo)) return false;
	    
 		$trans = parent::beginTransaction();
		try {
		    $giftId = self::addGiftActivation($activityInfo, $giftActivationCodes);
			
			if($trans) {
				$result = parent::commit();
				if ($result) {
					self::updataGiftActivityBaseInfoCache($activityInfo, $giftId);
					$remainGiftCount = self::getRemainGiftActivationCodeCount($giftId, $activityInfo);
					self::updateGiftActivityRemainNumCache($giftId, $remainGiftCount);
				}
				return $result;
			}
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
		return true;
	}
	
	private static function addGiftActivation($activityInfo, $giftActivationCodes) {
	    $giftId = self::addGiftActivityBaseInfo($activityInfo);
	    if (!$giftId) {
	        throw new Exception("Add Gift Activity Information Fail.", -201);
	    }
	    
	    $ret = self::addGiftActivationCodes($giftActivationCodes,$giftId, $activityInfo);
	    if (!$ret) {
	        throw new Exception("Add Gift Activity Codes Fail.", -202);
	    }
	    return $giftId;
	}
	
	public static function addGiftActivityBaseInfo($activityInfo) {
		if (!is_array($activityInfo)) return false;
		$activityInfo = self::cookData($activityInfo);
		$ret  =  self::getDao()->insert($activityInfo);
		if (!$ret) return $ret;
		return  self::getDao()->getLastInsertId();
	}
	
	
	public static function editGiftActivity($activityInfo, $oldActivityInfo) {
	    if (!is_array($activityInfo)) return false;
	     
	    $trans = parent::beginTransaction();
	    try {
	        self::updateGiftActivity($activityInfo, $oldActivityInfo);
	        	
	        if($trans) {
	            return parent::commit();
	        }
	    } catch (Exception $e) {
	        parent::rollBack();
	        return false;
	    }
	    return true;
	}
	
	private function updateGiftActivity($activityInfo, $oldActivityInfo) {
	    //echo "<pre>";
	    //print_r($activityInfo);exit;
	    $ret = self::update($activityInfo, $activityInfo['id']);
	    if(!$ret){
	        throw new Exception("update Gift Activity Information Fail.", -203);
	    }
	    
	    if($activityInfo['activity_type'] == self::SINGLE_GIFT){
	        $retLog = self::updateGiftActivityLog($activityInfo, $oldActivityInfo);
	        if(!$retLog){
	            throw new Exception("update Gift Activity Log Information Fail.", -204);
	        }
	    }
	}
	
	private function updateGiftActivityLog($activityInfo, $oldActivityInfo) {
	    if($activityInfo['gift_number'] == $oldActivityInfo['old_gift_number']){
	        return true;
	    }
	    
	    if($activityInfo['gift_number'] > $oldActivityInfo['old_gift_number']){
	        $giftTotal = Client_Service_GiftActivityLog::getGiftLogCount(array('gift_id'=>$activityInfo['id']));
	        $addGiftNumber = $activityInfo['gift_number'] - $giftTotal;
	        $giftActivationCodes = array($oldActivityInfo['gift_code']);
	        return self::addGiftActivationCodes($giftActivationCodes,
	                $activityInfo['id'], $activityInfo, $addGiftNumber);
	    }
	     
	    if($activityInfo['gift_number'] < $oldActivityInfo['old_gift_number']){
	        $giftTotal = Client_Service_GiftActivityLog::getGiftLogCount(array('gift_id'=>$activityInfo['id']));
	        $deleteNumber = $giftTotal - $activityInfo['gift_number'];
	        if($deleteNumber <= 0) {
	            return false;
	        }
	            
	        list(, $logs) = Client_Service_GiftActivityLog::getList(1, $deleteNumber,
	                array('gift_id'=>$activityInfo['id'],'status'=>Client_Service_GiftActivityLog::GIFT_NOT_SEND));
	        
	        $ids = $params = array();
	        foreach($logs as $key=>$value){
	            $ids[] = $value['id'];
	        }
	        
	        if(!$ids) {
	            return false;
	        }
	        
	        $params['id'] = array('IN',$ids);
	        $ret = Client_Service_GiftActivityLog::deleteBy($params);
	        if(!$ret){
	            return false;
	        }
	        
	        return true;
	    }
	}
	
	public static function updataGiftActivityBaseInfoCache($activityInfo , $giftId){
	    if(!$giftId){
	        return false;
	    }
	    $cache = Cache_Factory::getCache();
	    $cacheHash = $giftId.Util_CacheKey::GIFT_ACTIVITY_INFO;
	    $cache->hMset($cacheHash, $activityInfo);
	    $cache->expire($cacheHash, self::GIFT_EXPIRE);
	}
	
	public static  function getGiftBaseInfo($giftId){
	    if(!$giftId){
	        return false;
	    }
	    $cache = Cache_Factory::getCache();
	    $cacheHash = $giftId.Util_CacheKey::GIFT_ACTIVITY_INFO;
	    $giftInfo = $cache->hGetAll($cacheHash);
	    if($giftInfo['title']){
	        return $giftInfo;
	    }
	    
        $giftInfo = self::getGiftActivity($giftId);
        self::updataGiftActivityBaseInfoCache($giftInfo, $giftId);
        return $giftInfo;
	    
	}
	
	public static function updateGiftActivityRemainNumCache($giftId, $remainGiftCount){
	    if(!$giftId || !$remainGiftCount){
	        return false;
	    }
	    $cache = Cache_Factory::getCache();
	    $cacheHash = $giftId.Util_CacheKey::GIFT_ACTIVITY_INFO;
	    $cacheGiftActivityCountKey   = 'giftActivityCodeRemainNum';
	    $ret =  $cache->hSet($cacheHash, $cacheGiftActivityCountKey, $remainGiftCount, self::GIFT_EXPIRE);
	    return $ret;
	}
	
	public static function getRemainGiftActivationCodeCount($giftId, $activityInfo) {
	    $remainGiftCount = Client_Service_GiftActivityLog::getGiftLogCount(
	            array('status'=>Client_Service_GiftActivityLog::GIFT_ALREADY_SEND,
	                    'gift_id'=>$giftId));
	    
	    $giftTotal = Client_Service_GiftActivityLog::getGiftLogCount(array('gift_id'=>$giftId));
	    $remainGiftCount = $giftTotal - $remainGiftCount;
	    return $remainGiftCount;
	}
	
	public static function addGiftActivationCodes($giftActivationCodes, $giftId, $activityInfo, $addGiftNumber = 0) {
	    $giftLog = self::assembleGiftActivation($giftActivationCodes, $giftId, $activityInfo, $addGiftNumber);
	    $ret = Client_Service_GiftActivityLog::mutiGiftLog($giftLog);
	    if (!$ret) return false;
	    return true;
	}
	
    private static function assembleGiftActivation($giftActivationCodes, $giftId, $activityInfo, $addGiftNumber = 0) {
	    $giftLog = array();
	    if (!is_array($giftActivationCodes)) return $giftLog;
	    $giftLog = self::getGiftActivationArray($giftActivationCodes, $giftId, $activityInfo, $addGiftNumber);
	    return $giftLog;
	}
	
	
	private static function getGiftActivationArray($giftActivationCodes, $giftId, $activityInfo, $addGiftNumber = 0){
	    $ret = Client_Service_GiftActivityLog::getBy(array('gift_id'=>$giftId),array('send_order'=>'DESC'));
	    $maxSendOrder = intval($ret['send_order']);
	    
	    $giftLog = array();
	    if($activityInfo['gift_num_type'] == self::SINGLE_GIFT){
	         $giftTotal = $addGiftNumber ? $addGiftNumber : $activityInfo['gift_number'];
	         for($i=0; $i< $giftTotal; $i++) {
	            $maxSendOrder ++;
	            $giftLog[] = array(
	                    'id'=>'',
	                    'gift_id'=>$giftId,
	                    'game_id'=>$activityInfo['game_id'],
	                    'uuid'=>'',
	                    'uname'=>'',
	                    'imei'=>'',
	                    'imeicrc'=>'',
	                    'activation_code'=>$giftActivationCodes[0],
	                    'create_time'=>'',
	                    'status'=>0,
	                    'send_order'=>$maxSendOrder
	            );
	        }
	    } else {
	        foreach($giftActivationCodes as $key=>$value){
	            $maxSendOrder ++;
	            $giftLog[] = array(
	                    'id'=>'',
	                    'gift_id'=>$giftId,
	                    'game_id'=>$activityInfo['game_id'],
	                    'uuid'=>'',
	                    'uname'=>'',
	                    'imei'=>'',
	                    'imeicrc'=>'',
	                    'activation_code'=>$value,
	                    'create_time'=>'',
	                    'status'=>0,
	                    'send_order'=>$maxSendOrder
	            );
	        }
	    }
	    return $giftLog;
	}
	
	public static function batchActivitySort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	
	public static function sendGiftActivationCode($userInfo, $gameId){
	    $uuid = $userInfo['uuid'];
	
	    $giftActivities = self::getEffectiveActivity($gameId, $userInfo['activity_type']);
	    $debugMsg = array('msg' => "查找赠送活动".json_encode($userInfo), 'event'=> $giftActivities);
	    self::debugGrab($debugMsg);
	    if(!$giftActivities){
	        return;
	    }
	    
	    foreach($giftActivities as $key=>$value){
	        $remainActivationCode = array();
	        $sendLog = Client_Service_GiftActivityLog::getBy(array('uuid'=>$uuid,'gift_id'=>$value['id']));
	        $debugMsg = array('msg' => "该活动是否赠送过 ", 'event'=> $sendLog);
	        self::debugGrab($debugMsg);
	        if($sendLog)  {
	            continue;
	        }
	        
	        if($userInfo['activity_type'] == self::INSTALL_DOWNLOAD_GAME_SEND_GIFT){
	            $ret = self::isFinishTaskRule($userInfo, $gameId, $value);
	            if(!$ret)  {
	                continue;
	            }
	        }
	
	        $remainActivationCode = self::getRemainActivationCode($gameId, $value['id']);
	        $debugMsg = array('msg' => "查找是否有激活码 ", 'event'=> $remainActivationCode);
	        self::debugGrab($debugMsg);
	        if(!$remainActivationCode){
                //刷新单个游戏有奖礼包附加属性
                Resource_Service_GameExtraCache::refreshGameRewardGift($gameId);
	            continue;
	        }
	        
	        self::updateActivationCodeLog($userInfo, $value, $remainActivationCode);
	        self::saveMsg($userInfo, $gameId);
	    }
	}
	
	private static function isFinishTaskRule($userInfo, $gameId, $giftActivityInfo){
	    $downloadLog = self::getDownloadLog($userInfo, $gameId, $giftActivityInfo);
	    if(!$downloadLog)  {
	        $debugMsg = array('msg' => "查找是否完成下载安装游戏规则 ", 'event'=> $downloadLog);
	        self::debugGrab($debugMsg);
	        return false;
	    }
	    
	    $startdownversion = $downloadLog['startdownversion'];
	    $downfinishversion = $downloadLog['downfinishversion'];
	    if(strnatcmp($startdownversion, '1.5.8') < 0 || strnatcmp($downfinishversion, '1.5.8') < 0){
	        $debugMsg = array('msg' => "开始下载和安装游戏版本小于1.5.8 ", 'event'=> $downloadLog);
	        self::debugGrab($debugMsg);
	        return false;
	    }
	    
	    return true;
	}
	
	private static function getDownloadLog($userInfo, $gameId, $giftActivityInfo){
	    return self::getScheduleLogBy(
	            array('uuid'=>$userInfo['uuid'],
                      'activity_id'=>$giftActivityInfo['id'],
                      'activity_type'=>self::INSTALL_DOWNLOAD_GAME_SEND_GIFT,
                      'downperiod'=>Client_Service_Download::DOWNLOAD_FINISHED_GAME,
                      'game_id'=>$gameId,
	            ));
	}
	
	private static function saveMsg($userInfo, $gameId) {
	    $uuid = $userInfo['uuid']; 
	    $activityType = $userInfo['activity_type'];
	    $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
	    $title = "您获得".$gameInfo['name']."限量礼包";
	    if($activityType == self::LOGIN_GAME_SEND_GIFT){
	        $sendType ="登陆";
	    } else {
	        $sendType ="安装";
	    }
	    $desc ="恭喜，您成功下载".$sendType . $gameInfo['name']."，获得".$gameInfo['name']."限量礼包，请尽快使用！";
	    
	    $msgType = Game_Service_Msg::SEND_GIFT;
	    Game_Service_Msg::saveMsg($uuid,$msgType,$desc,$title);
	}
	
	public static function getEffectiveActivity($gameId, $activityType){
	    $parmes = array();
	    $parmes['status'] = self::GIFT_STATE_OPENED;
	    $parmes['game_status'] = self::GAME_STATE_ONLINE;
	    $currentTime = strtotime(date('Y-m-d H:00:00'));
	    $parmes['effect_start_time'] = array('<=', $currentTime);
	    $parmes['effect_end_time'] = array('>=', $currentTime);
	    $parmes['game_id'] = $gameId;
	    $parmes['activity_type'] = $activityType;
	    return  self::getsBy($parmes);
	     
	}
	
	private static function getRemainActivationCode($gameId, $giftId){
	    $remainActivationCode = Client_Service_GiftActivityLog::getBy(
	            array('status'=>Client_Service_GiftActivityLog::GIFT_NOT_SEND,
	                   'gift_id'=>$giftId),
	            array('send_order'=>'ASC',
	                   'id'=>'ASC'));
	    return $remainActivationCode;
	}
	
	private static function updateActivationCodeLog($userInfo, $giftActivityInfo, $remainActivationCode){
	    $time = Common::getTime();
	    $imeicrc = crc32($userInfo['imei']);
	    $updateField = array('uname' => $userInfo['uname'],
	                         'imei'=>$userInfo['imei'],
	                         'uuid'=>$userInfo['uuid'],
	                         'imeicrc'=>crc32($imeicrc),
	                         'create_time'=>$time,
	                         'status'=>Client_Service_GiftActivityLog::GIFT_ALREADY_SEND);
	    $ret = Client_Service_GiftActivityLog::updateById($updateField, $remainActivationCode['id']);
	    if($ret){
	        $remainGiftNum = self::getRemainGiftActivationCodeCount($giftActivityInfo['id'], $giftActivityInfo);
	        self::updateGiftActivityRemainNumCache($giftActivityInfo['id'], $remainGiftNum);
	        self::addGiftlog($updateField, $giftActivityInfo, $remainActivationCode, $userInfo['version']);
	    }
	}
	
	private static function addGiftlog($updateField, $giftActivityInfo, $remainActivationCode, $version){
	    $myGiftLogInfo = array('id' => '',
	                           'log_type' => Client_Service_Giftlog::SEND_GIFT_LOG,
	                           'gift_id' => $giftActivityInfo['id'],
	                           'game_id' => $giftActivityInfo['game_id'],
	                           'uname' => $updateField['uname'],
	                           'imei'=>$updateField['imei'],
	                           'imeicrc'=>$updateField['imeicrc'],
	                           'activation_code'=>$remainActivationCode['activation_code'],
	                           'create_time'=>$updateField['create_time'],
	                           'status'=>Client_Service_GiftActivityLog::GIFT_ALREADY_SEND,
	                           'send_order'=>$remainActivationCode['send_order'],
	    );
	   Client_Service_Giftlog::addGiftlog($myGiftLogInfo);
	}
	
	public static function updateGiftActivityByGameStatus($status, $gameId) {
	    self::updateBy(array('game_status'=>$status), array('game_id'=>$gameId));
	}
	
	private function checkIsCorrectRequestParams($data){
	    $imei = $data['imei'];
	    $uname = $data['uname'];
	     
	    $online = Account_Service_User::checkOnline($uname, $imei);
	    if(!$online) {
	        return false;
	    }
	     
	    $imeiDecrypt = null;
	    if ($imei != Util_Imei::EMPTY_IMEI) {
	        $imeiDecrypt = Util_Imei::decryptImei($imei);
	        if (!Util_Imei::isValidDeviceId($imeiDecrypt)) {
	            return false;
	        }
	    }
	}
	
	public static function debugGrab($debugMsg) {
	    Util_Log::info('sendGiftActivationCode', self::LOG_FILE, $debugMsg);
	}
	
	public static function addDownloadScheduleLog($downloadLog) {
	    if (!is_array($downloadLog)) return false;
	    $downloadLog = self::cookScheduleData($downloadLog);
	    return  self::getScheduleDao()->insert($downloadLog);
	}
	
	public static function getsScheduleLogBy($params = array(), $orderBy = array('id'=>'DESC', 'activity_id' => 'DESC')) {
	    return self::getScheduleDao()->getsBy($params, $orderBy);
	}
	
	public static function getScheduleLogBy($params = array(), $orderBy = array('id'=>'DESC', 'activity_id' => 'DESC')) {
		return self::getScheduleDao()->getBy($params, $orderBy);
	}
	
	public static function updateScheduleLogBy($data, $param) {
	    if (!is_array($param) || !is_array($data) ){
	        return false;
	    }
	    return self::getScheduleDao()->updateBy($data, $param);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['activity_type'])) $tmp['activity_type'] = $data['activity_type'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['use_start_time'])) $tmp['use_start_time'] = $data['use_start_time'];
		if(isset($data['use_end_time'])) $tmp['use_end_time'] = $data['use_end_time'];
		if(isset($data['effect_start_time'])) $tmp['effect_start_time'] = $data['effect_start_time'];
		if(isset($data['effect_end_time'])) $tmp['effect_end_time'] = $data['effect_end_time'];
		if(isset($data['gift_num_type'])) $tmp['gift_num_type'] = $data['gift_num_type'];
		if(isset($data['gift_number'])) $tmp['gift_number'] = $data['gift_number'];
		if(isset($data['method'])) $tmp['method'] = $data['method'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_status'])) $tmp['game_status'] = intval($data['game_status']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	private static function cookScheduleData($data) {
	    $tmp = array();
	    if(isset($data['id'])) $tmp['id'] = intval($data['id']);
	    if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
	    if(isset($data['activity_id'])) $tmp['activity_id'] = intval($data['activity_id']);
	    if(isset($data['activity_type'])) $tmp['activity_type'] = intval($data['activity_type']);
	    if(isset($data['downperiod'])) $tmp['downperiod'] = $data['downperiod'];
	    if(isset($data['startdownversion'])) $tmp['startdownversion'] = $data['startdownversion'];
	    if(isset($data['downfinishversion'])) $tmp['downfinishversion'] = $data['downfinishversion'];
	    if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
	    return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_GiftActivity
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_GiftActivity");
	}
	
	/**
	 * 
	 * @return Client_Dao_GiftActivityLog
	 */
	private static function getLogDao() {
		return Common::getDao("Client_Dao_GiftActivityLog");
	}
	
	/**
	 *
	 * @return Client_Dao_DownloadSchedule
	 */
	private static function getScheduleDao() {
	    return Common::getDao("Client_Dao_DownloadSchedule");
	}
}
