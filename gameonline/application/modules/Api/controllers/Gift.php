<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GiftController extends Api_BaseController {
	
	public $perpage = 10;
	
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		//1.5.1 根据游戏id查找在线，正在进行的礼包
		$game_id = $this->getInput('id');
		$isDetail = $this->getInput('isDetail');
		 
		if ($page < 1) $page = 1;
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$imeicrc = crc32(trim($imei));
	
		$parmas = explode('_', $sp);
		$version = $parmas[1];
	
		//1.5.0 帐号加入
		$uname = $this->getInput('uname');
		$online = false;
		if($uname) $online = Account_Service_User::checkOnline($uname, $imei);
	
		//礼包列表
		$search = array('status' => 1, 'game_status'=>1);
		$search['effect_start_time'] = array('<=', Common::getTime());
		$search['effect_end_time'] = array('>=', Common::getTime());
		if((strnatcmp($version, '1.5.1') >= 0) && $isDetail == 1) $search['game_id'] = $game_id;
		list($total, $gifts) = Client_Service_Gift::getList($page, $this->perpage,$search);
		$webroot = Common::getWebRoot();
		$temp = array();
		$intersrc = ($intersrc) ? $intersrc : 'olg_libao';
		foreach($gifts as $key=>$value) {
			$logs = $remain_gifts = $not_gifts = $info = array();
			//礼包领取记录
			//1.5 之前兼容
			if((strnatcmp($version, '1.5.0') < 0) && ($imei != "FD34645D0CF3A18C9FC4E2C49F11C510")){
				$logs = Client_Service_Giftlog::getByImeiGiftId($imeicrc,$value['id']);
			} else {
				//1.5.0 帐号加入
				if($online) $logs = Client_Service_Giftlog::getByUnameGiftId($uname,$value['id']);
			}
			//剩下的激活码数量
			$leftNum = intval(Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']));
			//总激活码数量
			$totalNum = Client_Service_Giftlog::getGiftlogCount($value['id']);
			//已使用激活的数量
			$grabNum = $totalNum - $leftNum;
				
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
				
			$href = urldecode($webroot."/client/gift/detail/". '?id=' . $value['game_id'].'&pc=2&intersrc=' . $intersrc . $value['id'] . '&t_bi='.$this->getSource());
			$temp[$key]['title'] = $value['name'];
			$temp[$key]['isGrab'] = ($logs ? "true" : "false");
			$temp[$key]['data-type'] = 2;
			$temp[$key]['viewType'] = 'GiftDetailView';
			$temp[$key]['img'] = urldecode($info['img']);
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
    	if((strnatcmp($version, '1.5.0') < 0) && ($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510')) $this->clientOutput(array());
    	
    	$logs = Client_Service_Giftlog::getByImei($imeicrc);
    	//1.5.0 通过帐号获取礼包信息
    	if($uname) {
    		//不在线处理
    		$online = Account_Service_User::checkOnline($uname, $imei);
    		if(!$online) $this->clientOutput(array('sign'=>'GioneeGameHall', 'code'=>'0'));
    		//更新指定imei下空帐号信息
    		//if($imei) Client_Service_Giftlog::updateByGiftLog(array('uname'=>$uname), array('uname'=>'', 'imei'=>$imei));
    		$logs = Client_Service_Giftlog::getByUname($uname);
    	} 
    	
	    //判断是否有数据
	    if(!$logs) $this->clientOutput(array());
    
    	//是否上线
    	$gifts = Client_Service_Gift::getOnlineGifts();
    	$gifts = Common::resetKey($gifts, 'id');
    	$ids = array_unique(array_keys($gifts));
    	
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	if($uname) $tmp['uname'] = $uname;
    	
    	foreach($logs as $key=>$value){
    		if(in_array($value['gift_id'],$ids)){  //判断是否下线
	    		$game = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
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
	    				'iconUrl' => $game['img'],
	    				'giftName' => html_entity_decode($gifts[$value['gift_id']]['name']),
	    				'giftLimitTime' => date('Y-m-d', $gifts[$value['gift_id']]['use_start_time'])."至".date('Y-m-d', $gifts[$value['gift_id']]['use_end_time']),
	    				'giftKey' => $value['activation_code'],
	    				'giftDetail' => $detail
	    		);
    	 }
    	}
    	$tmp['data'] = $temp;
	    $this->clientOutput($tmp);
    }
    
    /**
     * 礼包详情
     */
    
    public function detailAction() {
    	$data  = $this->getInput(array('gift_id', 'imei', 'uname','version'));
    	$gift_id =  intval(trim($data['gift_id']));
    	$imeicrc =  crc32(trim($data['imei']));
    	$imei = trim($data['imei']);
    	$uname = trim($data['uname']);
    	$version = $data['version'];
    	//礼包详情
    	$info = Client_Service_Gift::getGift($gift_id);
    	//礼包领取记录
    	$log = '';
    	//1.5.0以下特殊处理
    	if(strnatcmp($version, '1.5.0') < 0){
    		if ($imei != 'FD34645D0CF3A18C9FC4E2C49F11C510') {
    			$log = Client_Service_Giftlog::getByImeiGiftId($imeicrc, $gift_id);
    		} else {
    			$log = '';
    		}
    	}
    	//1.5.0 通过帐号获取礼包信息
    	$online = false;
    	if($uname) $online = Account_Service_User::checkOnline($uname, $imei);
    	if($uname) {
    		//更新指定imei下空帐号信息
    		//if($imei) Client_Service_Giftlog::updateByGiftLog(array('uname' => $uname), array('uname'=>'', 'imei'=>$imei));
    		if($online) $log = Client_Service_Giftlog::getByUnameGiftId($uname, $gift_id);
    	}
    	
    	
    	//剩下的激活码数量
    	$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0, $gift_id); 
    	//已经领过激活码数量
    	$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1, $gift_id);
    	//获取游戏信息
    	$game = Resource_Service_Games::getGameAllInfo(array("id"=>$info['game_id']));
    	
    	    
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['iconUrl'] = $game['img'];
    	$tmp['title'] = "礼包详情";
    	$tmp['giftName'] = html_entity_decode($info['name']);
    	$tmp['isGrab'] = ($log ? "true" : "false");
    	$tmp['giftNum'] = ($info['status'] ? ($remain_gifts."/".($not_gifts + $remain_gifts)) : "0/0");
    	$tmp['giftKey'] = ($log ? $log['activation_code'] : "");
    	
    	$temp = array();
    	$temp[] = array (
    				'title'=>"礼包内容",
    				'content' => html_entity_decode($info['content'])
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
    	$data  = $this->getInput(array('gift_id', 'imei', 'uname', 'version'));
    	$data['gift_id'] = intval(trim($data['gift_id']));
    	$imeicrc = crc32(trim($data['imei']));
    	$data['imei'] = trim($data['imei']);
    	$imei = trim($data['imei']);
    	$uname = trim($data['uname']);
    	$version = $data['version'];
    	//1.5.0帐号登陆检测
    	if($uname){
    		$online = Account_Service_User::checkOnline($uname, $imei);
    		if(!$online) $this->clientOutput(array('sign'=>'GioneeGameHall', 'code'=>'0'));
    	}
    	
    	$tmp = array('sign'=>'GioneeGameHall', 'giftKey'=>"", "giftNum"=>"0/0");
    	if(!$data && !$data['imei']) $this->clientOutput($tmp);
    	
    	//判断是否上线
    	$gift = Client_Service_Gift::getGift($data['gift_id']);
    	
    	if (!$gift['status']) $this->clientOutput($tmp);
    	if ($gift['effect_start_time'] > $time) $this->clientOutput($tmp);
    	if ($gift['effect_end_time'] < $time) $this->clientOutput($tmp); 
    	if (!$gift['game_status']) $this->clientOutput($tmp);

    	//剩下的激活码数量
    	$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$data['gift_id']);
    	//总激活码数量
    	$total_nums = Client_Service_Giftlog::getGiftlogCount($data['gift_id']);
    	
    	//非法版本判断
    	if ((strnatcmp($version, '1.5.0') < 0) && ($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510')) {
    		$tmp['giftKey'] = "";
    		$tmp['giftNum'] = $remain_gifts."/".($total_nums);
    		$this->clientOutput($tmp);
    	}
    	
    	//礼包已经领取，直接返回领取信息
    	$logs = Client_Service_Giftlog::getByImeiGiftId($imeicrc, $data['gift_id']);

    	//1.5.0 通过帐号获取礼包信息
    	if($uname) {
    		$tmp['uname'] = $uname;
    		//更新指定imei下空帐号信息
    		if($imei) Client_Service_Giftlog::updateByGiftLog(array('uname' => $uname), array('uname'=>'', 'imei'=>$imei));
    		$logs = Client_Service_Giftlog::getByUnameGiftId($uname, $data['gift_id']);
    	}
    	
    	//已抢过的礼包信息
    	if ($logs) {
    		$tmp['giftKey'] = $logs['activation_code'];
    		$tmp['giftNum'] = $remain_gifts."/".($total_nums);
    		$this->clientOutput($tmp);
    	}
    	
    	//没有礼包激活码啦
    	if(!$remain_gifts) {
    		$tmp['giftKey'] = "";
    		$tmp['giftNum'] = $remain_gifts."/".($total_nums);
    		$this->clientOutput($tmp);
    	}
    	
    	//抢礼包开始
    	$grab_gift = Client_Service_Giftlog::getBy(array('status'=>0,'gift_id'=>$data['gift_id']));
   	
    	//1.5.0 通过帐号抢礼包
    	$updata = array('uname' => $uname ? $uname : '', 'imei'=>$imei, 'imeicrc'=>$imeicrc,'create_time'=>$time,'status'=>1);
    	$ret = Client_Service_Giftlog::updateGiftlog($updata, $grab_gift['id']);
    	$tmp['giftKey'] = ($ret ? $grab_gift['activation_code']: "");
    	$tmp['giftNum'] = ($ret ? ($remain_gifts - 1 ) : $remain_gifts)."/".($total_nums);
    	
    	//预警提示,多个邮箱有逗号隔开
    	$num =  ceil($total_nums*(Game_Service_Config::getValue('game_gift_num')/100));
    	if(($remain_gifts-1) <= intval($num) ){
    		$email = html_entity_decode(Game_Service_Config::getValue('game_gift_eamil'));
    		if($email){
    			$game_info = Resource_Service_Games::getGameAllInfo(array('id'=>$gift['game_id']));
    			Common::sendEmail('礼包警告','游戏ID为"'.$gift['game_id'].'"的"'.$game_info['name'].'"游戏，发布的礼包"'.$gift['name'].'"还剩余"'.$remain_gifts.'"个，请及时添加',$email);
    		}
    	}
    	$this->clientOutput($tmp);
    }
    
    /**
     * 游戏详情页
     */
    public function gameinfoAction() {
    	$game = $tmp  = array();
    	$game_id  = $this->getInput('game_id');
    	$from  = $this->getInput('from');
    	if($from == "gn" || !$from){
    		$game = Resource_Service_Games::getGameAllInfo(array('id'=>intval($game_id)));
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
    	}
    	$this->clientOutput($tmp);
    }
}
