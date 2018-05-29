<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_GiftController extends Api_BaseController {
	
	public $perpage = 10;
	
    
    /**
     * 我的礼包列表
     */
    public function mygiftAction() {
    	$imei = $this->getInput('imei');
    	if(!$imei) $this->clientOutput(array());
    	$imei = crc32(trim($imei));
    	$webroot = Common::getWebRoot();
    	
	    $logs = Client_Service_Giftlog::getByImei($imei);
	    //判断是否有数据
	    if(!$logs) $this->clientOutput(array());
    
    	//是否上线
    	$gifts = Client_Service_Gift::getOnlineGifts();
    	$gifts = Common::resetKey($gifts, 'id');
    	$ids = array_unique(array_keys($gifts));
    	
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	foreach($logs as $key=>$value){
    		if(in_array($value['gift_id'],$ids)){  //判断是否下线
	    		$game = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
	    		$detail = $downinfo = array();
	    	
	    		$detail = array(
	    				'title' => "礼包详情",
	    				'viewType' => "GiftDetailView",
	    				'gameId' =>  $value['game_id'],
	    				'url' => sprintf("%s/kingstone/gift/detail/?gift_id=%d&pc=2&intersrc=%s&t_bi=%d", $webroot, $value['gift_id'], 'libao_my'.$value['gift_id'], self::getSource()),
	    				'giftId' => $value['gift_id']
	    		);
	    	
	    		$temp[] =  array(
	    				'iconUrl' => Common::getAttachPath(). $game['img'],
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
    	$data  = $this->getInput(array('gift_id', 'imei'));
    	$gift_id =  intval(trim($data['gift_id']));
    	$crc_imei =  crc32(trim($data['imei']));
    	
    	//礼包详情
    	$info = Client_Service_Gift::getGift($gift_id);
    	//礼包领取记录
    	$log = Client_Service_Giftlog::getByImeiGiftId($crc_imei, $gift_id);
    	
    	//剩下的激活码数量
    	$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0, $gift_id); 
    	//已经领过激活码数量
    	$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1, $gift_id);
    	//获取游戏信息
    	$game = Resource_Service_Games::getGameAllInfo(array("id"=>$info['game_id']));
    	
    	    
    	$tmp = array();
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['iconUrl'] = $game['img'];
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
    	$data  = $this->getInput(array('gift_id', 'imei'));
    	//
    	$data['gift_id'] = intval(trim($data['gift_id']));
    	$crc_imei = crc32(trim($data['imei']));
    	$data['imei'] = trim($data['imei']);
    	//
    	$tmp = array('sign'=>'GioneeGameHall', 'giftKey'=>"", "giftNum"=>"0/0");
    	if(!$data && !$data["imei"]) $this->clientOutput($tmp);

    	//判断是否上线
    	$gift = Client_Service_Gift::getGift($data['gift_id']);
    	
    	if (!$gift['status']) $this->clientOutput($tmp);
    	if ($gift['effect_start_time'] > $time) $this->clientOutput($tmp);
    	if ($gift['effect_end_time'] < $time) $this->clientOutput($tmp); 
    	if (!$gift['game_status']) $this->clientOutput($tmp);
    	
    	//剩下的激活码数量
    	$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$data['gift_id']);
    	//已经领过激活码数量
    	$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1, $data['gift_id']);
    	
    	if (!$remain_gifts || $crc_imei == crc32("FD34645D0CF3A18C9FC4E2C49F11C510")) {
    		$tmp['giftKey'] = "";
    		$tmp['giftNum'] = $remain_gifts."/".($not_gifts + $remain_gifts);
    		$this->clientOutput($tmp);
    	}
    	
    	 
    	//礼包已经领取，直接返回领取信息
    	$logs = Client_Service_Giftlog::getByImeiGiftId($crc_imei, $data['gift_id']);
    	if ($logs) {
    		$tmp['giftKey'] = $logs['activation_code'];
    		$tmp['giftNum'] = $remain_gifts."/".($not_gifts + $remain_gifts);
    		$this->clientOutput($tmp);
    	}
    	
    	//礼包领取
    	$grab_gift = Client_Service_Giftlog::getBy(array('status'=>0,'gift_id'=>$data['gift_id']));
    	$ret = Client_Service_Giftlog::updateImeiGiftId($data['imei'],$grab_gift['id']);
    	$tmp['giftKey'] = ($ret ? $grab_gift['activation_code']: "");
    	$tmp['giftNum'] = ($ret ? ($remain_gifts - 1 ) : $remain_gifts)."/".($not_gifts + $remain_gifts);
    	
    		
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
    		$tmp['gameName'] = $game['name'];
    		$tmp['downUrl'] = $game['link'];
    		$tmp['packageName'] = $game['package'];
    		$tmp['fileSize'] = $game['size'];
    		$tmp['sdkVersion'] = 'Android'.(($from == "gn" || !$from) ? $game['min_sys_version_title']: $game['versions']);
    		$resolution = (($from == "gn" || !$from) ? ($game['min_resolution_title']."-".$game['max_resolution_title']) : ($game['min_resolution']."-".$game['max_resolution'])); 
    		$tmp['resolution'] = $resolution;
    	}
    	$this->clientOutput($tmp);
    }
}
