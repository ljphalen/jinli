<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 游戏大厅 1.4.8 版本 开始使用
 * API V2
 * @author lichanghua
 *
 */
class IndexiController extends Api_BaseController {
	
	public $perpage = 10;
	public $cacheKey = 'Client_Index_index';
    
    /**
     * 置顶广告
     */
    public function turnAction() {
        /**
         //用新代码实现
    	list(, $ads) = Client_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>1, 'status'=>1, 'hits'=>1));
    	*/
        $ads = Game_Api_RecommendBanner::getOldVersionBannerData();
		$i = 1;
		foreach($ads as $key=>$value) {
			$info = Client_Service_IndexAdI::cookAd($value, "ad1", $i++);
			$info['img'] = Common::getAttachPath() . $value['img'];
			$ads[$key] = array_merge($ads[$key], $info);
		}
        return $this->_jsonData($ads, 'ad1');
    }
    
    /**
     * 广告位1
     */
    public function newAction() {
    	//最新游戏
    	list(, $games) = Client_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>2,'status'=>1));
    	$i = 1;
    	foreach($games as $key=>$value) {
    		$games[$key] = array_merge($games[$key], Client_Service_IndexAdI::cookAd($value, "ad2", $i++));
    	}
    	return $this->_jsonData($games, 'ad2');
    }
    
    /**
     * 广告位2
     */
    public function bannelAction() {
    	//首页bannel
    	list(, $bannels) = Client_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>5,'status'=>1));
    	$i = 1;
    	foreach($bannels as $key=>$value) {
    		$bannels[$key] = array_merge($bannels[$key], Client_Service_IndexAdI::cookAd($value, "ad3", $i++));
    	}
    	return $this->_jsonData($bannels, 'ad3');
    }
    
    /**
     * 广告位1(外链)
     */
    public function channelAction() {
    	//首页channel
    	$page = intval($this->getInput('page'));
    	if ($page < 1 || !$page) $page = 1;
    	list(, $channels) = Client_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>7,'status'=>1));
    	$i = 1;
    	foreach($channels as $key=>$value) {
    		$channels[$key]['img'] = Common::getAttachPath() . $channels[$key]['img'];
    		$channels[$key] = array_merge($channels[$key], Client_Service_IndexAdI::cookAd($value, "ad2", $i++));
    	}
    	return $this->_jsonData($channels, 'ad2',$page);
    }
    
    /**
     * 广告位3
     */
    public function recommendAction() {
    	//推荐专题
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	$params =  array();
    	$params['ad_type'] = 3;
    	$params['status'] = 1;
    	//$params['not_ids'] = $this->havePackage();
    	list($total, $subjects) = Client_Service_Ad::getCanUseApiAds($page, $this->perpage, $params);
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$i = 1;
    	foreach($subjects as $key=>$value) {
    		$subject_games[$key] = array_merge($subjects[$key], Client_Service_IndexAdI::cookAd($value, "ad4", (($page - 1) * $this->perpage)+ $i++));
    	}
    	return $this->_jsonData($subject_games, 'ad4' ,$page, $hasnext);
    	
    }
    
    /**
     * 活动公告
     */
    public function announceAction() {
    	$params = $tmp = array();
    	/**
    	 //用新代码实现
    	 $params['ad_type'] = 8;
    	 list(, $announce) = Client_Service_Ad::getCanUseNormalAds(1, 1, $params);
    	*/
    	$announce = Game_Api_RecommendText::getOldVersionTextData();
    	if($announce){
	    	$ret = Client_Service_IndexAdI::cookAd($announce[0], "ad5", 1);
	    	//$tmp['img'] = Common::getAttachPath().$announce[0]['img'];
	    	$tmp['head'] = 'textAnnounce';
	    	$tmp['title'] = $announce[0]['title'];
	    	$tmp['data-type'] = $ret['data-type'];
	    	$tmp['data-infpage'] = $ret['data-Info'];
	    	$this->cache(array($tmp['img']), 'gonggao');
    	}
    	$this->output(0, '', $tmp);
    }
    
    /**
     * 分享接口
     */
    public function shareAction(){	
    	$version = $this->checkAppVersion();
    	//1.5.3之后的版本
    	if($version >= 5){    		
    		$type = $this->getInput('type');
    		$id = $this->getInput('id');
    		$tmp = array();
    		$tmp['success'] = true;
    		$tmp['msg'] = '';
    		$tmp['sign'] = 'GioneeGameHall';
    		$tmp['data'] =  '';
    		//分享游戏详情
    		if($type == 'gameDetail'){
    			 $this->shareGameDetail($type, $id);
    		//分享活动详情
    		}elseif($type == 'eventDetail'){
    			$this->shareActivityDetail($type, $id);
    		//分享其它webview页面 例如论坛，节日活动等等。
    		}elseif($type == 'web'){
    			$this->shareBbsDetail($type, $id);
    		}else{
    			$this->clientOutput($tmp);
    		}   		
        //其他版本		
    	}else{
    		$id = $this->getInput('id');
    		$tmp = array();
    		$webroot = Common::getWebRoot();
    		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
    		if(!$id || !$game)  $this->Output(0, '', $tmp);
    		$tmp['sign'] = 'GioneeGameHall';
    		$tmp['gameName'] = $game['name'];
    		$tmp['content'] = '游戏大厅的'.'"'.$game['name'].'"'.'很好玩哦'.','.$webroot.'/index/detail/?id='.$id.'&intersrc=shareH5&t_bi='.self::getSource();
    		$tmp['gameScreeshotUrl'] = $game['simgs'][0];
    		$tmp['gameIconUrl'] = $game['img'];
    		$tmp['url'] = $webroot.'/index/detail/?id='.$id.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    		$this->Output(0, '', $tmp);
    	}
    	
    }
    
    /**
     * 分享bbs
     * @param unknown_type $type
     * @param unknown_type $id
     */
    private function shareBbsDetail($type, $id){
    	$tmp = array();
    	$tmp['success'] = true;
    	$tmp['msg'] = '';
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['data'] =  '';
    	//http://bbs.amigo.cn/forum.php?mod=viewthread&tid=10168
    	//http://bbs.amigo.cn/thread-22-1-1.html
    	//写日志
    	//取得传递的url参数，且转码
    	$url = urldecode(html_entity_decode($id)) ;
    	$attachroot = Common::getAttachPath();
    	//处理论坛的分享
    	if(stripos($url, 'amigo.cn')){
    		//$log_data ='测试结果2'.stripos($url, 'amigo.cn');
    		//Common::WriteLogFile($path, $file_name, $log_data);
    		//论坛的接口地址
    		$bbs_url = Game_Service_Config::getValue('bbs_share_url');
    		$bbs_bimg = Game_Service_Config::getValue('bbs_share_bimg');
    		$bbs_simg = Game_Service_Config::getValue('bbs_share_simg');
    		$temp = parse_url($url);
    		//分析url有参数的 如 http://bbs.amigo.cn/forum.php?mod=viewthread&tid=10168
    		if(stripos($url,'viewthread')){
    			//解析url的参数，取得帖子的id
    			$param_arr= $this->_convertUrlQuery($temp['query']);
    			$tid = $param_arr['tid'];
    			//非法
    			if(!$tid) $this->clientOutput($tmp);
    			//发送请求，取得论坛贴子的标题
    			$http =  new Util_Http_Curl($bbs_url);
    			$data['act']= 'get-thread-info';
    			$data['tid']= $tid;
    			$rs = json_decode($http->get($data),TRUE);
    			//帖子内容取得成功
    			if($rs['success']){
    				$tmp['data']['gameName'] = $rs['data']['title'];
    				$tmp['data']['content'] = html_entity_decode($rs['data']['title']).','.$rs['data']['url'];
    				$tmp['data']['gameScreeshotUrl'] = $attachroot.$bbs_simg;
    				$tmp['data']['gameIconUrl'] = $attachroot.$bbs_bimg;
    				$tmp['data']['url'] = $rs['data']['url'];
    				$tmp['data']['type'] = $type;
    				$tmp['data']['id'] = html_entity_decode($id);
    				$this->clientOutput($tmp);
    			}else{
    				$this->clientOutput($tmp);
    			}
    			//解析论坛的伪静态地址	如 http://bbs.amigo.cn/thread-10168-1-1.html
    		}elseif(stripos($url,'thread')){
    			//解析url的参数，取得帖子的id
    			$temp = parse_url($url);
    			$tid_arr = explode('-', $temp['path']);
    			$tid = $tid_arr[1];
    			//非法
    			if(!$tid) $this->clientOutput($tmp);
    			//能取得tid
    			if($tid){
    				//发送请求，取得论坛贴子的标题
    				$http =  new Util_Http_Curl($bbs_url);
    				$data['act']= 'get-thread-info';
    				$data['tid']= $tid;
    				$rs = json_decode($http->get($data),TRUE);
    				//帖子内容取得成功
    				if($rs['success']){
    					$tmp['data']['gameName'] = $rs['data']['title'];
    					$tmp['data']['content'] = html_entity_decode($rs['data']['title']).','.$rs['data']['url'];
    					$tmp['data']['gameScreeshotUrl'] = $attachroot.$bbs_simg;
    					$tmp['data']['gameIconUrl'] = $attachroot.$bbs_bimg;
    					$tmp['data']['url'] = $rs['data']['url'];
    					$tmp['data']['type'] = $type;
    					$tmp['data']['id'] = html_entity_decode($id);
    					$this->clientOutput($tmp);
    				}else{
    					$this->clientOutput($tmp);
    				}
    			}
    		}else{
    			$this->clientOutput($tmp);
    		}
    	}else{
    		$this->clientOutput($tmp);
    	}
    }
    
    /**
     *分享到活动详情
     * @param unknown_type $type
     * @param unknown_type $id
     */
    private function shareActivityDetail($type, $id){
    	$tmp = array();
    	$tmp['success'] = true;
    	$tmp['msg'] = '';
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['data'] =  '';
    	$activity =Client_Service_Hd::getHd($id);
    	if(!$id || !$activity)  $this->clientOutput($tmp);
    	$sp = $this->getInput('sp');
    	$webroot = Common::getWebRoot();
    	$attachroot = Common::getAttachPath();
    	$bbs_simg = Game_Service_Config::getValue('bbs_share_simg');
    	$tmp['data']['gameName'] = $activity['title'];
    	$tmp['data']['content'] = '游戏大厅的活动'.'"'.html_entity_decode($activity['title']).'"'.'开始哦'.','.$webroot.'/client/Activity/addetail/?id='.$id.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    	$tmp['data']['gameScreeshotUrl'] = $attachroot.$bbs_simg;
    	$tmp['data']['gameIconUrl'] = $activity['img'];
    	$tmp['data']['url'] = $webroot.'/client/Activity/addetail/?id='.$id.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    	//1.5.5版本
    	$clientVersion = Common::parseSp($sp, 'game_ver');
    	if(strnatcmp($clientVersion, '1.5.5') >= 0){
    		$uuid = $this->getInput('puuid');
    		$uame = $this->getInput('uname');
    		$clientId = $this->getInput('clientId');
    		$imei = $this->getInput('imei');
    		$serverId = $this->getInput('serverId');
    		$rs = Common::verifyClientEncryptData($uuid, $uame, $clientId);
    		if($rs){
    			$ret = $this->inspectorIsValidRequest($clientVersion, $uuid, $uame, $imei, $serverId);
    			if($ret) {
    				$uuidEncript = rawurlencode(Common::encrypt($uuid,'ENCODE'));
    				$tmp['data']['content'] = '游戏大厅的活动'.'"'.html_entity_decode($activity['title']).'"'.'开始哦'.','.$webroot.'/client/Activity/addetail/?id='.$id.'&uuid='.$uuidEncript.'&clientVersion='.$clientVersion.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    				$tmp['data']['url'] = $webroot.'/client/Activity/addetail/?id='.$id.'&uuid='.$uuidEncript.'&clientVersion='.$clientVersion.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    				$this->saveDailyTaskLog(Util_Activity_Context::CONTENT_TYPE_SHARE_ACTIVITY, $id, $uuid);
    			}
    		}
    	}
    	$this->clientOutput($tmp);
    }
    
    private function inspectorIsValidRequest($clientVersion, $uuid, $uame, $imei, $serverId) {
    	if(strnatcmp($clientVersion, '1.5.7') < 0 ) {
    		return true;
    	} else {
    		if(!$serverId){
    			return false;
    		}
    		
    		$apiName = strtoupper('share');
    		$imeiDecrypt = Util_Imei::decryptImei($imei);
    		$verifyInfo = array();
    		$verifyInfo['apiName'] = $apiName;
    		$verifyInfo['puuid'] = $uuid;
    		$verifyInfo['uname'] = $uame;
    		$verifyInfo['imei'] = $imeiDecrypt;
    		$verifyInfo['version'] = $clientVersion;
    		$verifyInfo['serverId'] = $serverId;
    		$ret = $this->verifyEncryServerId($verifyInfo);
    		if(!$ret){
    			return false;
    		}
    		
    		return true;
    	}
    }
    
    private function verifyEncryServerId($verifyInfo) {
    	$keyParam = array(
    			'apiName' => $verifyInfo['apiName'],
    			'imei' => $verifyInfo['imei'],
    			'uname' => $verifyInfo['uname'],
    	);
    	$ivParam = $verifyInfo['puuid'];
    	$serverId = $verifyInfo['serverId'];
    	$serverIdParam = array(
    			'clientVersion' => $verifyInfo['version'],
    			'imei' => $verifyInfo['imei'],
    			'uname' => $verifyInfo['uname'],
    	);
    	return Util_Inspector::verifyServerId($keyParam, $ivParam, $serverId, $serverIdParam);
    }
    
    /**
     * 分享到游戏详情
     * @param unknown_type $type
     * @param unknown_type $id
     */
    private function shareGameDetail($type, $id){
    	$webroot = Common::getWebRoot();
    	$tmp = array();
    	$tmp['success'] = true;
    	$tmp['msg'] = '';
    	$tmp['sign'] = 'GioneeGameHall';
    	$tmp['data'] =  '';
    	$sp = $this->getInput('sp');
    	$game = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
    	if(!$id || !$game)  $this->clientOutput($tmp);
    	$tmp['data']['gameName'] = $game['name'];
    	$tmp['data']['content'] = '游戏大厅的'.'"'.html_entity_decode($game['name']).'"'.'很好玩哦'.','.$webroot.'/index/detail/?id='.$id.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    	$tmp['data']['gameScreeshotUrl'] = $game['simgs'][0];
    	$tmp['data']['gameIconUrl'] = $game['img'];
    	$tmp['data']['url'] = $webroot.'/index/detail/?id='.$id.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    	//1.5.5版本 
    	$clientVersion = Common::parseSp($sp, 'game_ver');
    	if(strnatcmp($clientVersion, '1.5.5') >= 0){
    		$uuid = $this->getInput('puuid');
    		$uname = $this->getInput('uname');
    		$clientId = $this->getInput('clientId');
    		$rs = Common::verifyClientEncryptData($uuid, $uname, $clientId);
    		$logData = 'uuid='.$uuid.',uname='.$uname.', clientId='.$clientId.',rs='.$rs;
    		if($rs){
    			$uuidEncript = rawurlencode(Common::encrypt($uuid,'ENCODE'));
    			$tmp['data']['content'] = '游戏大厅的'.'"'.html_entity_decode($game['name']).'"'.'很好玩哦'.','.$webroot.'/index/detail/?id='.$id.'&uuid='.$uuidEncript.'&clientVersion='.$clientVersion.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();
    			$tmp['data']['url'] = $webroot.'/index/detail/?id='.$id.'&uuid='.$uuidEncript.'&clientVersion='.$clientVersion.'&isShare=1&intersrc=shareH5&t_bi='.self::getSource();	  		
    			$this->saveDailyTaskLog(Util_Activity_Context::CONTENT_TYPE_SHARE_GAME, $id, $uuid);
    		}
    	}
    	$this->clientOutput($tmp);
    }
    public function _havePackageAction() {
    	/*
    	$info = $this->getInput(array('apk_package', 'imei'));
    	$tmp = array();
    	$tmp['id'] = '';
    	$tmp['m_id'] =  crc32($info['imei']);
    	$tmp['imei'] =  $info['imei'];
    	$tmp['package'] =  $info['apk_package'];
    	$ret = Client_Service_Imei::replaceImei($tmp);
    	*/
    }
    
    /**
     * 广告位3过滤已经安装的APK
     */
    private function havePackage() {
    	//推荐专题
    	$game_ids = $params = array();
    	$imei = Util_Cookie::get('imei', true);
    	$info = Client_Service_Imei::getImeiByImei(crc32($imei));
    	$packages = explode('|',$info['package']);
    	$params['package'] = array("IN", $packages);
    	//get resource games
    	list($total, $games) = Resource_Service_Games::getList(1, 100, $params);
    	
    	$games = Common::resetKey($games, 'id');
    	$game_ids = array_unique(array_keys($games));
    	return $game_ids;
    }
    
    public function webIndexiAction() {
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	//最新游戏
    	list($total, $resource_games) = Resource_Service_Games::getList($page, $this->perpage, array('status'=>1), array('online_time'=>'DESC'));
    	$total = Game_Service_Config::getValue('game_rank_newnum');
    
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$i = 1;
    	foreach($resource_games as $key=>$value) {
    		$rank_games[$key] = array_merge($resource_games[$key], Client_Service_IndexAdI::cookAd($value, "Newrelease", (($page - 1) * $this->perpage)+ $i++));
    	}
    	return $this->_jsonData($rank_games, 'Newrelease' ,$page, $hasnext);
    		
    }
    
    private  function _jsonData($ads, $name, $page=1, $hasnext=false) {
    	$attachPath = Common::getAttachPath();
    	$data = $imgs=  array();
    	$i= 0;
    	foreach ($ads as $key=>$value) {
    		if($value['data-Info']){   //如果没有数据不显示
	    		$data[$i]['img']  = $value['img'];
	    		if($value['icon']){
	    			$data[$i]['icon']  = $attachPath. $value['icon'];
	    			$imgs[] = $attachPath . $value['icon'];
	    		}
	    		if($value['title']){
	    			$data[$i]['title']  = $value['title'];
	    		}
	    		$data[$i]['data-infpage'] =  $value['data-Info'];
	    		$data[$i]['resume'] =  $value['resume'];
    			$data[$i]['name'] =  $value['name'];
    			$data[$i]['size'] =  $value['size']."M";
    			$data[$i]['category'] =  $value['category'];
    			$data[$i]['hot'] =  $value['hot'];
	    		$data[$i]['data-type'] =  $value['data-type'];
	    		$data[$i]['attach'] =  $value['attach'];
	    		$data[$i]['device'] = $value['device'];
	    		$i++;
	    		$imgs[] = $value['img'];
    		}
    	}
    	$this->cache($imgs, $name);
    	if($name != 'ad4'){
    		$this->output(0, '', $data);
    	} else {
    		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    	}
    	
    }
    
    private function _convertUrlQuery($query)
    {
    	$queryParts = explode('&', $query);
    	$params = array();
    	foreach ($queryParts as $param)
    	{
    		$item = explode('=', $param);
    		$params[$item[0]] = $item[1];
    	} 
    	return $params;
    }
    
    /**
     * 记录
     * @param unknown_type $type
     * @param unknown_type $gameId
     * @param unknown_type $uuid
     */
    private function saveDailyTaskLog($contentType, $gameId, $uuid){
    	
    	$cache = Cache_Factory::getCache();
    	$dailyLimit = intval($cache->hGet('dailyTask4', 'dailyLimit'));
    	//取出对应每日任务的分享是否开启
    	if($dailyLimit == 0){
    		return false;
    	}
    	
    	$cacheHash = $uuid.'_user_info' ;
    	//每天完成任务次数与时间
    	$cacheFinishNumKey     = 'finishDailyTaskNum4';
    	$cacheFinishTimeKey    = 'finishDailyTaskTime4';
    	$finishDailyTaskNum    = $cache->hGet($cacheHash, $cacheFinishNumKey);
    	$finishDailyTaskTime   = $cache->hGet($cacheHash, $cacheFinishTimeKey);
    	//任务已经完成到达一定次数
    	$days = Common::diffDate($finishDailyTaskTime,  date('Y-m-d H:i:s'));
    	if($days == 0  && $finishDailyTaskNum >= $dailyLimit){	
    		return false;
    	}
    	
    	$params['task_id'] = Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID ;
    	$params['uuid'] = $uuid ;
    	$params['game_id'] = $gameId ;
    	$params['content_type'] = $contentType ;
    	$time = Common::getTime();
    	$params['create_time'] = array(array('>=', strtotime(date('Y-m-d 00:00:01')) ),array('<=', strtotime(date('Y-m-d 23:59:59')))) ;
    	$logRs = Client_Service_DailyTaskLog::getBy($params);
        if($logRs){
        	return false;
        }
    	$data['task_id'] = Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID;
    	$data['uuid'] = $uuid;
    	$data['content_type'] = $contentType;
    	$data['game_id'] = $gameId;
    	$data['create_time'] = $time;
    	$data['status'] = 0;
    	Client_Service_DailyTaskLog::insert($data);
    
    }
}