<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 免流量接口文件
 * @author master
 *
 */
class FreedlController extends Api_BaseController {
	
	public $perpage = 10;
	
	/**
	 * 免流量专区首页

	 */
	public function indexAction(){
		
	}
	
	private function _checkAllow($imsi){
		if(!$imsi) return false;
		//功能开关状态
		$status = Game_Service_Config::getCacheValue('module_freedl_status');
		if(!is_null($status) && intval($status) == 1) return true;
		//用户判断
		$allowImsi = Game_Service_Config::getCacheValue('module_freedl_allow_imsi');
		if(!is_null($allowImsi) && $allowImsi){
			$allowImsiArr = explode('|', $allowImsi);
			if(in_array($imsi, $allowImsiArr)) return true;
		}
		return false;
	}
	
	
	/**
	 * 权限检测
	{
    "success":true,
    "msg":"",
    "sign":"GioneeGameHall",
    "data":{
            "imsi" : "89860xxx", //按客户端传入的参数值返回
            "type"  : 0/1, //0代表专区免流量，1代表全站免流量
            "permission" : 0/1/ //0代表无权限，1代表有权限
            "blacklist" : 0/1  //0代表非黑名单，1代表黑名单
            "activityId" : "100" //活动id
            "receive"  : 0/1, //0未领取、1领取
            "activityTime" : "11月1日-11月31日" //后台配置
            "activityContent" : "在免流量专区内下载任何游戏都不会消耗您的手机流量，赶快领取权限吧！" //后台配置
            "activityEndTime" : "1413179444106" //结束时间戳
            "serverTime" : "1413179444106" // 当前时间戳
            "serverFlowSize" : "100" //当前imsi历史下载流量，单位为M
            "freedl"=>'' //cu19 地区运营商编码
 
    }
	 * 
	 */
	public function checkpermissionAction(){
		$request = $this->getInput(array('imsi', 'uname', 'uuid', 'imei', 'client_pkg', 'sp'));
		//初始化数组
		$response = array(
				"imsi" => $request['imsi'],
				"type" => '0',
				"permission" => '0',
				"blacklist" => '0',
				"activityId" => '',
				"receive" => '0',
				"imgUrl" => '',
				"activityTime" => '',
				"activityContent" => '',
				"activityEndTime" => '',
				"serverTime" => Common::getTime(),
				"serverFlowSize" => '0',
				"freedl"=>''
		);
		if(!$request['imsi']) $this->localOutput('','',$response);
		//免流量允许开启的用户
		$imsi = $request['imsi'];
		$flag = $this->_checkAllow($imsi);
		//根据后台控制无权限
		if(!$flag) $this->localOutput('','',$response);
		
		//检查活动是否有效
		$info = Freedl_Service_Permission::checkActivity($request['imsi']);
		if(!$info) $this->localOutput('','',$response);
		
		//判断是否是黑名单
		$b_ret = Freedl_Service_Permission::checkBlacklist($request['imsi'], $request['imei']);
		if($b_ret) {
			$response['blacklist'] = '1';
			$this->localOutput('','',$response);
		} 
		
		//判断是否已经领取过
		//$r_ret = Freedl_Service_Permission::checkReceive($request['imsi'], $info['id']);
		$r_ret = Freedl_Service_Receive::getBy(array('imsi'=>$request['imsi'],'activity_id'=>$info['id']));
		//如果没有领过添加记录
		if(!$r_ret)  $ret = Freedl_Service_Permission::addReceive($request, $info['id']);
		
		//查找当前用户流量总消耗
		//$consum = Freedl_Service_Process::getFreedlUserCache($request['imsi']);
		$consum = Freedl_Service_Usertotal::getBy(array('imsi'=>$request['imsi'],'activity_id'=>$info['id']));
		$total_consume = ($consum) ? $consum['total_consume'] : '0';
		
		$response['type'] = ($info['htype'] == 1 ? '0' : '1');
		$response['activityId'] = $info['id'];
		$response['permission'] = '1';
		$response['blacklist'] = '0';
		$response['receive'] = ($r_ret['status'] && $r_ret) ? '1' : '0' ;
		$response['imgUrl'] = Common::getAttachPath() . $info['img'];
		$response['activityTime'] = date("Y.m.d", $info['start_time']).'-'.date("Y.m.d", $info['end_time']);
		$response['activityContent'] = html_entity_decode($info['explain']);
		$response['activityEndTime'] = $info['end_time'];
		$response['serverFlowSize'] = $total_consume;
		$response['freedl'] = $info['imsiCode'];
	
		$this->localOutput(0, '', $response);
	}

	/**
	 * 黑名单检测
	{
	    "success":true,
	    "msg":"",
	    "sign":"GioneeGameHall",
	    "data":{
	            "imsi" : "89860xxx", //按客户端传入的参数值返回
	            "blacklist" : 0/1  //0代表非黑名单，1代表黑名单
	    }
	}
	*/
	public function checkblacklistAction(){
		$request = $this->getInput(array('imsi', 'uuid', 'imei'));
		
		//初始化数组
		$response = array(
				"imsi" => $request['imsi'] ? $request['imsi'] : '',
				"blacklist" => '0',
		);
		if(!$request['imsi']) $this->localOutput('','',$response);
		$b_ret = Freedl_Service_Permission::checkBlacklist($request['imsi'], $request['imei']);
		$response['blacklist'] = $b_ret ? '1' : '0' ;
		$this->localOutput(0, '', $response);
	}
	
	/**
	 * 领取权限
	 {
    "success":true,
    "msg":"",
    "sign":"GioneeGameHall",
    "data":{
             "imsi" : "89860xxx", //按客户端传入的参数值返回
             "receive"  : 0/1, //0未领取、1领取
             "permission" : 0/1/ //0代表无权限，1代表有权限
             "blacklist" : 0/1  //0代表非黑名单，1代表黑名单
    	}
	}
	 */
	public function getpermissionAction(){
		$request = $this->getInput(array('imsi', 'activityId', 'imei', 'uname', 'uuid', 'client_pkg', 'sp'));
		
		//初始化数组
		$response = array(
				"imsi" => $request['imsi'],
				"activityId" => $request['activityId'],
				"receive" => '0',
				"permission" => '1',
				"blacklist" => '0',
		);
		
		if(!$request['imsi'] && !$request['uuid']) $this->localOutput('','',$response);

		//检查活动是否有效
		$info = Freedl_Service_Permission::checkActivity($request['imsi'], $request['activityId']);
		if(!$info){
			$response['permission'] = '0';
			$this->localOutput('','',$response);
		}
		
		//判断是否是黑名单
		$b_ret = Freedl_Service_Permission::checkBlacklist($request['imsi'], $request['imei']);
		if($b_ret) {
			$response['blacklist'] = '1';
			$response['permission'] = '0';
			$this->localOutput('','',$response);
		}
		
		//判断是否已经领取过
		//$r_ret = Freedl_Service_Permission::checkReceive($request['imsi'], intval($request['activityId']));
		$r_ret = Freedl_Service_Receive::getBy(array('imsi'=>$request['imsi'],'activity_id'=>intval($request['activityId'])));
		//更新领取状态[成功领取时，状态置为1]
		if(!$r_ret['status'] && $r_ret) {
			$params = array();
			$userInfo = Account_Service_User::getUserInfo(array('uname'=>$request['uname']));
			$sp_arr = Freedl_Service_Permission::getConvertSpData($request['sp']);
			//同步更新原来imsi对应该活动的uuid等信息
			$params['status'] = 1;
			$params['uuid'] = $request['uuid'];
			$params['uname'] = $request['uname'];
			$params['imei'] = $request['imei'];
			$params['nickname'] = $userInfo['nickname'];
			$params['model'] = $sp_arr[0];
			$params['version'] = $sp_arr[1];
			$params['sys_version'] = $sp_arr[2];
			$params['client_pkg'] = $request['client_pkg'];
			$params['receive_time'] = Common::getTime();
			$ret = Freedl_Service_Receive::updateBy($params,array('imsi'=>$request['imsi'],'activity_id'=>intval($request['activityId'])));
		}
		
		
		$response['receive'] = ((!$r_ret['status'] && $ret) || $r_ret['status']) ? '1' : '0' ;
		$response['permission'] = '1';
		$response['blacklist'] = '0';
		
		$this->localOutput(0, '', $response);
	}
	
	/**
	 * 获取免流量apk地址
	{
    "success":true,
    "msg":"",
    "sign":"GioneeGameHall",
    "data":{
        "imsi" : "89860xxx", //按客户端传入的参数值返回
        "gameId" : "1862", //按客户端传入的参数值返回
        "activityId" : "2", // 活动id按客户端传入的参数值返回
        "downUrl"  : "http://s.game.3gtest.gione", //免流量下载地址，如果没权限或者活动结束返回正常下载地址
        "permission" : 0/1/ //0代表无权限，1代表有权限
        "blacklist" : 0/1  //0代表非黑名单，1代表黑名单
        "type"  : 0/1/2, //0代表移动免流量，1代表联通免流量, 2代表非免流量地址
    	}
 	}
	 */
	public function getapkAction(){
		$request = $this->getInput(array('imsi', 'gameId', 'uuid', 'activityId', 'diff_version','imei'));
		$activityId = intval($request['activityId']);
		$gameId = intval($request['gameId']);
		$version = trim($request['diff_version']);
		
		//初始化数组
		$response = array(
				"imsi"=>$request['imsi'],
				"gameId" => $request['gameId'],
				"activityId" => $request['activityId'],
				"permission" => '0',
				"blacklist" => '0',
				"downUrl" => '',
		);
		//检查活动是否有效
		$hinfo = Freedl_Service_Permission::checkActivity($request['imsi'], $activityId);
		//活动下线
		if(!$hinfo) {
			$response['activityId'] = '';
			$this->localOutput(0, '', $response);
		}
		
		//判断是否是黑名单
		$b_ret = Freedl_Service_Permission::checkBlacklist($request['imsi'], $request['imei']);
		if($b_ret) {
			$response['blacklist'] = '1';
			$this->localOutput(0, '', $response);
		}
		
		
		//在免流量活动有权限并且不是黑名单
		$response['permission'] = '1';
		$response['blacklist'] = '0';
		
		$info = Resource_Service_Games::getGameAllInfo(array('id'=>$gameId));
		//检查游戏id是否是指定活动中专区免流量类型的游戏（从缓存中查找）
		if(!$info){
			$response['downUrl'] = '';
			$this->localOutput(0, '', $response);
		}
		//活动中免流量游戏识别		
		$flag = 0;
		if($info['freedl']) {
			$freedls = explode('|',$info['freedl']);
			foreach($freedls as $k=>$v){
				$tmp[] = end(explode('_',$v));
			}
			if(in_array($activityId, $tmp)) $flag = 1;
		}

		if(!$info['freedl'] || !$flag){
			$response['gameId'] = '0';
			$response['downUrl'] = '';
			$this->localOutput(0, '', $response);
		}
		
		//移动运营商
// 		if($hinfo['htype'] == 1){
// 			//检查是否有差分包
// 			if($version){
// 				$curr_version =Resource_Service_Games::getIdxVersionGetBy(array('game_id'=>$gameId,'version'=> $version));
// 				$diffs = Resource_Service_Games::getByIdxDiff(array('game_id'=>$gameId,'version_id'=> $info['version_id'],'object_id'=> $curr_version['id']));
// 				$response['downUrl'] = $this->_getUrlData($diffs['link']);
// 				$this->localOutput(0, '', $response);
// 			}
			
// 			//没有差分包，直接替换为移动免流量地址
// 			$response['downUrl'] = $this->_getUrlData($info['link']);
// 			$this->localOutput(0, '', $response);
// 		}
		
		//联通运营商
		if($hinfo['htype'] == 2){
			//上线中的联通游戏内容
			$freedlUrl = '';
			$freedlUrl = $this->_getFreedlUrl($gameId);
			//如果此时联通下线的话，直接返回
			if(!$freedlUrl){
				$response['gameId'] = '0';
				$response['downUrl'] = '';
				$this->localOutput(0, '', $response);
			}
			$response['downUrl'] = $freedlUrl ? $freedlUrl: $info['link'] . '?flag=cugd';
			
			$this->localOutput(0, '', $response);
		}
	}
	
	private function _getFreedlUrl($gameId){
		$url='';
		$cuGame = Freedl_Service_Cugd::getBy(array('game_id'=>$gameId, 'online_flag'=>1, 'game_status'=>1, 'cu_status'=>array('in', array('2', '4'))));
		if(!$cuGame) return $url;
		$ret = Api_Freedl_Cu_Gd::getdownloadurl($cuGame['content_id']);
		if($ret['errcode']==0){
			$url = $ret['resultData']['url'];
		}
		$url = $url ? $url : Api_Freedl_Cu_Gd::getmllurl($cuGame['content_id']);
		return $url;
	}
	
	/**
	 * 替换为移动免流量地址
	 */
	private function _getUrlData($url) {
		if(!$url) return '';
		$parts = parse_url($url);
		return preg_replace('/'.$parts['host'].'/i', "gd.mll.game.3gtest.gionee.com", $url);
	}
	
	/**
	 * 免流量反馈
	   {
	    "success":true,
	    "msg":"",
	    "sign":"GioneeGameHall",
	    "data":{
	            "imsi" : "89860xxx", //按客户端传入的参数值返回
	            "result" : 0/1, //0不成功、1成功
	    }
	   }
	 */
	public function feedbackAction(){
		$sp = $this->getInput('sp');
		$imei = $this->getInput('imei');
		$uname = $this->getInput('uname');
		$title = $this->getInput('content');
		$uuid = $this->getInput('uuid');
		$contact = $this->getInput('contact');
		$client_pkg = $this->getInput('client_pkg');
		
	    $spArr = Common::parseSp($sp);
		$imei = $spArr['imei'];
		if($imei){
			$imeicrc = crc32($imei);
		} 
		//获取机型
		$mode = $spArr['device'];
		$clientVertion = $spArr['game_ver'];
		$sysVersion = $spArr['android_ver'];
		$tmp['result'] = false;
		if(!$imei && (!$uname || !$uuid )){
			$this->clientOutput(array());
		}
		if($title) {	
			//提交信息
			$info = array(
					'content'=>$title,
					'uuid'=>$uuid,
					'uname'=> $uname,
					'imei'=> $imei,
					'imcrc'=> crc32($imei),
					'create_time'=>Common::getTime(),
					'model'=>$mode,
					'client_version'=>$clientVertion,
					'sys_version'=>$sysVersion,
					'contact'=>$contact,
					'status'=>0,
					'client_pkg'=>$client_pkg
			);
		}
		$ret  = Feedback_Service_Feedback::add($info);
		if(!$ret) $this->clientOutput($tmp);
		$tmp['result'] = true;
		$this->localOutput('','',$tmp);
	}
	
	/**
	 * 免流量专区列表 第一页数据
	 {
	 "success":true,
	 "msg":"",
	 "sign":"GioneeGameHall",
	 "data":{
	 "imsi" : "89860xxx", //按客户端传入的参数值返回
	 "type"  : 0/1, //0代表专区免流量，1代表全站免流量
	 "permission" : 0/1/ //0代表无权限，1代表有权限
	 "blacklist" : 0/1  //0代表非黑名单，1代表黑名单
	 "receive"  : 0/1, //0未领取、1领取
	 "areaImageUrl"  : "http://s.game.3gtest.gione",  //专区顶部图片url
	 "listGameUrl" : "url" //列表数据
	 "listData" : "json" // 第一页数据
	
	 }
	 *
	 */
	public function freeInfoAction(){
		$id = intval($this->getInput('activityId'));
		$uname = $this->getInput('uname');
		$uuid = $this->getInput('uuid');
		$imsi = $this->getInput('imsi');
		
		if(!$id) $this->localOutput('','',array());
		
		//检测专区免流量活动是否有
		$params =  $temp = array();
		$params['id'] = $id;
		$params['status'] = 1;
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		$info = Freedl_Service_Hd::getByFreedl($params, array('id'=>'DESC','start_time'=>'DESC'));
		if(!$info) $this->localOutput('','',array());
		
		//有免流量活动
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Freedl/freeList?id='.$id);

		//检查用户是否领取过
		$receinfo = Freedl_Service_Permission::checkReceive($imsi, $id);
		$data = $this->_freedlList(1, $id);
		
		$temp['areaImageUrl'] = Common::getAttachPath() . $info['f_img'];
		$temp['listGameUrl'] = $href;
		$temp['activityId'] = $id;
		$temp['receive'] = $receinfo ? 1 : 0;
		
		header("Content-type:text/json");
		$freeList = json_encode(array(
				'success' => $data  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'data' => $data,
		));
		$temp['listData'] = $freeList;
		$temp['totalCount'] = $data['totalCount'];
		
		$this->localOutput('','',$temp);
	}
	
	/**
	 * 免流量专区列表
	 */
	public function freeListAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		$data = $this->_freedlList($page, $id);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 免流量专区列表组装
	 * @param unknown_type $page
	 * @param unknown_type $params
	 */
	private  function _freedlList($page, $id) {
		if(!$id) return '';
		$tmp = array();
		list($total, $games) = Freedl_Service_Hd::getFreedList($page, $this->perpage, array('freedl_id'=>$id,'status'=>1));
		foreach($games as $key=>$value){
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			
			//附加属性处理,1:礼包
			$attach = array();
			if (Client_Service_IndexAdI::haveGiftByGame($value['game_id'])) array_push($attach, '1');
			
			$tmp[] = array(
					'img'=>urldecode($info['img']),
					'name'=>html_entity_decode($info['name']),
					'resume'=>html_entity_decode($info['resume']),
					'package'=>$info['package'],
					'link'=>$info['link'],
					'gameid'=>$info['id'],
					'size'=>$info['size'].'M',
					'category'=>$info['category_title'],
					'attach' => ($attach) ? implode(',', $attach) : '',
					'hot' => Resource_Service_Games::getSubscript($info['hot']),
					'viewType' => 'GameDetailView',
					'score' => $info['client_star'],
					'freedl' => $info['freedl']
			);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
	}
	
	/**
	 * 获取服务器当前时间戳
	 */
	public function serverTimeAction() {
		$version = $this->getInput('version');
		$tmp['serverTime'] = Common::getTime();
		$this->localOutput('','',$tmp);
	}
	
	/**
	 * 联通通知游戏大厅联通资源内容变更接口。
	 * 资源内容如上线，下线，新审核通过，审核不通过。
	 * 
	 */
	public function cunotifyAction(){
		header("Content-type:text/json");
		$request = $this->getInput(array('requestType','pKey','tm','contentId','code'));
		Common::log(array('time'=>time(), 'action'=>'cunotify', 'request'=>$request), date('Y-m-d') . '_cunotify.log');
		$ret = array(
				'requestData'=> array(
					'id'=>	$request['contentId'],
					'pKey'=> $request['pKey'],
					'tm'=> $request['tm'],
					'code'=> $request['code']	
			)
		);
 
		$code = $request['code'];
		unset($request['code']);
		$sign = Api_Freedl_Cu_Gd::sign($request);
		if($sign != $code){ //签名失败
			$ret['errcode'] = 5;
			$ret['msg'] = 'ERR: Parameter signature invalid';
			exit(json_encode($ret));
		}
		//要处理联通免流量数据存在
		$cuData = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId']));
		if($cuData){
			//业务处理逻辑--start
			switch ($request['requestType']){
				case 'newchecked':
					//审核通过
					//查找指定contentId上线标识状态online_flag=0的记录，修改对应数据并发送上线请求到联通。
					$newCugame = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId'], 'online_flag'=>0));
					if($newCugame){
						//获取游戏内容库最新游戏数据,设置最新的游戏内容状态
						$game = Resource_Service_Games::getGameAllInfo(array('id'=> $newCugame['game_id']), false, false);
						//联通审核通过的上线版本跟在线上的版本相同才能时内容库处于上线状态
						$game_status = ($game['version_code'] == $newCugame['version_code']) ? 1 : 0;
						
						//首先查找contentId上线标识状态online_flag=1的记录，记录id
						$onlineCugame = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId'], 'online_flag'=>1));
						if($onlineCugame){
								//需要变更的数据
								$data = array(
										'version' => $newCugame['version'],
										'version_code' => $newCugame['version_code'],
										'cu_online_time' => Common::getTime(),
										'cu_status' => 2, //审核通过
										'game_status' => $game_status,
										'create_time' => $newCugame['create_time']
								);
								//更新线上数据版本信息为上线
								Freedl_Service_Cugd::updateCugd($data, array('id'=>$onlineCugame['id']));
								//清理掉进行审核的数据,保持之前免流量游戏的状态
								Freedl_Service_Cugd::deleteCugd(array('id'=>$newCugame['id']));
							}else{
							//更新待审核的记录为通过审核状态
							Freedl_Service_Cugd::updateCugd(array('cu_status'=>2, 'online_flag'=>1, 'game_status' => $game_status, 'cu_online_time'=>Common::getTime()), array('id'=>$newCugame['id']));
						}
					}
					break;
				case 'checkfailed':
					//审核不通过
					//查找指定contentId上线标识状态online_flag=0的记录，修改对应数据。
					$cugame = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId'], 'online_flag'=>0));
					//更新联通资源状态为审核不通过
					if ($cugame['id']) Freedl_Service_Cugd::updateCugd(array('cu_status'=>3), array('id'=>$cugame['id']));
					
					break;
				case 'valid':
					//上线
					//首先查找contentId上线标识状态online_flag=1的记录
					$cugame = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId'], 'online_flag'=>1));
					//更新联通资源状态为上线，游戏记录为上线状态
					if ($cugame['id']) {
						//获取游戏内容库最新游戏数据,设置最新的游戏内容状态
						$game = Resource_Service_Games::getGameAllInfo(array('id'=> $cugame['game_id']), false, false);
						//联通审核通过的上线版本跟在线上的版本相同才能时内容库处于上线状态
						$game_status = ($game['version_code'] == $cugame['version_code']) ? 1 : 0;
						Freedl_Service_Cugd::updateCugd(array('cu_status'=>4, 'game_status' => $game_status), array('id'=>$cugame['id']));
					}
					break;
				case 'invalid':
					//下线
					//首先查找contentId上线标识状态online_flag=1的记录
					$cugame = Freedl_Service_Cugd::getBy(array('content_id'=>$request['contentId'], 'online_flag'=>1));
					//更新联通资源状态为下线，游戏记录为下线状态
					if ($cugame['id']) Freedl_Service_Cugd::updateCugd(array('cu_status'=>5), array('id'=>$cugame['id']));
					break;
			}
		}
		//业务处理逻辑--end
		
		exit(json_encode(array('errcode'=>'0', 'msg' => '成功')));
	}
	

	/**
	 * 用于测试，获取联通通知地址
	 */
	public function testAction(){
		header("Content-type: text/html; charset=utf-8");
		$act = $this->getInput('act');
		$cid = $this->getInput('cid');
		$tm = date('YmdHis');
		echo '免流量通知接口使用须知：<br/>';
		echo '地址栏输入示例：http://game.3gtest.gionee.com/api/freedl/test?act=newchecked&cid=13393386 <br/>';
		echo '参数说明：<br/>';
		echo 'act：[newchecked:审核通过|checkfailed：审核不通过|valid：上线|invalid：下线]<br/>';
		echo 'cid：[联通分配的游戏资源id]<br/>';
		if($act && $cid){
			$request = array(
					'requestType'=>$act,
					'pKey'=>'108',
					'tm'=>$tm,
					'contentId'=>$cid,
			);
			$code = Api_Freedl_Cu_Gd::sign($request);
// 			$url = 'http://game.3gtest.gionee.com/api/freedl/cunotify';
			$url = 'http://t.dev.game.gionee.com/api/freedl/cunotify';
			echo '通知地址如下：<br/>';//
			echo sprintf('%s?requestType=%s&pKey=108&tm=%s&contentId=%s&code=%s', $url, $act, $tm, $cid, $code);
		}else {
			echo '请选填写对应参数 act 跟 cid 参数';
		}
	
	}
}