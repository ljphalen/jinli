<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MsgController extends Api_BaseController {

	public $perpage = 10;	
	
	/**
	 * 获取我的消息新接口（包括系统消息和运营消息）
	 */
	public function getMyMsgAction() {
		$items  = $params = $search = array();
		$input = $this->getInput(array('timeStamp', 'puuid', 'imei', 'uname', 'version', 'systemMsgTimeStamp'));	
		
		$this->_checkUser($input['puuid'], $input['imei'], $input['uname']);
		if ($input['timeStamp'] == null || $input['systemMsgTimeStamp'] == null) {
			$this->localOutput(-1, '参数错误', array());
		}
		
		$yunyingMsgTimeStamp = intval($input['timeStamp']);
		$systemMsgTimeStamp = intval($input['systemMsgTimeStamp']);
		
		$currenTime = Common::getTime();
		$data['serverTime'] = $currenTime;
		
		$rs = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($input['puuid']); 
		$allMsgReadTime = $rs->hGet($cacheKey,'allMsgRead');
		$rs->hSet($cacheKey,'allMsgRead', $currenTime);
		if ($allMsgReadTime > $yunyingMsgTimeStamp) {
			$yunyingMsgTimeStamp = $allMsgReadTime;
		}
		if ($allMsgReadTime > $systemMsgTimeStamp) {
			$systemMsgTimeStamp = $allMsgReadTime;
		}
		$data['lastTime'] = $allMsgReadTime;
		
		//系统消息
		$params['top_type'] = 100;
		$params['uid'] = $input['puuid'];
		$params['expire_status'] = 0;
		$params['type'] = array('IN', array(101,102,103,107,108,109));
		
		$maps = Game_Service_Msg::getsMaps($params, array('create_time'=>'DESC','id'=>'DESC'));
		foreach($maps as $k=>$v){
		    $info  = Game_Service_Msg::getMsg($v['msgid']);
		    if($info['create_time'] <= $currenTime && $info['create_time'] >= $allMsgReadTime){
		        $systemMsgs[] = $info;
		    }
		}
		
		if ($systemMsgs) {
			$items[] = Game_Service_Msg::genSysMsgOutput($systemMsgs[0], $input['puuid'], $input['uname'], $input['version']);
		}
		
	    //所有账号热门消息
		$search['totype'] = '0';
		$search['top_type'] = 200;
		$search['type'] = array('IN', array(201,202,203,204,205,206,207));
		$search['start_time'] = array('<=', $currenTime);
		$search['operate_status'] = '1';
		$search['start_time'][0] = array('>=', $yunyingMsgTimeStamp);
		$search['start_time'][1] = array('<=', $currenTime);
		$search['end_time'] = array('>=', $currenTime);
					
		$allYunyingMsgs = Game_Service_Msg::getsByMsg($search,array('start_time'=>'DESC','id'=>'DESC'));
		if ($allYunyingMsgs && count($systemMsgs) == 0) {
			$items[] = Game_Service_Msg::genMsgOutput($allYunyingMsgs[0]);
		}
				
		//指定账号热门消息
		unset($params);
		$params['top_type'] = 200;
		$params['uid'] = $input['puuid'];
		$params['type'] = array('IN', array(201,202,203,204,205,206,207));
		$params['create_time'][0] = array('>=', $yunyingMsgTimeStamp);
		$params['create_time'][1] = array('<=', $currenTime);
		
	    $yunyingMsgMaps = Game_Service_Msg::getsMaps($params, array('create_time'=>'DESC','id'=>'DESC'));
	    foreach($yunyingMsgMaps as $key=>$value){
	    	$ids[] = $value['msgid'];
	    }
	    if ($yunyingMsgMaps) {
		    unset($search);
		    $search['top_type'] = 200;
		    $search['type'] = array('IN', array(201,202,203,204,205,206,207));
		    $search['start_time'] = array('<=', $currenTime);
		    $search['operate_status'] = '1';
		    $search['start_time'][0] = array('>=', $yunyingMsgTimeStamp);
		    $search['start_time'][1] = array('<=', $currenTime);
		    $search['end_time'] = array('>=', $currenTime);
	    	$search['id'] =array('IN',$ids);
	    	
	    	$yunyingMsgs = Game_Service_Msg::getsByMsg($search, array('start_time'=>'DESC','id'=>'DESC'));
	    	if ($yunyingMsgs && count($systemMsgs) == 0 && count($allYunyingMsgs) == 0) {
	    		$msg = Game_Service_Msg::getNewMsg(array('id'=>$yunyingMsgs[0]['id']),array('start_time'=>'DESC','id'=>'DESC'));
	    		$items[] = Game_Service_Msg::genMsgOutput($msg);
	    	}
	    }
		
	    $data['unreadCount'] = count($systemMsgs) + count($allYunyingMsgs) + count($yunyingMsgs);
		$data['items'] = $items;
		$this->localOutput(0,'',$data);
	}
	
	/**
	 * 获取定向消息（包括已安装和已关注消息）
	 */
	public function getTargetMsgAction() {
		$data  = $search = $msgs = $item = $pushMsgs = array();
		$input = $this->getInput(array('timeStamp','puuid','imei','uname','version','gameIds','appList'));
		$currentTime = floor(Common::getTime()/60)*60;
	
		$search['status'] = Client_Service_PushMsg::STATE_OPEN;
		$search['end_time'] = array('>=', $currentTime);
		if($input[Util_JsonKey::TIME_STAMP]) {
			$search['start_time'][0] = array('<=', $currentTime);
			$search['start_time'][1] = array('>=', intval($input[Util_JsonKey::TIME_STAMP]));
		} else {
			$search['start_time'] = array('<=', $currentTime);
		}
	
		$specifiedRecieverPushMsg = $this->getSpecifiedRecieverPushMsg($search, $input['gameIds'], $input['puuid'], $input['appList']);
		$pushMsgs = $this->sortMsgs($specifiedRecieverPushMsg);
	
		$data['serverTime'] = $currentTime;
		if (!$pushMsgs) {
			$data['items'] = array();
			$this->localOutput(0,'',$data);
		}
	
		foreach($pushMsgs as $key => $value) {
			$item = Game_Service_Msg::genMsgOutput($value, true);
			if (!$item) {
				continue;
			}
			if (strnatcmp($item['type'], Util_JsonKey::MSG_FAVOR) == 0 && !$input['puuid']) {
				$item['type'] = Util_JsonKey::MSG_INSTALLED;
			}
			$msgs[] = $item;
		}
	
		$data['items'] = $msgs;
		$this->localOutput(0,'',$data);
	}
	
	/**
	 * 获取Push消息
	 */
	public function getPushMsgAction() {
		$data  = $search = $msgs = $item = $pushMsgs = array();
		$input = $this->getInput(array('timeStamp','puuid','imei','uname','version','gameIds','appList'));
		$currentTime = floor(Common::getTime()/60)*60;

		$search['status'] = Client_Service_PushMsg::STATE_OPEN;
		$search['end_time'] = array('>=', $currentTime);
		$search['reciver_type'] = Client_Service_PushMsg::PushMsg_RECIVER_ALL_USER;
		if($input[Util_JsonKey::TIME_STAMP]) {
			$search['start_time'][0] = array('<=', $currentTime);
			$search['start_time'][1] = array('>=', intval($input[Util_JsonKey::TIME_STAMP]));
		} else {
			$search['start_time'] = array('<=', $currentTime);
		}

		$pushMsgs = $this->getPushMsgs($search);
		$data['serverTime'] = $currentTime;
		if (!$pushMsgs) {
			$data['items'] = array();
			$this->localOutput(0,'',$data);
		}
		
		foreach($pushMsgs as $key => $value) {
			$item = Game_Service_Msg::genMsgOutput($value, true);
			if (!$item) {
				continue;
			}
			if (strnatcmp($input['version'], '1.5.7') < 0 && strnatcmp($item['viewType'], 'HtmlView') == 0) {
				continue;
			}
			$msgs[] = $item;
		}	
		
		$data['items'] = $msgs;
		$this->localOutput(0,'',$data);
	}
	
	
	/**
	 * 添加消息读取记录
	 */
	private function recordReads($input, $msgids, $msg) {
		if (empty($msgids)) {
			return;
		}
		$searchLog = array();
		$searchLog['msgid'] = array('IN',$msgids);
		$searchLog['imei'] = $input['imei'];
		$orderBy = array('create_time'=>'DESC','id'=>'DESC');
		list(,$searchLogResult) = Client_Service_PushMsgLog::getsBy($searchLog, $orderBy);
		
		$logsId = Common::resetKey($searchLogResult,'msgid');
		$logsId = array_unique(array_keys($logsId));
		
		foreach($msg as $key => $value) {
			if(!in_array($value['id'], $logsId)){
				$reads[] = $this->genReadParam($input, $value);
			}
		}
		
		if($reads) {
			Client_Service_PushMsgLog::mutiInsert($reads);
		}
	}
	
	private function getSpecifiedRecieverPushMsg($search, $installedGames, $uuid, $appLists) {
		if ($appLists) {
			$installedGameIds = $this->extractGameIdsFromPackages($appLists);
		} else {
			$installedGameIds = $this->extractGameIds($installedGames);
		}
		$attentionedGameIds = $this->getAttentionGameIds($uuid);
		
		$installedMsg = $attentionedMsgs = array();
		if (!empty($installedGameIds)) {
			$recieverArr = array(
					Client_Service_PushMsg::PushMsg_RECIVER_ATTENTION_INSTALL,
					Client_Service_PushMsg::PushMsg_RECIVER_INSTALL,
			);
			$search['game_id'] = array('IN', $installedGameIds);
			$search['reciver_type'] = array('IN', $recieverArr);
			$installedMsg = $this->getPushMsgs($search);
		}
		
		if (!empty($attentionedGameIds)) {
			$recieverArr = array(
					Client_Service_PushMsg::PushMsg_RECIVER_ATTENTION_INSTALL,
					Client_Service_PushMsg::PushMsg_RECIVER_ATTENTION,
			);
			$search['game_id'] = array('IN', $attentionedGameIds);
			$search['reciver_type'] = array('IN', $recieverArr);
			$attentionedMsgs = $this->getPushMsgs($search);
		}
		
		$msgs = array_merge($installedMsg, $attentionedMsgs);
		if (empty($msgs)) {
			return array();
		}
		
		return $msgs;
	}
	
	private function extractGameIdsFromPackages($packagesInput) {
		$pkgNames = explode('|', $packagesInput);
		$gameIds = array();
		foreach ($pkgNames as $packageKey => $gamePackage) {
			$game = Resource_Service_Games::getBy(array('package'=>$gamePackage));
			$gameInfo = Resource_Service_GameData::getBasicInfo($game['id']);
    		if (empty($gameInfo)) {
    			continue;
    		}
    		$gameIds[] = $game['id'];
		}
	
		return array_unique($gameIds);
	}
	
	private function extractGameIds($installedGames) {
		if(!$installedGames) {
			 return array();
		}

		$gameIds = array();
		
		$ids = explode('|', $installedGames);
		
		foreach($ids as $key => $id) {
			$id = intval($id);
			if ($id > 0) {
				$gameIds[] = $id;
			}
		}

		return array_unique($gameIds);
	}
	
	private function getAttentionGameIds($uuid) {
		if(!$uuid){
			return array();
		}

		$query = array();
		$query['uuid'] = $uuid;
		$attentionGames = Client_Service_Attention::getsBy($query);

		$ids = array();
		
		foreach($attentionGames as $key => $value) {
			$ids[] = $value['game_id'];
		}

		return $ids;
	}

	private function getPushMsgs($search) {
		$orderBy = array('create_time'=>'DESC','id'=>'DESC');
		list(,$result)  = Client_Service_PushMsg::getList(1, 3, $search, $orderBy);
		return $result;
	}
	
	private function sortMsgs($msg) {
		if(!$msg) {
			return false;
		}

		$msg = Common::resetKey($msg,'id');
	    $sortMsg = array();
	    krsort($msg);
	    
	    $i = 0;
	    foreach($msg as $key=>$value){
	 	  if($i <= 2 ){
	 		 $sortMsg[] = $value;
	 	  }
	 	  $i++;
	    }
	    
        return $sortMsg;
	}
	
	/**
	 * 生成读取消息数据结构
	 */
	private function genReadParam($data, $msg) {
		$read = array(
				'id'        => '',
				'type'        => $msg['type'],
				'msgid'       => $msg['id'],
				'uuid'        => $data['puuid'],
				'imei'        => $data['imei'],
				'create_time' =>Common::getTime()
		);
		return $read;
	}
	
    /**
     * 获取最新的一条消息
     */
	public function getLatestMsgAction() {
		$sign  = 'GioneeGameHall';		
		$data  = $params = $search = array();
		$input = $this->getInput(array('timeStamp','puuid','imei','uname','version'));	
				
		//检查用户是否在线
		$this->_checkUser($input['puuid'], $input['imei'], $input['uname']);
		
		$data['serverTime'] = time();
		$data['unreadCount']= $this->_getCountData($input['puuid'],$input['version'],$input['timeStamp']);
		//先查找系统未读消息，如果没有再去查找热门未读消息
		$params['top_type'] = 100;
		$params['uid'] = $input['puuid'];
		$params['status'] = 0;
		$params['expire_status'] = 0;
		if((strnatcmp($input['version'], '1.5.5') < 0)){
			$params['type'] = array('IN', array(101,102,103));
		} else {
			$params['type'] = array('IN', array(101,102,103,107,108));
		}
		
		$rs = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($input['puuid']) ;            //获取用户的uuid
		$allMsgReadTime = $rs->hGet($cacheKey,'allMsgRead');  //获取全部读取的时间戳
		
		if($allMsgReadTime) $params['create_time'] = array('>=', $allMsgReadTime);
		if($input['timeStamp']) {
			unset($params['create_time']);
			$params['create_time'][0] = array('>=', intval($input['timeStamp']));
			$params['create_time'][1] = array('<=', Common::getTime());
			if($allMsgReadTime) {
				if( intval($input['timeStamp']) < $allMsgReadTime )  $params['create_time'][0] = array('>=', $allMsgReadTime);
			}
		}
		
		$msg  = Game_Service_Msg::getMap($params, array('create_time'=>'DESC','id'=>'DESC'));
		if($msg) {
			$msg  = Game_Service_Msg::getNewMsg(array('id'=>$msg['msgid']),array('start_time'=>'DESC','id'=>'DESC'));
		   	$res = Game_Service_Msg::genSysMsgOutput($msg, $input['puuid'], $input['uname'], $input['version']);
		   	$data['items'] = array($res);
		   	if($msg['type'] == 102){
			   	//将该过期消息设置为已读
			   	if(!$msg['status']) $this->_setRead($msg['msgid']);
		   	}
		   	$this->localOutput(0,'',$data, $sign);
    	 }

		if(!$msg){
			//热门未读
			$search['totype'] = '0'; //所有账号
			$search['top_type'] = 200;
			$search['type'] = array('IN', array(201,202,203,204,205,206,207));
			$search['start_time'] = array('<=', Common::getTime());
			$search['operate_status'] = '1';
			
			if($input['timeStamp']) {
				unset($search['start_time']);
				$search['start_time'][0] = array('>=', intval($input['timeStamp']));
				$search['start_time'][1] = array('<=', Common::getTime());
				if($allMsgReadTime) {
					if( intval($input['timeStamp']) < $allMsgReadTime )  $search['start_time'][0] = array('>=', $allMsgReadTime);
				}
			} else {
				if($allMsgReadTime){
				    unset($search['start_time']);
					$search['start_time'][0] = array('>=', $allMsgReadTime);
					$search['start_time'][1] = array('<=', Common::getTime());
				}
			}
			$search['end_time'] = array('>=', Common::getTime());
						
			$msg  = Game_Service_Msg::getNewMsg($search,array('start_time'=>'DESC','id'=>'DESC'));
			
			if(!$msg) {     
				//热门所有账号消息没有的话，查找热门指定未读
				//先查找指定账号热门未读消息
				unset($params);
				$params['top_type'] = 200;
				$params['uid'] = $input['puuid'];
				$params['status'] = 0;
				$params['type'] = array('IN', array(201,202,203,204,205,206,207));
				
				if($allMsgReadTime) $params['create_time'] = array('>=', $allMsgReadTime);
				if($input['timeStamp']) {
					unset($params['create_time']);
					$params['create_time'][0] = array('>=', intval($input['timeStamp']));
					$params['create_time'][1] = array('<=', Common::getTime());
					if($allMsgReadTime) {
						if( intval($input['timeStamp']) < $allMsgReadTime )  $params['create_time'][0] = array('>=', $allMsgReadTime);
					}
				}
				
			    $map  = Game_Service_Msg::getMap($params, array('create_time'=>'DESC','id'=>'DESC'));
				if(!$map) {
					$data['items'] = array();
					$this->localOutput(0,'',$data, $sign);
				}
				
				$msg  = Game_Service_Msg::getNewMsg(array('id'=>$map['msgid']),array('start_time'=>'DESC','id'=>'DESC'));
				if($msg['operate_status'] == 0 || ($msg['start_time'] > Common::getTime() || $msg['end_time'] < Common::getTime()) ){
					$data['items'] = array();
					$this->localOutput(0,'',$data, $sign);
				}
				
				$res = Game_Service_Msg::genMsgOutput($msg);
				if (!$res) {
					$data['items'] = array();
				}
				$data['items'] = array($res);
				$this->localOutput(0,'',$data, $sign);
			}
			
			$res = Game_Service_Msg::genMsgOutput($msg);
			if (!$res) {
				$data['items'] = array();
			}
			$data['items'] = array($res);
			$this->localOutput(0,'',$data, $sign);

		}
				
	}
	
	/**
	 * 系统消息列表
	 */
	public  function getSysMsgByTypeAction(){
		$input = $this->getInput(array('puuid','imei','t','uname','version'));
		$page = intval($this->getInput('page'));
		$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);
		$input['t'] = 100;
		$this->_getApiData($input,$page) ;
	}
	
	/**
	 * 热门消息列表
	 */
	public  function getHotMsgByTypeAction(){
		$input = $this->getInput(array('puuid','imei','t','uname','version'));
		$page = intval($this->getInput('page'));
		$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);
		$input['t'] = 200;
		$this->_getApiData($input,$page) ;
	}
	
	/**
	 * 消息tab
	 */
	public function getMsgTabAction() {
		$res = $data = array();
		$sign = 'GioneeGameHall';	
		$tabArr  = Game_Service_Msg::$msgTopType;
		$webroot = Common::getWebRoot();		
		$input   = $this->getInput(array('puuid','imei','uname','version'));		

		$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);
		foreach($tabArr as $k => $v) {	
			if($k == 0) $url = 	$webroot . '/Api/msg/getMyMsgList/?page=1&puuid='.$input['puuid'].'&imei='.$input['imei'].'&uname='.$input['uname'].'&version='.$input['version'];	
			if($k == 100) $url = 	$webroot . '/Api/msg/getSysMsgByType/?page=1&puuid='.$input['puuid'].'&imei='.$input['imei'].'&uname='.$input['uname'].'&version='.$input['version'];
			if($k == 200) $url = 	$webroot . '/Api/msg/getHotMsgByType/?page=1&puuid='.$input['puuid'].'&imei='.$input['imei'].'&uname='.$input['uname'].'&version='.$input['version'];
			$res[] = array(
				'title'    =>  $v['txt'],
				'source'   =>  $v['key'],
				'viewType' =>  'MessageView',
				'url' 	   =>  html_entity_decode($url)
			);		
		}	
		$data['items'] = $res;
		$this->localOutput(0,'',$data,$sign);		
	}
	
	/**
	 * 系统，热门消息列表数据组装
	 * @param array $input
	 * @param int $page
	 * return json
	 */
	private function _getApiData($input,$page) {
		$data  = array();
		$sign  = 'GioneeGameHall';
		if ($page < 1) $page = 1;
		$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);
	
		if($input['t'] == 0 || !in_array($input['t'],array_keys(Game_Service_Msg::$msgTopType))) {
			$this->localOutput(-1,'类型错误',$data,$sign);
		}
		 
		$params = $list = $msgs = array();
		if(intval($input['t']) == 100) {                 //系统消息
				$params['top_type'] = 100;
				$params['expire_status'] = 0;
				if((strnatcmp($input['version'], '1.5.5') < 0)){
					$params['type'] = array('IN', array(101,102,103));
				} else {
					$params['type'] = array('IN', array(101,102,103,107,108,109));
				}
				$params['uid'] = $input['puuid'];
				list($total, $mapgs) = Game_Service_Msg::getMapList($page, $this->perpage, $params, array('create_time'=>'DESC','id'=>'DESC'));
				foreach($mapgs as $k=>$v){
				     $msgs[]  = Game_Service_Msg::getNewMsg(array('id'=>$v['msgid']),array('start_time'=>'DESC','id'=>'DESC'));
				}
				//将该过期消息设置为已读
				if($page == 1) $this->_batchSetRead($input['puuid']);
		} else if(intval($input['t']) == 200) {           //热门消息
				$params['top_type'] = 200;
				$params['type'] = array('IN', array(201,202,203,204,205,206,207));
				$params['start_time'] = array('<=', Common::getTime());
				$params['end_time'] = array('>=', Common::getTime());
				$params['totype'] = '0'; //所有账号
				$params['operate_status'] = '1';
				
				//查找指定账号
				$maps  = Game_Service_Msg::getsMaps(array('uid'=>$input['puuid'],'top_type'=>200),array('create_time'=>'DESC','id'=>'DESC'));
				foreach($maps as $k=>$v){
					$info  = Game_Service_Msg::getMsg($v['msgid']);
					if($info['operate_status'] && $info['start_time'] <= Common::getTime() && $info['end_time'] >= Common::getTime()){
					  if($info['totype'] == 1) $tmp[] = $v['msgid'];
					}
				}
				
				if($tmp) {
					$params['ids'] = array('IN', $tmp);
				}
				list($total, $msgs) = Game_Service_Msg::getSearchList($page, $this->perpage, $params, array('start_time'=>'DESC','id'=>'DESC'));
		}
	
		foreach($msgs as $key=>$value){
			if($value['top_type'] == 100) {
				$list[] = Game_Service_Msg::genSysMsgOutput($value, $input['puuid'], $input['uname'], $input['version']);
			}
		   	if($value['top_type'] == 200) {
         	  	$item = Game_Service_Msg::genMsgOutput($value);
         		if ($item) {
 	        		$list[] = $item;
          		}
         	}
		}
	
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$data['hasnext'] = $hasnext;
		$data['curpage'] = $page;
		$data['list']    = $list;
		$this->localOutput(0,'',$data,$sign);
	}
	
	/**
	 * 获取我的消息列表
	 */
	public function getMyMsgListAction() {
		$sign  = 'GioneeGameHall';
		$input = $this->getInput(array('puuid','imei','uname','version'));	
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);

		if(empty($input['puuid']) && empty($input['imei'])) {
			$this->localOutput(-1,'参数错误',array(),$sign);
		}
		
		$rs = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($input['puuid']) ;            //获取用户的uuid
		if($page == 1){
			$rs->hSet($cacheKey,'allMsgRead',time());         //本次请求时间戳
		}
		
		$params = $search = $tmp = $data = array();
		
         //查找指定账号
         $maps  = Game_Service_Msg::getsMaps(array('uid'=>$input['puuid']),array('create_time'=>'DESC','id'=>'DESC'));
         foreach($maps as $k=>$v){
         	    if($v['expire_status'] == 1)  continue;
	         	if((strnatcmp($input['version'], '1.5.5') < 0)){
	         		if($v['type'] == 107){                     //1.5.5之前的版本不显示积分赠送消息
	         			continue;
	         		} else if($v['top_type'] == 200){         //判断运营消息是否开启
	         			$checkMsg  = Game_Service_Msg::getMsg($v['msgid']);
	         			if($checkMsg['operate_status'] && $checkMsg['start_time'] <= Common::getTime() && $checkMsg['end_time'] >= Common::getTime()) $tmp[] = $v['msgid'];
	         		} else {
	         			$tmp[] = $v['msgid'];
	         		}
	         	}  else {
	         		if($v['top_type'] == 200){               //判断运营消息是否开启
	         			$checkMsg  = Game_Service_Msg::getMsg($v['msgid']);
	         			if($checkMsg['operate_status'] && $checkMsg['start_time'] <= Common::getTime() && $checkMsg['end_time'] >= Common::getTime()) $tmp[] = $v['msgid'];
	         		} else {
	         			$tmp[] = $v['msgid'];
	         		}
	         	}
         		
         }
         
         if($tmp) {
         	$search['ids'] = array('IN', $tmp);
         }

         $search['start_time'] = array('<=', Common::getTime());
         $search['end_time'] = array('>=', Common::getTime());
         $search['totype'] = '0'; //所有账号
         $search['operate_status'] = '1';
         	
         list($total, $msgs) = Game_Service_Msg::getSearchList($page, $this->perpage, $search, array('start_time'=>'DESC','id'=>'DESC'));
         foreach($msgs as $key=>$value){
         	if($value['top_type'] == 100) {
         		$list[] = Game_Service_Msg::genSysMsgOutput($value, $input['puuid'], $input['uname'], $input['version']);
         	}
         	if($value['top_type'] == 200) {
         	  	$item = Game_Service_Msg::genMsgOutput($value);
         		if ($item) {
 	        		$list[] = $item;
          		}
         	}
         }
         $hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		 $data['hasnext'] = $hasnext;
		 $data['curpage'] = $page;
		 $data['list']    = $list;				
		 
		 //将该过期消息设置为已读
		 if ($page == 1) $this->_batchSetRead($input['puuid']);
		$this->localOutput(0,'',$data,$sign);
	}
	
	/**
	 * 获取未读消息总数
	 * @param string $uid
	 * @return int
	 */
	private function _getCountData($puuid, $version, $timeStamp) {
		if(!$puuid)  return false;
		
		$rs = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($puuid); 
		$allMsgReadTime = $rs->hGet($cacheKey,'allMsgRead');  //获取全部读取的时间戳
		
		$params = array();
		$params['uid'] = $puuid;
		$params['status'] = 0;
		$params['expire_status'] =0;
		$params['top_type'] = 100;
		if((strnatcmp($version, '1.5.5') < 0)){
			$params['type'] = array('IN', array(101,102,103));
		} else {
			$params['type'] = array('IN', array(101,102,103,107,108));
		}
		if($allMsgReadTime) $params['create_time'] = array('>=', $allMsgReadTime);
		if($timeStamp) {
			unset($params['create_time']);
			$params['create_time'][0] = array('>=', intval($timeStamp));
			$params['create_time'][1] = array('<=', Common::getTime());
			if($allMsgReadTime) {
				if( intval($timeStamp) < $allMsgReadTime )  $params['create_time'][0] = array('>=', $allMsgReadTime);
			}
		}
		
		//系统未读消息总数
		$sysUnreadCnt   = Game_Service_Msg::getUnReadCount($params);
		$params = $search = array();
		$params['start_time'] = array('<=', Common::getTime());
		$search['start_time'] = array('<=', Common::getTime());
		
		if($allMsgReadTime) {
			$params['start_time'][0] = array('<=', Common::getTime());
			$params['start_time'][1] = array('>=', $allMsgReadTime);
			
			$search['start_time'][0] = array('<=', Common::getTime());
			$search['start_time'][1] = array('>=', $allMsgReadTime);
		}
		
		$params['end_time'] = array('>=', Common::getTime());
		$search['end_time'] = array('>=', Common::getTime());
		
		$params['top_type'] = 200;
		$params['type'] = $this->_getMsgType($version);
		$params['totype'] = '0'; //所有账号
		$params['operate_status'] = '1';
		
		//热门所有账号未读消息总数
		$tmp = array();
		//获取截止上一时刻有效的未读数
		$mgs   = Game_Service_Msg::getsByMsg($params,array('start_time'=>'DESC'));
		$num = 0;
		foreach($mgs as $key=>$value){
			$ids[] = $value['id'];
			$num++;
		}
		
		if($ids){
			$mapSearch = $maps = array();
			$mapSearch['msgid'] = array('IN',$ids);
			$mapSearch['status'] = 1;
			$mapSearch['uid'] = $puuid;
			$maps = Game_Service_Msg::getsMaps($mapSearch,array('create_time'=>'DESC','id'=>'DESC'));
			$num -= count($maps);
		}
		
		//热门指定账号未读消息总数
		$params = array();
	    $params['uid'] = $puuid;
		$params['top_type'] = 200;
		$params['type'] = $this->_getMsgType($version);
		$params['status'] = 0;
		$maps  = Game_Service_Msg::getsMaps($params,array('create_time'=>'DESC','id'=>'DESC'));
		foreach($maps as $key=>$value){
			$mg   = Game_Service_Msg::getMsg($value['msgid']);
			if($mg['totype'] == 1 && $mg['operate_status'] && $mg['start_time'] <= Common::getTime() && $mg['end_time'] >= Common::getTime()) {
				$tmp[] = $value['msgid'];
			}
 		}
 		
 		if($tmp) {
 			$search['id'] = array('IN', $tmp);
 		    $hotUnreadCnt   = Game_Service_Msg::getMsgUnReadCount($search);
 		}
		return intval($sysUnreadCnt + $num + $hotUnreadCnt);
	}
	
	private function _getMsgType($version) {
	   if((strnatcmp($version, '1.5.7') < 0)){
			$type = array('IN', array(201,202,203,204,205,206));
		} else {
			$type = array('IN', array(201,202,203,204,205,206,207));
		}
		return $type;
	}
	
	/**
	 * 消息已读上报数据
	 */
    public function markReadAction() {    
    	$data  = array();
    	$sign  = 'GioneeGameHall';
    	$input = $this->getInput(array('id','puuid','imei','uname'));     	
    	
    	$this->_checkUser($input['puuid'], $input['imei'],$input['uname']);
    	
    	if(empty($input['id'])) {
    		$this->localOutput(-1,'参数错误',$data,$sign);
    	}
    	
    	$res = Game_Service_Msg::markRead($input['id'],$input['puuid'],$input['imei']);
    	
    	$data['result'] = $res ? 'success' : 'fail';
    	$this->localOutput(0,'',$data,$sign);
    }
    
    public function _checkUser($uid,$imei,$uname) {
    	$data = array();
    	$sign = 'GioneeGameHall';
   		if(empty($uid) && empty($uname)) {
   			$this->localOutput(-1,'参数错误',$data,$sign);
   		}    	
    	if(!empty($uid)) {
			$res = Account_Service_User::getUser(array('uuid'=>$uid));
			if(empty($res)) {
				$this->localOutput(-1,'用户不存在',$data,$sign);
			}
    	} else {
	    	$res = Account_Service_User::getUser(array('imei'=>$imei));
	    	if(empty($res)) {
	    		$this->localOutput(-1,'用户不存在',$data,$sign);
	    	}	    	
    	}
    	//检查在线
    	$online = Account_Service_User::checkOnline($uname,$imei);
    	if(!$online) {
    		exit(json_encode(array(
    			'code'    => 0,
    			'success' => -1,
    			'msg' => '用户登录过期',
    			'sign' => $sign,
    			'data' => array()
    		)));
    	} 
    }
    
    //单条更新已读
    private static function _setRead($msgid) {
    	Game_Service_Msg::updateBy(array('status'=>1), array('id'=>$msgid));
    	Game_Service_Msg::updateMapBy(array('status'=>1,'read_time'=>time()), array('msgid'=>$msgid));
    }
    
    //批量更新过期已读消息
    private static function _batchSetRead($puuid) {
    	$rs = Cache_Factory::getCache();
		$cacheKey = Util_CacheKey::getUserInfoKey($puuid); 
    	$allMsgReadTime = $rs->hGet($cacheKey,'allMsgRead');  //获取全部读取的时间戳
    	
    	$params = array();
    	$params['uid'] = $puuid;
    	$params['status'] = 0;
    	$params['expire_status'] = 0;
    	$params['top_type'] = 100;
    	$params['type'] = 102;
    	if($allMsgReadTime) $params['create_time'] = array('<', $allMsgReadTime);
    	$msgs = Game_Service_Msg::getsMaps($params, array('create_time'=>'DESC','id'=>'DESC'));
    	if($msgs){
    		foreach($msgs as $key=>$value){
    		  Game_Service_Msg::updateMapBy(array('status'=>1,'read_time'=>time()), array('msgid'=>$value['msgid']));
    	      Game_Service_Msg::updateBy(array('status'=>1), array('id'=>$value['msgid']));
    		}
    	}
    	
    	
    }
}
