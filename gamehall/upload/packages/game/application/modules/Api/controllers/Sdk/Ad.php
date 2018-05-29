<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_AdController extends Api_BaseController {
	
	public $perpage = 10;
	const SIGN = 'GameSDK';
	
	/**
	 * 最新公告
	 */
	public function newestAdAction(){
		
		$package = trim($this->getInput('gamePackage'));
		$uname = $this->getInput('account');
		$uuid = $this->getInput('uuid');
		$sp = trim($this->getInput('sp'));
		
		$data = array();
		$sign = self::SIGN;
		if(!$package){
			$this->localOutput(1, '参数错误', $data, $sign);
		}
		
		$gameInfo = $this->getGameInfoByPackage ($package );
		if(!$gameInfo['id']){
			$this->localOutput(1, '参数错误', $data, $sign);
		}
		
		$orderBy = array('sort'=>'DESC', 
		                 'start_time'=>'DESC',
		                 'id'=>'DESC');
		$adInfo = $this->getCurrentAdInfo ($gameInfo['id'], Sdk_Service_Ad::SHOW_TYPE_AD, $orderBy);

		$data['package'] = $package;
		$data['noticeId'] = $adInfo['id'];
		$data['noticeTitle'] = html_entity_decode( $adInfo['title'], ENT_QUOTES);
		$data['noticeContent'] = $this->fillStyle($adInfo['ad_content']);
	
		//问题的版本号
		$questionInfo = $this->getMyQuestionInfo ($uuid );	
		$data['versions']['helpVersion'] = intval($questionInfo['version_time']);
	
		//活动的版本号
		$orderBy = array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		$activityInfo = $this->getCurrentAdInfo ($gameInfo['id'], Sdk_Service_Ad::SHOW_TYPE_ACTIVITY, $orderBy);
		$data['versions']['activityVersion'] = intval($activityInfo['version_time']);
		$data['timeStamp'] = intval($activityInfo['version_time']);
		
		$this->localOutput(0, '', $data, $sign);
	}
	
	private function getGameInfoByPackage($package) {
		$gameParams['package'] = $package;
		$gameParams['status'] = Resource_Service_Games::STATE_ONLINE;
		$gameInfo = Resource_Service_Games::getBy($gameParams);
		return $gameInfo;
	}

	
	private function getMyQuestionInfo($uuid) {
		$questionParam['uuid'] = $uuid;
		$orderBy = array('reply_time'=>'DESC',
				         'create_time'=>'DESC',
				         'id'=>'DESC');
		$result = Sdk_Service_Feedback::getBy($questionParam, $orderBy);
		return $result;
	}

	
	private function getCurrentAdInfo($gameId, $showType, $orderBy) {
		$params = $this->getFoundParam ($gameId, $showType);
		$result = Sdk_Service_Ad::getBy($params, $orderBy);
		return $result;
		
	}
	
	private function getActivityList($gameId, $showType,  $orderBy, $page) {
		$params = $this->getFoundParam ( $gameId, $showType);
		list($total, $adList) = Sdk_Service_Ad::getList($page, $this->perpage, $params,  $orderBy);
		return array($total, $adList);
	}
	
	
	private function getFoundParam($gameId, $showType) {
		$params['show_type'] = array('LIKE', $showType);
		$currTime = Util_TimeConvert::floor(Common::getTime(), Util_TimeConvert::RADIX_HOUR);
		//$currTime = Common::getTime();
		$params['start_time'] = array('<=', $currTime);
		$params['end_time']   = array('>=',   $currTime);
		$params['status']     = Sdk_Service_Ad::AD_STATUS_OPEN;
		$info = Sdk_Service_Ad::getsBy($params);
		$ids = array();
		
		foreach ($info as $adinfo){
		    $gameIds = array();
			$gameIds = explode(',', $adinfo['game_ids']);
			if( $this->isEffectAdId(intval($gameId), $gameIds, $adinfo)){
				$ids[$adinfo['id']] = $adinfo['id'];
			}
		}		
		if($ids){
			$returnParams['id'] = array('IN', $ids);
		}else{
			$returnParams['id'] = 0 ;
		}
		
		return $returnParams;
	}

	
	private function getAdList($gameId, $showType, $orderBy) {
		$params = $this->getFoundParam ( $gameId, $showType);
		list($total, $adList) = Sdk_Service_Ad::getList(1, $this->perpage, $params,  $orderBy);
		return $adList;
	}
	
	private function isEffectAdId($gameId, $gameIds, $adInfo) {
		return (in_array($gameId, $gameIds) &&  $adInfo['join_game_type'] != Sdk_Service_Ad::JOIN_GAME_TYPE_EXCLUDE) 
		|| $adInfo['join_game_type'] == Sdk_Service_Ad::JOIN_GAME_TYPE_ALL
		|| (!in_array($gameId, $gameIds) &&  $adInfo['join_game_type'] == Sdk_Service_Ad::JOIN_GAME_TYPE_EXCLUDE);
	}

	/**
	 * 公告列表
	 */
	public function adListAction(){
		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$package = trim($this->getInput('gamePackage'));
		$uname = $this->getInput('account');
		$uuid = $this->getInput('uuid');
		$sp = trim($this->getInput('sp'));
		
		if(!$package){
			$this->clientOutput(array());
		}
		
		$data['success'] = true;
		$data['sign']    = self::SIGN;
		$gameInfo =  $this->getGameInfoByPackage ($package );
		if(!$gameInfo['id']){
			$data['noticeList'] = array();
			$this->clientOutput($data);
		}

		$orderBy = array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC');
		$adList = $this->getAdList($gameInfo['id'], Sdk_Service_Ad::SHOW_TYPE_AD, $orderBy);
		$adList = $this->formatAdList ( $adList );
		$data['noticeList'] = $adList;
		
		//问题的版本号
		$questionInfo = $this->getMyQuestionInfo ($uuid );
		$data['versions']['helpVersion'] = intval($questionInfo['version_time']);
		
		//公告的版本号
		$orderBy = array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		$result = $this->getCurrentAdInfo($gameInfo['id'], Sdk_Service_Ad::SHOW_TYPE_AD, $orderBy);
		$data['versions']['noticeVersion'] = intval($result['version_time']);
		
		//活动版本号
		$orderBy = array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		$result = $this->getCurrentAdInfo($gameInfo['id'], Sdk_Service_Ad::SHOW_TYPE_ACTIVITY, $orderBy);
		$data['versions']['activityVersion'] = intval($result['version_time']);
		
		$this->clientOutput($data);
	}
	
	private function formatAdList($adList) {
	    $webroot  =  Yaf_Application::app()->getConfig()->sdkroot;
	    $returnList = array();
		foreach ($adList as $val){	
			$url = $webroot.'/game/sdk/activityDetail/?id='.$val['id'];
			$returnList[]=array('id'=>$val['id'],
					      'name'=>html_entity_decode( $val['title'], ENT_QUOTES),
					      'url' => $url,
					      'type' =>'Activity',
					      'showPay'=> $val['is_payment']?true:false
					);
		}
		return $returnList;
	}

	
	/**
	 * 获得登陆或消费赠送的A券值
	 * @author yinjiayan
	 */
	public function ticketAction() {
	    $type = trim($this->getInput('type'));
	    $uuid = trim($this->getInput('uuid'));
	    $sp = trim($this->getInput('sp'));
	    if(!$type || !$uuid || !$sp){
	        $this->localOutput(1, '参数错误', array(), self::SIGN);
	    }
	    
	    $tickets = $this->getTicketCache($uuid, $type);    
	    $list = array();
	    foreach ($tickets as $ticket) {
	        $list[][Util_JsonKey::SDK_TICKET] = $ticket;
	    }
	    $this->localOutput(0, '', array('list' => $list), self::SIGN);
	}
	
	private function getTicketCache($uuid, $type) {
	    $cacheKey = '';
	    if ($type == 'login') {
	        $cacheKey = Util_CacheKey::SDK_TICKET_LOGIN;
	    } else if ($type == 'consume') {
	        $cacheKey = Util_CacheKey::SDK_TICKET_CONSUME;
	    } else {
	        $this->localOutput(1, 'type参数错误', array(), self::SIGN);
	    }
	    
	    $cache = Cache_Factory::getCache();
	    $ticketJson = $cache->hGet($cacheKey, $uuid);
	    $cache->hDel($cacheKey, $uuid);
	    return $ticketJson ? json_decode($ticketJson, true) : array();
	}
	
	/**
	 * 活动列表
	 */
	public function activityListAction(){		
		$page = intval($this->getInput('page'));
		$package = trim($this->getInput('gamePackage'));
		if(!$package){
			$this->clientOutput(array());
		}
		if ($page < 1){
			$page = 1;
		} 
		
		$data['success'] = true;
		$data['sign'] = self::SIGN;
		$gameInfo = $this->getGameInfoByPackage($package);
	 	if(!$gameInfo['id']){
			$data['activityList'] = array();
			$this->clientOutput($data);
		} 
	
		$orderBy = array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC');
		list($total, $activityList) = $this->getActivityList($gameInfo['id'], 
				                                       Sdk_Service_Ad::SHOW_TYPE_ACTIVITY,
				                                       $orderBy,
				                                       $page);
		$activityList = $this->formatActivityList ( $activityList );
		
		$hasnext = ($this->perpage * $page) < $total   ? true : false;
		$data['activityList'] = $activityList;
		$data['hasNext'] = $hasnext ;
		$data['curPage'] = $page ;
		$data['totalCount'] = intval($total) ;
		$this->clientOutput($data);
	}
	
	private function formatActivityList($adList) {
		$attachroot = Common::getAttachPath();
		$webroot  =  Yaf_Application::app()->getConfig()->sdkroot;
		$returnList = array();
		foreach ($adList as $val){
			$url = $webroot.'/game/sdk/activityDetail/?id='.$val['id'];
			$returnList[]=array(
							'id'=>$val['id'],
							'name'=>html_entity_decode( $val['title'], ENT_QUOTES),
							'url'  => $url,
							'showPay'=> $val['is_payment']?true:false,
							'imageUrl'=>$val['img']?$attachroot.$val['img']:'',
			);
		}
		return $returnList;
	}

	private function fillStyle($data){
		if(!$data) return "";
		$content = html_entity_decode($data, ENT_QUOTES);
		//去除html空白处理
		$subject = strip_tags($content, '<img><a>');
		$pattern = array('/\s/','/&nbsp;/i');//去除空白跟空格
		$text = preg_replace($pattern, '', $subject);
		if(empty($text)) return "";
	
		$html = <<<str
    	<style>
    		html,body,div,span,h1,h2,h3,h4,h5,h6,p,dl,dt,dd,ol,ul,li,a,em,img,small,strike,strong,form,label,canvas,footer,header,nav,output{
    			margin:0; padding:0;
    		}
    		.ui-editor {
  				word-break: break-all;
    			line-height: 1.2rem;
			}
			.ui-editor i, .ui-editor em {
  				font-style: italic !important;
			}
			.ui-editor b {
  				font-weight: bold !important;
			}
			.ui-editor u {
  				text-decoration: underline !important;
			}
			.ui-editor s {
  				text-decoration: line-through !important;
			}
			.ui-editor ul li {
  				list-style: initial;
  				margin-left: 1rem !important;
			}
			.ui-editor ol li {
  				list-style: decimal;
  				margin-left: 1rem !important;
			}
			.ui-editor span, .ui-editor p, .ui-editor h1, .ui-editor h2, .ui-editor h3, .ui-editor h4, .ui-editor h5 {
  				white-space: normal !important;
			}
			.ui-editor span, .ui-editor p {
  				line-height: 1.2rem;
			}
			.ui-editor img {
  				padding-top: 5px;
 				max-width: 100% !important;
  				width: auto;
  				height: auto;
  				display: block;
 				margin: 0 auto;
			}
			.ui-editor table {
  				margin: 4px 0;
  				max-width: 300px !important;
			}
			.ui-editor h1, .ui-editor h2, .ui-editor h3 {
  				font-size: 1.2rem !important;
  				line-height: 1.5rem;
			}
			.ui-editor h4, .ui-editor h5, .ui-editor h6 {
  				font-size: 1.2rem !important;
  				line-height: 1.3rem;
			}
    	</style>
    	<div class="ui-editor" style='font-size:13px; color:#777777;'>
str;
		$html.= $content.'</div>';
		return base64_encode($html);
	}
}
