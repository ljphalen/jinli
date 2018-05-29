<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GiftController extends Api_BaseController {
	
	public $perpage = 10;
	
	
	/**
	 * 旧版本的礼包列表 
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		//1.5.1 根据游戏id查找在线，正在进行的礼包
		$game_id = $this->getInput('id');
		$isDetail = $this->getInput('isDetail');
		$uname = $this->getInput('uname');
		$sp = $this->getInput('sp');
		
		if ($page < 1) $page = 1;
		
		$spArr = Common::parseSp($sp);
		$imei = $spArr['imei'];
		$imeicrc = crc32(trim($imei));
		$version = $spArr['game_ver'];
		

		//1.5.0 账号加入
		$online = false;
		if($uname) $online = Account_Service_User::checkOnline($uname, $imei);
		//礼包列表
		$parmas['status'] = 1 ;
		$parmas['game_status'] = 1;
		$parmas['effect_start_time'] = array('<=', Common::getTime());
		$parmas['effect_end_time'] = array('>=', Common::getTime());
		if((strnatcmp($version, '1.5.1') >= 0) && $isDetail == 1) $parmas['game_id'] = $game_id;
		list($total, $giftsList) = Client_Service_Gift::getList($page, $this->perpage, $parmas);
		
		$webroot = Common::getWebRoot(); 
		$temp = array();
		$intersrc = ($intersrc) ? $intersrc : 'olg_libao';
		foreach($giftsList as $key=>$value) {
			//1.5 之前兼容
			if((strnatcmp($version, '1.5.0') < 0) && ($imei && $imei != "FD34645D0CF3A18C9FC4E2C49F11C510")){
				$logs = Client_Service_Giftlog::getByImeiGiftId($imeicrc,$value['id']);
			} else {
				//1.5.0 账号加入
				if($online) $logs = Client_Service_Giftlog::getByUnameGiftId($uname,$value['id']);
			}
			//剩下的激活码数量
			$leftNum = Client_Service_Gift::getGiftRemainNum($value['id']);
			//总激活码数量
			$totalNum = Client_Service_Gift::getGiftTotal($value['id']);
			
			//已使用激活的数量
			$grabNum = $totalNum - $leftNum;
			$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
			$href = urldecode($webroot."/client/gift/detail/". '?id=' . $value['game_id'].'&pc=2&intersrc=' . $intersrc . $value['id'] . '&t_bi='.$this->getSource());
			$temp[$key]['title'] = $value['name'];
			$temp[$key]['isGrab'] = ($logs ? "true" : "false");
			$temp[$key]['data-type'] = 2;
			$temp[$key]['viewType'] = 'GiftDetailView';
			$temp[$key]['img'] = urldecode($gameInfo['img']);
			$temp[$key]['data-infpage'] = $value['name'].','.$href.','.$value['game_id'].','.$value['id'];
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['game_id'] = $value['game_id'];
			$temp[$key]['totalNum'] = $totalNum;
			$temp[$key]['leftNum'] = $leftNum;
			$temp[$key]['grabNum'] = $grabNum;
				
		}
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
    /**
     * 我的礼包列表
     */
    public function mygiftAction() {
    	$imei = $this->getInput('imei');
    	$uname = $this->getInput('uname');
    	$version = $this->getInput('version');
    	if(!$imei) $this->clientOutput(array());
    	$imeicrc = crc32(trim($imei));
    	$webroot = Common::getWebRoot();
	   	//imei为空 并且版本 小于等于1.5.0 特殊处理
    	if((strnatcmp($version, '1.5.0') < 0) && ($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510')){
    		$this->clientOutput(array());
    	} 
    	
    	//1.5.0 通过账号获取礼包信息
    	if($uname) {
    		//不在线处理
    		$online = Account_Service_User::checkOnline($uname, $imei);
    		if(!$online) $this->clientOutput(array('sign'=>'GioneeGameHall', 'code'=>'0'));
    		//更新指定imei下空账号信息
    		//if($imei) Client_Service_Giftlog::updateByGiftLog(array('uname'=>$uname), array('uname'=>'', 'imei'=>$imei));
    		$logs = Client_Service_Giftlog::getByUname($uname);
    	}else{
    		$logs = Client_Service_Giftlog::getByImei($imeicrc);
    	} 
    	
	    //判断是否有数据
	    if(!$logs) $this->clientOutput(array());
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	if($uname) $tmp['uname'] = $uname;
    	
    	foreach($logs as $key=>$value){
    		    $giftInfo = Client_Service_Gift::getGiftBaseInfo($value['gift_id']);
	    		$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
	    		$detail = $downinfo = array();
	    		$detail = array(
	    				'title' => "礼包详情",
	    				'viewType' => "GiftDetailView",
	    				'gameId' =>  $value['game_id'],
	    				'url' => sprintf("%s/client/gift/detail/?gift_id=%d&pc=2&intersrc=%s&t_bi=%d", $webroot, $value['gift_id'], 'libao_my'.$value['gift_id'], self::getSource()),
	    				'giftId' => $value['gift_id'],
	    				'param' => array(
	    						   'contentId'=>$value['gift_id'],
	    						   'gameId'=>$value['game_id'],
	    						),
	    		);
	    		$temp[] =  array(
	    				'iconUrl' => $gameInfo['img'],
	    				'giftName' => html_entity_decode($giftInfo['name'], ENT_QUOTES),
	    				'giftLimitTime' => date('Y-m-d', $giftInfo['use_start_time'])."至".date('Y-m-d', $giftInfo['use_end_time']),
	    				'giftKey' => $value['activation_code'],
	    				'giftDetail' => $detail
	    		);
    	}
    	$tmp['data'] = $temp;
	    $this->clientOutput($tmp);
    }
    
    /**
     * 1.5.6我的礼包列表接口
     */
    public function myGiftListAction(){
    	
    	$imei = trim($this->getInput('imei'));
    	$uname = $this->getInput('uname');
    	$version = $this->getInput('version');
    	$page = $this->getInput('page');
    	
    	if(!$uname){
    		$this->clientOutput(array());
    	} 
    	
    	if(!$imei){
    		$this->clientOutput(array());
    	} 
    	
    	if ($page < 1) $page = 1;
    	
    	$online = Account_Service_User::checkOnline($uname, $imei);
    	if (!$online){
    		$data['code'] = '0';
    		$data['success'] = '-1';
    		$data['msg'] = '用户登录过期';
    		$data['sign'] = 'GioneeGameHall';
    		$data['data'] = array();
    		$this->clientOutput($data);
    	} 

    	$this->organizeMyList ( $uname, $page);

    }
    
    
    /**
     * 礼包详情
     */
	/**
	 * @param uname
	 * @param page
	 * @param params
	 * @param data
	 */
	 private function organizeMyList($uname, $page, $params, $data) {
		$params['uname'] = $uname;
    	list($total, $giftList)  = Client_Service_Giftlog::getList($page, 10, $params, array('create_time'=>'DESC'));
    	$hasNext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
    	$temp = array();
    	$data['success'] = true;
    	$data['sign'] = 'GioneeGameHall';
    	foreach ($giftList as $value){
    		$giftInfo = Client_Service_Gift::getGiftBaseInfo($value['gift_id']);
    		$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
    		$temp['list'][]=array(
    				'iconUrl'=>$gameInfo['img'],
    				'giftName'=>html_entity_decode( $giftInfo['name'], ENT_QUOTES),
    				'giftStartTime'  => $giftInfo['use_start_time'],
    				'giftEndTime'=> $giftInfo['use_end_time'],
    				'giftKey'=>$value['activation_code'],
    				'giftId'=>$value['gift_id'],
    				'gameId'=>$value['game_id']
    		);
    	}
    	$data['data'] = $temp;
    	$data['hasNext'] = $hasNext ;
    	$data['curPage'] = intval($page) ;
    	$data['totalCount'] = intval($total) ;
    	$this->clientOutput($data);
	}

    
    public function detailAction() {
    	$data  = $this->getInput(array('gift_id', 'imei', 'uname','version'));
    	$giftId =  intval(trim($data['gift_id']));
    	$imeicrc =  crc32(trim($data['imei']));
    	$imei = trim($data['imei']);
    	$uname = trim($data['uname']);
    	$version = $data['version'];
    	
    	if(!$imei){
    		$this->clientOutput(array());
    	}
    	 
    	//礼包详情
    	$info = Client_Service_Gift::getGift($giftId);
    	//礼包领取记录
    	$log = '';
    	//1.5.0以下特殊处理
    	if(strnatcmp($version, '1.5.0') < 0){
    		if ($imei && $imei != 'FD34645D0CF3A18C9FC4E2C49F11C510') {
    			$log = Client_Service_Giftlog::getByImeiGiftId($imeicrc, $giftId);
    		} else {
    			$log = '';
    		}
    	}
    	//1.5.0 通过账号获取礼包信息
    	$online = false;
    	if($uname) {
    	    $online = Account_Service_User::checkOnline($uname, $imei);
    	}
    	if($uname) {
    		//更新指定imei下空账号信息	
    		if($online) $log = Client_Service_Giftlog::getByUnameGiftId($uname, $giftId);
    	}
    	
    	//剩下的激活码数量
    	$giftRemainNum = Client_Service_Gift::getGiftRemainNum($giftId);
    	//总激活码数量
    	$giftTotal = Client_Service_Gift::getGiftTotal($giftId);
    	//获取游戏信息
    	$gameInfo = Resource_Service_GameData::getGameAllInfo($info['game_id']);
    	
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['iconUrl'] = $gameInfo['img'];
    	$tmp['title'] = "礼包详情";
    	$tmp['giftName'] = html_entity_decode($info['name'], ENT_QUOTES);
    	$tmp['isGrab'] = ($log ? "true" : "false");
    	$tmp['giftNum'] = ($info['status'] ? ($giftRemainNum."/".$giftTotal) : "0/0");
    	$tmp['giftKey'] = ($log ? $log['activation_code'] : "");
    	
    	$temp = array();
    	$temp[] = array (
    				'title'=>"礼包内容",
    				'content' => html_entity_decode($info['content'], ENT_QUOTES)
    	);
    	$temp[] = array (
    				'title'=>"使用时间",
    				'content' => date("Y年m月d日 H:i:s",$info['use_start_time'])."-".date("Y年m月d日 H:i:s",$info['use_end_time'])
    	);
    	$temp[] = array (
    				'title'=>"使用方法",
    				'content' => html_entity_decode($info['method'])
    	);
    	$tmp['data'] = $temp;
    	$this->clientOutput($tmp);
    }
    
    /**
     * 抢礼包
     */
    public function grabAction() {
    	$time = Common::getTime();
    	$data  = $this->getInput(array('gift_id', 'imei', 'uname', 'version', 'sp', 'puuid', 'clientId', 'serverId'));
    	$data['gift_id'] = intval(trim($data['gift_id']));
    	$imeicrc = crc32(trim($data['imei']));
    	$data['imei'] = trim($data['imei']);
    	$imei = trim($data['imei']);
    	$uname = trim($data['uname']);
    	$version = $data['version'];
    	
        //非法判断
    	$tmp = array('sign'=>'GioneeGameHall', 'giftKey'=>"", "giftNum"=>"0/0");
        if(!$data['imei'] || !$data){
        	$this->clientOutput($tmp);
        }
        
        //非法版本判断
        if ((strnatcmp($version, '1.5.0') < 0) && ($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510')) {
        	$this->clientOutput($tmp);
        }
        
        $this->_checkRequestParmes($data,$tmp);
    	//有账号未登录直接退出 1.5.0之前没有账号
    	if($uname){
    		$online = Account_Service_User::checkOnline($uname, $imei);
    		if(!$online) $this->clientOutput(array('sign'=>'GioneeGameHall', 'code'=>'0'));
    	}
    	
    	//判断是否上线
    	$giftInfo = Client_Service_Gift::getGiftBaseInfo($data['gift_id']);
    	if (!$giftInfo['status']) $this->clientOutput($tmp);
    	if ($giftInfo['effect_start_time'] > $time) $this->clientOutput($tmp);
    	if ($giftInfo['effect_end_time'] < $time) $this->clientOutput($tmp); 
    	if (!$giftInfo['game_status']) $this->clientOutput($tmp);

    	//剩下的激活码数量
    	$remainGiftNum = Client_Service_Gift::getGiftRemainNum($data['gift_id']);
    	//总激活码数量
    	$giftTotal = Client_Service_Gift::getGiftTotal($data['gift_id']);
    
    	//没有礼包激活码
    	if(!$remainGiftNum) { 	
    		$tmp['giftKey'] = "";
    		$tmp['giftNum'] = $remainGiftNum."/".($giftTotal);
    		$this->clientOutput($tmp);
    	}
    	
    	//大于1.5.0 通过账号获取礼包信息
    	if($uname) {
    		$tmp['uname'] = $uname;
    		//更新指定imei下空账号信息
    		if($imei) Client_Service_Giftlog::updateByGiftLog(array('uname' => $uname), array('uname'=>'', 'imei'=>$imei));
    		$logs = Client_Service_Giftlog::getByUnameGiftId($uname, $data['gift_id']);
    	}else{
    		//礼包已经领取，直接返回领取信息
    		$logs = Client_Service_Giftlog::getByImeiGiftId($imeicrc, $data['gift_id']);
    	}	
    	//用户抢过此礼包
    	if ($logs) {
    		$tmp['giftKey'] = $logs['activation_code'];
    		$tmp['giftNum'] = $remainGiftNum."/".($giftTotal);
    		$this->clientOutput($tmp);
    	}
    	
    
    	//抢礼包开始,获取激活码
    	$giftAcitivityCode = Client_Service_Giftlog::getBy(array('status'=>0,'gift_id'=>$data['gift_id']),array('send_order'=>'ASC','id'=>'ASC'));
    	if(!$giftAcitivityCode['activation_code']){
    		$tmp['giftKey'] = '';
    		$tmp['giftNum'] = $remainGiftNum."/".($giftTotal);
    		$this->clientOutput($tmp);
    	}
    	$updata = array('uname' => $uname ? $uname : '', 'imei'=>$imei, 'imeicrc'=>$imeicrc,'create_time'=>$time,'status'=>1);
    	$ret = Client_Service_Giftlog::updateGiftlog($updata, $giftAcitivityCode['id']);
    	if($ret){
    		$remainGiftNum = Client_Service_Gift::reduceRemainActivitionCodeCache($data['gift_id'], $remainGiftNum);
    		if($remainGiftNum == 0){
    			$this->giftEmaiToQueue($giftInfo, 0, '礼包零预警');
    			$this->updateGiftStatus($data['gift_id']);
    		}		
    	}
    	$tmp['giftKey'] = ($ret ? $giftAcitivityCode['activation_code']: '');
    	$tmp['giftNum'] =  $remainGiftNum."/".($giftTotal);
    	
       //预警提示,多个邮箱有逗号隔开
    	$temp_arr = array();
    	$giftNum = Game_Service_Config::getValue('game_gift_num');
    	$num =  intval($giftTotal*($giftNum/100));
    	if($giftNum && $remainGiftNum == intval($num) ){
    		$this->giftEmaiToQueue($giftInfo, $remainGiftNum, '剩余礼包比例预警');
    	}
    	$this->clientOutput($tmp);
    }
    
    private function _checkRequestParmes($data, $error){
    	if((substr($data['version'],0,5) == '1.4.8')){
    		return false;
    	}
    	if(!$data['sp'])  $this->clientOutput($error);
    	$spArr = $this->_spAnalyze($data['sp']);
    	$imei = end($spArr);
    	$version = $spArr[1];
    	
    	if($imei != $data['imei']){
    		$this->clientOutput($error);
    	}
    	
    	if($data['imei']){
    		$decryptImei = $this->decryptImei($data['imei']);
    		if(!$decryptImei){
    			$this->clientOutput($error);
    		}
    	}
    	
    	if($version != $data['version']){
    		$this->clientOutput($error);
    	}
    	
    	if((strnatcmp($data['version'], '1.5.5') >= 0)){
	    	$rs = Common::verifyClientEncryptData($data['puuid'], $data['uname'], $data['clientId']);
	    	if(!$rs){
	    		$this->clientOutput($error);
	    	}
    	}
    	
        if ($this->isRobber($data['imei'])) {
            $this->clientOutput($error);
        }
    }
    
    private function _spAnalyze($sp){
    	return explode('_',$sp);
    }
    
    private function decryptImei($imei){
    	$cryptAES = new Util_CryptAES();
  
    	$decryptImei = $cryptAES->decrypt($imei);
    	
    	if(!$decryptImei) {
    		return null;
    	}

    	if(!preg_match("/^[0-9]{15}$/i", $decryptImei)) {
    		return null;
    	}
    	
    	return $decryptImei;
    }
    
    private function _checkGioneeRequest($decryptImei, $encryptServerId, $data){  	
    	$key = 'grab'.substr($decryptImei,-8).substr($data['uname'],-8);
    	$key = substr($key, 0, 16);
    	$iv = substr($data['puuid'], 0, 16); 

    	$cryptAES = new Util_CryptAES();
    	$cryptAES->setIv($iv);
    	$cryptAES->setKey($key);
    	$decryptServerId = $cryptAES->decrypt($encryptServerId);
    	
    	$serverId = $data['puuid'].'_'.$decryptImei.'_'.$data['uname'];
    	
    	if($serverId != $decryptServerId) {
    		return false; 	
    	}
    	
    	return true;
    }
    
    private function isRobber($imei) {
        $search = array();
        $search['imeicrc'] = crc32($imei);
        $recently = Common::getTime() - 3600;// 1 hour recently
        $search['create_time'] = array('>', $recently);
        list($total, $giftLogs) = Client_Service_Giftlog::getList(1, 12, $search);
        if (empty($giftLogs)) {
            return false;
        }

        $unameList = array();
        foreach ($giftLogs as $key => $value) {
            if ($imei == $value['imei']) {
                $unameList[] = $value['uname'];
            }
        }

        $unameCount = count(array_unique($unameList));
        if ($unameCount >= 10) {
            return true;
        }

        return false;
    }

    /**
     * 把要发邮件的礼包警告信息写入信息队列中
     */
    public function giftEmaiToQueue($giftInfo, $remainGiftNum, $emailSubject){
    	$email = html_entity_decode(Game_Service_Config::getValue('game_gift_eamil'));
    	if($email){
    		$gameInfo = Resource_Service_GameData::getGameAllInfo($giftInfo['game_id']);
    		$temp_arr = array('gift_id'=>$giftInfo['id'],'game_id'=>$giftInfo['game_id'],'game_name'=>$gameInfo['name'],'gift_name'=>$giftInfo['name'],'remain_gifts'=>$remainGiftNum,'email'=>$email, 'emailSubject'=>$emailSubject);
    		Common::getQueue()->push('game_gift',$temp_arr);
    	}
    }
    
    
    /**
     * 礼包自动下线
     * @param int $giftId
     */
    public function updateGiftStatus($giftId){
    	$ret = Client_Service_Gift::updateGift(array('status'=>0), $giftId);
    }
    
    /**
     * 游戏详情页
     */
    public function gameinfoAction() {
    	$game = $tmp  = array();
    	$game_id  = $this->getInput('game_id');
    	$from  = $this->getInput('from');
    	//v1.5.4 add
    	$gameName  = $this->getInput('gameName');
    	if($from == "gn" || !$from){
    		$game = Resource_Service_GameData::getGameAllInfo(intval($game_id));
    		if($gameName == 'com.gionee.gsp'){
    			if(ENV == 'product'){
    			    $game = Resource_Service_GameData::getGameAllInfo(1971);
    			} else {
    				$game = Resource_Service_GameData::getGameAllInfo(142);
    			}
    		}
    	} else if($from == "baidu"){
    		$baiduApi = new Api_Baidu_Game();
    		$game = $baiduApi->getInfo($game_id, $from);
    	}
    	
    	if(($game['status'] && ($from == "gn" || !$from)) || $from == "baidu"){
    		$tmp['sign'] = 'GioneeGameHall';
    		$tmp['gameId'] = $game['id'];
    		$tmp['gameName'] = html_entity_decode($game['name']);
    		$tmp['downUrl'] = $game['link'];
    		$tmp['packageName'] = $game['package'];
    		$tmp['fileSize'] = $game['size'];
    		$tmp['iconUrl'] = $game['img'];
    		$tmp['sdkVersion'] = 'Android'.(($from == "gn" || !$from) ? $game['min_sys_version_title']: $game['apply_version']);
    		$resolution = (($from == "gn" || !$from) ? ($game['min_resolution_title']."-".$game['max_resolution_title']) : ($game['min_resolution']."-".$game['max_resolution'])); 
    		$tmp['resolution'] = $resolution;
    		//客户端 v1.5.3免流量标识
    		$tmp['freedl'] = $game['freedl'];
    		$tmp['reward'] = $game['reward'];
    	}
    	$this->clientOutput($tmp);
    }

    public function getGiftsForGamesAction() {
        list($idsInput,$packagesInput) = $this->extractGameIds();

        if($idsInput) {
        	list($gameIds, $listInfo) = $this->fillGamesInfo($idsInput);
        } else if($packagesInput){
         	list($gameIds, $listInfo) = $this->fillGamesInfoByPackages($packagesInput);
        }
        
        $giftListData = $this->fillGiftsInfo($gameIds, $listInfo);
        $data[Util_JsonKey::LIST_ITEMS] = $giftListData;

        $this->localOutput(0, '', $data);
    }
    

    private function extractGameIds() {
        $gameIds = $this->getInput('gameIds');
        $appLists = $this->getInput('appList');
        if(!$gameIds && !$appLists) {
            $this->localOutput(-1, 'not found gameIds or gamePackages field');
            return;
        }

        if($gameIds) $gameIdArr = explode('|', $gameIds);
        if($appLists) $packageArr = explode('|', $appLists);

        return array($gameIdArr,$packageArr);
    }

    private function fillGamesInfo($idsInput) {
        $gameIds = array();
        $listInfo = array();
        foreach ($idsInput as $idKey => $gameId) {
            $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
            if (empty($gameInfo)) {
                continue;
            }
            $gameIds[] = $gameId;
            $listInfo[$gameId][Util_JsonKey::GAME_ID] = $gameId;
            $listInfo[$gameId][Util_JsonKey::ICON_URL] = $gameInfo['img'];
            $listInfo[$gameId][Util_JsonKey::GAME_NAME] = $gameInfo['name'];
            $listInfo[$gameId][Util_JsonKey::PACKAGE_NAME] = $gameInfo['package'];
        }

        if (empty($gameIds)) {
            $this->localOutput(-1, 'not found valid game id');
        }
        return array($gameIds, $listInfo);
    }
    
    
    private function fillGamesInfoByPackages($packagesInput) {
    	$gameIds = array();
    	$listInfo = array();
    	foreach ($packagesInput as $packageKey => $gamePackage) {
    		$game = Resource_Service_Games::getBy(array('package'=>$gamePackage));
    		$gameId = $game['id'];
    		$gameInfo = Resource_Service_GameData::getBasicInfo($game['id']);
    		if (empty($gameInfo)) {
    			continue;
    		}
    		$gameIds[] = $gameId;
    		$listInfo[$gameId][Util_JsonKey::GAME_ID] = $gameId;
    		$listInfo[$gameId][Util_JsonKey::ICON_URL] = $gameInfo['img'];
    		$listInfo[$gameId][Util_JsonKey::GAME_NAME] = $gameInfo['name'];
    		$listInfo[$gameId][Util_JsonKey::PACKAGE_NAME] = $gameInfo['package'];
    	}
    
    	if (empty($gameIds)) {
    		$this->localOutput(-1, 'not found valid game id');
    	}
    	return array($gameIds, $listInfo);
    }

    private function fillGiftsInfo($gameIds, $giftListData) {
        $search['status'] = Client_Service_Gift::GIFT_STATE_OPENED;
        $currentTime = Common::getTime();
        $search['effect_start_time'] = array('<=', $currentTime);
        $search['effect_end_time'] = array('>=', $currentTime);
        if (count($gameIds) > 1) {
            $search['game_id'] = array('IN', $gameIds);
        } else {
            $search['game_id'] = $gameIds[0];
        }

        $gitfs = Client_Service_Gift::getsBy($search);

        foreach ($gitfs as $giftKey => $giftInfo) {
            $gameId = $giftInfo['game_id'];
            if (!array_key_exists($gameId, $giftListData)) {
                continue;
            }
            if (empty($giftListData[$gameId][Util_JsonKey::GIFT_NUM])) {
                $giftListData[$gameId][Util_JsonKey::GIFT_NUM] = 0;
                $giftListData[$gameId][Util_JsonKey::GIFT_START_TIME] = 0;
            }
            $giftListData[$gameId][Util_JsonKey::GIFT_NUM] += 1;
            if ($giftInfo['effect_start_time'] > 
                    $giftListData[$gameId][Util_JsonKey::GIFT_START_TIME]) {
                $giftListData[$gameId][Util_JsonKey::GIFT_START_TIME] = $giftInfo['effect_start_time'];
            }
        }

        $gameGiftInfoList = array();
        foreach($giftListData as $gameId => $gameGiftInfo) {
            if(empty($gameGiftInfo[Util_JsonKey::GIFT_NUM])) {
                continue;
            }
            $gameGiftInfoList[] = $gameGiftInfo;
        }

        return $gameGiftInfoList;
    }
    
    public function getGiftsForAllGamesAction() {
        $currentPage = $this->getInput('page');
        $numPerPage = $this->getInput('numPerPage');
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if (empty($numPerPage)) {
            $numPerPage = 21;
        }

        list($total, $gameIds) = $this->getGameIdsWhichHasGift($currentPage, $numPerPage);
        list($gameIds, $gameListInfo) = $this->fillGamesInfo($gameIds);
        $listInfo = $this->fillGiftNums($gameListInfo);

        $hasNext = ($currentPage * $numPerPage >= $total) ? false : true;
        $pageData['hasnext'] = $hasNext;
        $pageData['curpage'] = $currentPage;
        $pageData['list'] = $listInfo;

        $this->localOutput(0, '', $pageData);
    }
    
    private function getGameIdsWhichHasGift($currentPage, $perPage) {
        $search['status'] = Client_Service_Gift::GIFT_STATE_OPENED;
        $search['game_status'] = Client_Service_Gift::GAME_STATE_ONLINE;
        $currentTime = Common::getTime();
        $search['effect_start_time'] = array('<=', $currentTime);
        $search['effect_end_time'] = array('>=', $currentTime);
        list($total, $giftList) = Client_Service_Gift::getGameList($currentPage, $perPage, $search);

        foreach($giftList as $key => $value) {
            $gameIds[] = $value['game_id'];
        }

        return array($total, $gameIds);
    }

    private function fillGiftNums($gameListInfo) {
        $listInfo = array();

        foreach ($gameListInfo as $gameId => $gameInfo) {
            $giftInfo = Client_Service_Gift::getGiftByGameId($gameId);
            $giftNum = Client_Service_Gift::getGiftNumByGameId($gameId);
            $item = $gameInfo;
            $item[Util_JsonKey::GIFT_NUM] = $giftNum;
            $listInfo[] = $item;
        }

        return $listInfo;
    }

    public function getHotGiftListAction() {
        $page = $this->getInput('page');
        if (!$page) {
        	$page = 1;
        }
        $userName = $this->getInput('uname');
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));

        $onLine = false;
        if($userName) {
           $onLine = Account_Service_User::checkOnline($userName, $imei);
        }
        $perPage = 10;

        $currentTime = Common::getTime();
        $search['effect_start_time'] = array('<=', $currentTime);
        $search['effect_end_time'] = array('>=', $currentTime);
        $search['status'] = Client_Service_Gift::GIFT_STATE_OPENED;
        $search['game_status']  = Resource_Service_Games::STATE_ONLINE;
        list($total, $hotGiftList) = Client_Service_GiftHot::getList($page, $perPage, $search);

        $listData = $this->fillHotGiftsData($onLine, $userName, $hotGiftList);

        $countLoaded = $page * $perPage;
        $hasNext = ($countLoaded >= $total) ? false : true;

        $hotGiftsData[Util_JsonKey::HAS_NEXT] = $hasNext;
        $hotGiftsData[Util_JsonKey::CUR_PAGE] = $page;
        $hotGiftsData[Util_JsonKey::LIST_ITEMS] = $listData;

        $this->localOutput(0, '', $hotGiftsData);
    }

    private function fillHotGiftsData($onLine, $userName, $hotGiftList) {
        $listData = array();
        foreach($hotGiftList as $key=>$value) {
            $giftId = $value['gift_id'];
            $gameId = $value['game_id'];

            $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
            if (empty($gameInfo)) {
                continue;
            }

            if($onLine) {
                $log = Client_Service_Giftlog::getByUnameGiftId($userName, $giftId);
            }

            $item[Util_JsonKey::GAME_ID] = $gameId;
            $item[Util_JsonKey::GAME_NAME] = $gameInfo['name'];
            $item[Util_JsonKey::ICON_URL] = $gameInfo['img'];

            $item[Util_JsonKey::GIFT_ID] = $giftId;
            $item[Util_JsonKey::GIFT_NAME] = html_entity_decode($value['gift_name'], ENT_QUOTES);
            $item[Util_JsonKey::GIFT_KEY_TOTAL] = Client_Service_Gift::getGiftTotal($giftId);
            $item[Util_JsonKey::GIFT_KEY_REMAINS] = Client_Service_Gift::getGiftRemainNum($giftId);
            $item[Util_JsonKey::IS_GRAB] = ($log ? "true" : "false");
            $listData[] = $item;
        }

        return $listData;
    }
    
    /**
     * 单个游戏礼包列表
     */
    public function getGiftListByGameIdAction(){
    	$page = $this->getInput('page');
    	if (!$page) {
    		$page = 1;
    	}
    	$imei = $this->getInput('imei');
    	$uname = $this->getInput('uname');
    	$gameId = intval($this->getInput('gameId'));
    		
    	if($uname) {
    		$online = Account_Service_User::checkOnline($uname, $imei);
    	}
    	$this->organizeMyGiftListByGameId ( $uname, $page, $gameId, $online);

    }
	/**
	 * @param uuid
	 * @param page
	 * @param gameId
	 */
	 private function organizeMyGiftListByGameId($uname, $page, $gameId, $online) {
    	$params['game_id'] = $gameId;
    	$params['status'] = 1;
    	$params['game_status'] = 1;
        $params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
    	list($total, $giftList)  = Client_Service_Gift::getList($page, 10, $params, array('sort'=>'DESC', 'effect_start_time' => 'DESC','id'=>'DESC'));
    	$hasNext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
    	$data = array();
    	$newStartTime = 0;
    	foreach ($giftList as $value){
    		$log = array();
    		$giftInfo = Client_Service_Gift::getGiftBaseInfo($value[Util_JsonKey::ID]);
    		$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
    		if($online) {
    			$log = Client_Service_Giftlog::getByUnameGiftId($uname, $value[Util_JsonKey::ID]);
    		}
    		$data[Util_JsonKey::LIST_ITEMS][]=array(
    				Util_JsonKey::ICON_URL=>$gameInfo[Util_JsonKey::IMAGE],
    				Util_JsonKey::GIFT_NAME=>html_entity_decode($giftInfo[Util_JsonKey::NAME], ENT_QUOTES),
    				Util_JsonKey::GIFT_KEY_TOTAL=>Client_Service_Gift::getGiftTotal($value[Util_JsonKey::ID]),
    				Util_JsonKey::GIFT_KEY_REMAINS=> Client_Service_Gift::getGiftRemainNum($value[Util_JsonKey::ID]),
    				Util_JsonKey::GIFT_ID=>$value[Util_JsonKey::ID],
    				Util_JsonKey::GAME_ID=>$value['game_id'],
    				Util_JsonKey::IS_GRAB=> ($log ? "true" : "false")
    		);
    		if ($giftInfo['effect_start_time'] > $newStartTime) {
    			$newStartTime = $giftInfo['effect_start_time'];
    		}
    	}
    	
    	$data[Util_JsonKey::HAS_NEXT] = $hasNext ;
    	$data[Util_JsonKey::GIFT_START_TIME] = $newStartTime ;
    	$data[Util_JsonKey::CUR_PAGE] = intval($page) ;
    	$data[Util_JsonKey::TOTAL_COUNT] = intval($total) ;
    	$this->localOutput(0, '', $data);
	}

    
}
