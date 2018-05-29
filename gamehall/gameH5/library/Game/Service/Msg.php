<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Service_Msg {
	const TARGET_SPECIFIED_ACCOUNT = 1;
	const TARGET_ALL_ACOUNT = 0;
	const TARGET_ALL_USER = 2;
	const TARGET_OTHER = 3;
	
	static $status     = array( 0 => '关闭'  , 1 => '开启');
	static $mapStatus  = array( 0 => '未读'  , 1 => '已读');
	static $reciver    = array( 0 => '所有帐号', 1 => '指定帐号UUID'/*,2 => '指定IMEI'*/);
		
	static $msgTopType = array( 
			0 => array('key'=>'mall','txt'=>'全部'),
			100 => array('key'=>'msys','txt'=>'系统'),
			200 => array('key'=>'mhot','txt'=> '热门')
	);
	static $msgType    = array(			
		101  =>  array('key'=>'taskComplete','txt'=>'任务完成消息'),
		102  =>  array('key'=>'ATickExpire' ,'txt'=>'A券过期消息'),
		103  =>  array('key'=>'ATickGive'   ,'txt'=>'A券赠送消息'),			
		/*
		104  =>  array('key'=>'','txt'=>'充值成功消息'),
		105  =>  array('key'=>'','txt'=>'消费成功消息'),		
		106  =>  array('key'=>'','txt'=>'沉默用户消息'),			
		*/		
		107  =>  array('key'=>'PointGive'   ,'txt'=>'积分赠送消息'),
		108  =>  array('key'=>'PointExpire'   ,'txt'=>'积分过期消息'),
		201  =>  array('key'=>'custom','txt'=>'游戏内容'),		
		202  =>  array('key'=>'custom','txt'=>'专题'),
		203  =>  array('key'=>'custom','txt'=>'分类'),
		204  =>  array('key'=>'custom','txt'=>'活动'),
		205  =>  array('key'=>'custom','txt'=>'链接'),
		206  =>  array('key'=>'custom','txt'=>'礼包'),
		207  =>  array('key'=>'msgText','txt'=>'文本信息'),
		
	);
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderby = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderby );
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getMsg($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsByMsg($params, $orderBy=array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNewMsg($params,$orderBy=array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateMsgInfo($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateMsg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);				
		$res = self::_getDao()->update($data, intval($id));
		if($res) {
			$mapRes = self::_getMapDao()->deleteBy(array('msgid'=>$id));	
			$sendInputs = $_POST['sendInput'];
			self::handleMsgMap($data['totype'],$id,$sendInputs);			
		}		
		return $res;
	}
	
	public static function updateMsgBat($data,$statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::_getDao()->update(array('operate_status'=>$statu), $value);
		}
		return $ret;
	}
	
	
	public static function updateBy($data,$param) {
		return self::_getDao()->updateBy($data,$param);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteMsg($id) {
		$res = self::_getDao()->delete(intval($id));		
		if($res) {
			self::_getMapDao()->deleteBy(array('msgid'=>$id));			
		}	
		return $res;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addMsg($data) {
		if (!is_array($data)) return false;
		$sendInputs = $_POST['sendInput'];
		$data = self::_cookData($data);
		$currTime = Common::getTime();
		$data['create_time'] = $currTime;	
		$data['update_time'] = $currTime;
		$res   = self::_getDao()->insert($data);	
		if(!$res) {
			return false;
		}
		$msgId = self::_getDao()->getLastInsertId(); 	
		
		self::handleMsgMap($data['totype'],$data['type'],$msgId,$sendInputs,$data['create_time']);
		
		return $res;	
	}
	
	/**
	 * 根据类型结合配置开关
	 * @param int $type
	 */
	public static function isNewMsgEnabled($type){
		$state = 0;
		$configName = '';
		switch(intval($type)){
			case 102:
				//A券过期
				$configName = 'msg_atickexpire';
				break;
			case 103:
				//A券赠送
				$configName = 'msg_atickgive';
				break;
			case 108:
				//积分过期
				$configName = 'msg_pointexpire';
				break;
			case 201:
			case 202:
			case 203:
			case 204:
			case 205:
			case 206:
			case 207:
				//运营开关
				$configName = 'msg_yunying';
				break;
		}
		if ($configName) {
			$state = Game_Service_Config::getValue($configName);
		}
		return $state == 1 ? true:false;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addApiMsg($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = time();
		$data['update_time'] = time();
		$res   = self::_getDao()->insert($data);
		if(!$res) {
			return false;
		}
		$msgId = self::_getDao()->getLastInsertId();
	
		self::handleApiMsgMap($data['type'],$msgId,$data['sendInput'],$data['start_time']);
	
		return $msgId;
	}
	
	public static function addMap($data) {
		if(!is_array($data)) return false;
		empty($data['uid'])  && $data['uid'] = -1;
		empty($data['imei']) && $data['imei'] = -1;
		
		if($data['status'] == 1) {
			$data['read_time'] = time();
		}
		
		return self::_getMapDao()->insert($data);
	}
	
	public static function handleApiMsgMap($type,$msgId,$uuid,$start_time) {
	    $tempData = array(
					'msgid'   => $msgId,
					'uid'     => trim($uuid),
					'imei'    => '-1',
				 	'status'  => 0,
	    		    'top_type'  => 100,
	    		    'type'     => $type,
	    		    'create_time'    => $start_time,
		  );
		$ret = self::_getMapDao()->getBy(array('uid'=>$uuid,'msgid'=>$msgId));
		if(!$ret) self::_getMapDao()->insert($tempData);
	}
	
	public static function handleMsgMap($type,$rtype,$msgId,$sendInputs,$start_time) {
		$sendInput = explode(',',trim($sendInputs));
		$sendInput = array_unique($sendInput);
		if(empty($sendInput)) {
			return false;
		}
		switch($type) {
			case 1 : //指定帐号				
				foreach($sendInput as $account) {
					$accountInfo = Account_Service_User::getUser(array('uname'=>$account));
					if(empty($accountInfo) || empty($accountInfo['uuid'])) continue;
					$uuid = $accountInfo['uuid'];
					$tempData = array(
							'msgid'   => $msgId,
							'uid'     => trim($uuid),
							'imei'    => '-1',
							'status'  => 0,
							'top_type'  => 200,
							'type'  => $rtype,
							'create_time'    => $start_time,
					);
					$exsit = self::_getMapDao()->getBy(array('uid'=>$uuid,'msgid'=>$msgId));
					if(empty($exsit)) {						
						self::_getMapDao()->insert($tempData);
					}
				}
				break;
			case 2 : //指定IMEI				
				foreach($sendInput as $imei) {					
					$tempData = array(
							'msgid'   => $msgId,
							'uid'     => '-1',
							'imei'    => trim($imei),
							'status'  => 0,
							'top_type'  => 200,
							'type'  => $rtype,
							'create_time'    => $start_time,
					);
					$exsit = self::_getMapDao()->getBy(array('imei'=>$imei,'msgid'=>$msgId));
					if(empty($exsit)) {						
						self::_getMapDao()->insert($tempData);
					}
				}
				break;
			default:
				break;
		}	
	}

	
	/**
	 * 热门消息
	 * @param array $v
	 * @return array
	 */
	public static function genMsgOutput($v, $isPush = false) {
		switch($v['type']) {
			case 201 : //游戏
				$game = Resource_Service_GameData::getGameAllInfo($v['contentId']);
				$viewType = Util_JsonKey::GAME_DETAIL_VIEW;
				$param    = array(
						'url'      => '',
						'contentId'=> $v['contentId'],
						'gameId'   => $v['contentId'],
						'title'    => self::convertSting($v['title']),
						'package' => $game['package']
				);
				break;
			case 202 : //专题
				$viewType = Util_JsonKey::TOPIC_DETAIL_VIEW;
				$param    = array(
						'url'      => '',
						'contentId'=> $v['contentId'],
						'gameId'   => '',
						'title'    => self::convertSting($v['title']),
				);
				break;
			case 203 : //分类
				$viewType = Util_JsonKey::CATEGORY_DETAIL_VIEW;
				$param    = array(
						'url'      => '',
						'contentId'=> $v['contentId'],
						'gameId'   => '',
						'title'    => self::convertSting($v['title']),
				);
				break;
			case 204 : //活动
				list(, $activity) = Client_Service_Hd::getList(1, 1, array('id'=>$v['contentId']));
				$activity = $activity[0];
				$viewType = Util_JsonKey::ACTIVITY_DETAIL_VIEW;
				$param    = array(
						'url'      => '',
						'contentId'=> $v['contentId'],
						'gameId'   => empty($activity['status']) ? '' : $activity['game_id'],
						'title'    => self::convertSting($v['title']),
				);
				break;
			case 205 : //链接
				$viewType =  Util_JsonKey::LINK;
				$param    = array(
						'url'      => html_entity_decode($v['contentId']),
						'contentId'=> '',
						'gameId'   => '',
						'title'    => self::convertSting($v['title']),
				);
				break;
			case 206 : //礼包
				$viewType = Util_JsonKey::GIFT_DETAIL_VIEW;
				$giftInfo = self::getGiftInfo($v['contentId']);
				if (!$giftInfo) {
					Client_Service_PushMsg::close($v['id']);
					return null;
				}
				$param    = array(
						'url'      => '',
						'contentId'=> $v['contentId'],
						'gameId'   => $giftInfo['game_id'],
						'title'    => self::convertSting($giftInfo['name']),
				);
				break;
			case 207 : //文本信息
				$viewType = Util_JsonKey::HTML_VIEW;
				$param    = array(
						'url'      => '',
						'contentId'=>  '',
						'gameId'   => '',
						'htmlData' =>  Common::fillStyle($v['contentId']),
						'title'    => self::convertSting($v['title']),
				);
				break;
			default :
				$viewType = '';
				$param    = array();
				break;
		}		
		$t = array(
				'id'        => $v['id'],
				'type'      => self::geMsgType($v, $isPush),
				'viewType'  => $viewType,
				'title'		=> self::convertSting($v['title']),
				'content'   => self::convertSting($v['msg']),
				'param'     => $param,
				'timeStamp' => $v['create_time'],
				'startTime' => $v['start_time'],
				'source'    => $viewType,
		);
		return $t;	
	}
	
	private static function convertSting($content){
		$content = preg_replace("/&#44;/", ",", $content);
		$content = preg_replace("/&#39;/", "'", $content);
		$content = html_entity_decode($content, ENT_QUOTES);
		return $content;
	}
	
	private static function getGiftInfo($giftId){
		$time = Common::getTime();
		$giftInfo = Client_Service_Gift::getGiftBaseInfo($giftId);
    	if (!$giftInfo['status']) {
    		return null;
    	}
    	if ($giftInfo['effect_start_time'] > $time) {
    		return null;
    	}
    	if ($giftInfo['effect_end_time'] < $time) {
    		return null;
    	}
    	if (!$giftInfo['game_status']) {
    		return null;
    	}
		return $giftInfo;
	}
	
	private static function geMsgType($msg, $isPush = false){
		$msgType = "";
		if($isPush){
			$msgType = self::gePushMsgType($msg);
		} else {
			$msgType = self::$msgType[$msg['type']]['key'];
		}
		return $msgType;
	}
	
	private static function gePushMsgType($msg){
		$pushMsgType = "";
		switch (intval($msg['reciver_type']))
		{
			case 0 :
				$pushMsgType = Util_JsonKey::MSG_PUSH;
				break;
			case 1 :
				$pushMsgType = Util_JsonKey::MSG_FAVOR;
				break;
			case 2 :
				$pushMsgType = Util_JsonKey::MSG_INSTALLED;
				break;
			case 3 :
				$pushMsgType = Util_JsonKey::MSG_FAVOR;
				break;
		}
		return $pushMsgType;
	}
	
	/**
	 * 系统消息
	 * @param array $v
	 * @return array
	 */
	public static function genSysMsgOutput($v, $puuid, $uname, $version) {
		$webroot = Common::getWebRoot ();
		$msg = array();
		if($v['msgid'])  $msg  = Game_Service_Msg::getMsg($v['msgid']);
		if(!$v['msgid'])  {
			$msg = $v;
			$s  = Game_Service_Msg::getMap(array('msgid'=>$v['id']));
		}
		switch($v['type']) {
			case 101 : //任务完成消息
				$type = 'taskComplete';
				$viewType = 'MyATicketView';
				$source = 'aticket';
				$param    = array(
						'url'      => '',
						'title'    => '我的A券',
						'source'      => 'acertificate',
				);
				break;
			case 102 : //A券过期消息
				$type = 'ATickExpire';
				$viewType = 'MyATicketView';
				$source = 'aticket';
				$param    = array(
						'url'      => $webroot .'/client/task/myticket/?puuid='.$puuid.'&uname='.$uname,
						'title'    => 'A券过期',
						'source'      => 'acertificate',
				);
				break;
			case 103 : //A券赠送消息
				$type = 'ATickGive';
				$viewType = 'MyATicketView';
				$source = 'aticket';
				$param    = array(
						'url'      => $webroot .'/client/task/myticket/?puuid='.$puuid.'&uname='.$uname,
						'title'    => '我的A券',
						'source'      => 'acertificate',
				);
				break;
			case 107 : //积分赠送消息
				$type = 'PointGive';
				$viewType = 'MyPointView';
				$source = 'aticket';
				$param    = array(
						'url'      => '',
						'title'    => '我的积分',
						'source'      => '',
				);
				break;
			case 108 : //积分过期消息
				if (strnatcmp($version, '1.5.5') < 0) {
					$type = 'undefine';
				} else if (strnatcmp($version, '1.5.7') < 0) {
					$type = 'ATickExpire';
				} else {
					$type = 'expire';
				}
				
				$viewType = 'MyPointView';
				$source = 'point';
				$param    = array(
						'url'      => '',
						'title'    => '我的积分',
						'source'      => '',
				);
				break;			default :
				$viewType = '';
				$param    = array();
				break;
		}
		$t = array(
				'id'        => $msg['id'],
				'type'      => $type,
				'viewType'  => $viewType,
				'title'		=> self::convertSting($msg['title']),
				'content'   => self::convertSting($msg['msg']),
				'param'     => $param,
				'timeStamp' => $msg['update_time'],
				'source'     => $source,
		);
		return $t;
	}
	
	public static function markRead($msgid,$uid,$imei) {
		$idArr = explode('|',rtrim($msgid,'|'));
		foreach($idArr as $id) {
			$msgInfo    = self::_getDao()->get(intval($id));
			if(empty($msgInfo)) {
				continue;
			}
			switch($msgInfo['totype']) {
				case 0 :
					$msgMapInfo = self::_getDao()->checkMap($msgid,$uid,$imei);
					$data = array(
							'msgid'   => $id,
							'uid'     => empty($uid)  ? '-1' : $uid,
							'imei'    => empty($imei) ? '-1' : $imei,
							'status'  => 1,
							'top_type'  => $msgInfo['top_type'],
							'type'  => $msgInfo['type'],
							'create_time'    => time(),
							'read_time'    => time(),
					);
					if(empty($msgMapInfo)) {
						$res = self::addMap($data);	
					} else {
						$res = true;
					}											
					break;
				case 1 :
					if(!empty($uid)) {
						$condition = array(
								'uid'   => $uid,
								'msgid' => $id
						);
						$ret = self::_getDao()->update(array('status'=>1), $id);
						$res = self::_getMapDao()->updateBy(array('status'=>1,'read_time'=>time()),$condition);
					}					
					break;
				case 2 :
					if(!empty($imei)) {
						$condition = array(
								'imei'  => $imei,
								'msgid' => $id
						);
						$ret = self::_getDao()->update(array('status'=>1), $id);
						$res = self::_getMapDao()->updateBy(array('status'=>1,'read_time'=>time()),$condition);
					}
					break;
			}	
		}		
		return $res;
	}	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();	
		if(isset($data['id']))          $tmp['id']        = $data['id'];
		if(isset($data['type']))        $tmp['type']        = $data['type'];
		if(isset($data['sendInput']))   $tmp['sendInput']   = $data['sendInput'];
		if(isset($data['top_type']))    $tmp['top_type']    = $data['top_type'];		
		if(isset($data['totype']))      $tmp['totype']      = $data['totype'];
		if(isset($data['title']))       $tmp['title']       = $data['title'];
		if(isset($data['msg']))         $tmp['msg']         = $data['msg'];
		if(isset($data['start_time']))  $tmp['start_time']  = $data['start_time'];
		if(isset($data['end_time']))    $tmp['end_time']    = $data['end_time'];
		if(isset($data['status']))      $tmp['status']      = $data['status'];		
		if(isset($data['operate_status']))      $tmp['operate_status']      = $data['operate_status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['last_author'])) $tmp['last_author'] = $data['last_author'];	
		if(isset($data['contentId']))   $tmp['contentId']   = $data['contentId'];			
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getSearchList($page = 1, $limit = 10, $params = array(),$orderby = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		
		$sqlWhere = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderby);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getMapList($page = 1, $limit = 10, $params = array(),$orderby = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getMapDao()->getList($start, $limit, $params, $orderby);
		$total = self::_getMapDao()->count($params);
		return array($total, $ret);
	}
	
	public static function updateMapBy($data,$param) {
		return self::_getMapDao()->updateBy($data,$param);
	}
	
	public static function getUnReadCount($params) {
		return self::_getMapDao()->count($params);
	}
	
	public static function getMsgUnReadCount($params) {
		return self::_getDao()->count($params);
	}
	
	/**
	 * @param unknown_type $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsMaps($params,$orderBy) {
		if (!is_array($params)) return false;
		return self::_getMapDao()->getsBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getMap($params,$orderBy = array('create_time'=>'DESC','id'=>'DESC')) {
		return self::_getMapDao()->getBy($params,$orderBy);
	}
	
	/**
	 * 
	 * @return Game_Dao_Msg
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_Msg");
	}
	
	/**
	 *
	 * @return Game_Dao_MsgMap
	 */
	private static function _getMapDao() {
		return Common::getDao('Game_Dao_MsgMap');
	}
}
