<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_AdController extends Api_BaseController {
	
	public $perpage = 10;
	
	const SIGN = 'GameSDK';
	/**
	 * 最新公告
	 */
	public function newestAdAction(){
		$sign='GameSDK';
		$package = trim($this->getInput('gamePackage'));
		$uname = $this->getInput('account');
		$uuid = $this->getInput('uuid');
		$sp = trim($this->getInput('sp'));
		if(!$package){
			$this->localOutput(1, '参数错误', $data, $sign);
		}
		
		$game_params['package'] = $package;
		$game_info = Resource_Service_Games::getBy($game_params);
		if(!$game_info['id']){
			$this->localOutput(1, '参数错误', $data, $sign);
		}
		
		$data['package'] = $package;
	
		$time = Common::getTime();
		$params['show_type'] = array('LIKE',1);
		$params['start_time'] = array('<=',$time);
		$params['end_time'] = array('>=',$time);
		$params['is_finish'] = 1;
		$params['status'] = 1;
		$params['game_ids'] = array('LIKE',$game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
				
		}
		if($ids){
			$params['id'] = array('IN',$ids);
		}else{
			$params['id'] = 0 ;
		}
		$ad = Sdk_Service_Ad::getBy($params,array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC'));
		
		$data['noticeId'] = $ad['id'];
		$data['noticeTitle'] = html_entity_decode( $ad['title'],ENT_QUOTES);
		$data['noticeContent'] = $this->fillStyle($ad['ad_content']);
	
		//取得版本信息
		$question_param['uuid'] = $uuid;
		$result = Sdk_Service_Feedback::getBy($question_param, array('reply_time'=>'DESC', 'create_time'=>'DESC', 'id'=>'DESC'));
		//问题的版本号
		$data['versions']['helpVersion'] = intval($result['version_time']);
	
		//活动的版本号
		$activity_params['show_type'] = array('LIKE', '2');
		$activity_params['is_finish'] = 1;
		$activity_params['status'] = 1;
		$activity_params['start_time'] = array('<=', $time);
		$activity_params['end_time'] = array('>=', $time);
		$activity_params['game_ids'] = array('LIKE', $game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($activity_params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
		}
		if($ids){
			$activity_params['id'] = array('IN',$ids);
		}else{
			$activity_params['id'] = 0;
		}
	
		$result = Sdk_Service_Ad::getBy($activity_params ,array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC'));
		$data['versions']['activityVersion'] = intval($result['version_time']);
		$data['timeStamp'] = intval($result['version_time']);
		$this->localOutput(0, '', $data, $sign);
	
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
		$data['sign'] = 'GameSDK';
		$game_params['package'] = $package;
		$game_info = Resource_Service_Games::getBy($game_params);
		if(!$game_info['id']){
			$data['noticeList'] = array();
			$this->clientOutput($data);
		}

		$time = Common::getTime();
		$params['show_type'] = array('LIKE',1);
		$params['start_time'] = array('<=',$time);
		$params['end_time'] = array('>=',$time);
		$params['is_finish'] = 1;
		$params['status'] = 1;
		$params['game_ids'] = array('LIKE',$game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
			
		}
		if($ids){
			$params['id'] = array('IN',$ids);
		}else{
			$params['id'] = 0 ;
		}
		$ad_list = Sdk_Service_Ad::getsBy($params,array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC'));
		//list($total,$ad_list) = Sdk_Service_Ad::getList($page, $this->perpage, $params,  array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC'));
		$temp = array();
	    $webroot  =  Yaf_Application::app()->getConfig()->sdkroot;
		foreach ($ad_list as $val){
			if($val['ad_type'] == 1){
				$url = $val['ad_content'];
			}else{
				$url = $webroot.'/game/sdk/activityDetail/?id='.$val['id'];
			}
			$temp[]=array('id'=>$val['id'],
					      'name'=>html_entity_decode( $val['title'],ENT_QUOTES),
					      'url' => $url,
					      'type' =>'Activity',
					      'showPay'=> $val['is_payment']?true:false
					);
		}
		$data['noticeList'] = $temp;
		
		//取得版本信息
		$question_param['uuid']  = $uuid ;
		$result = Sdk_Service_Feedback::getBy($question_param, array('reply_time'=>'DESC', 'create_time'=>'DESC', 'id'=>'DESC'));
		//问题的版本号
		$data['versions']['helpVersion'] = intval($result['version_time']);
		
	
		//公告的版本号
		$time = Common::getTime();
		$ad_params['show_type'] = array('LIKE', '1');
		$ad_params['is_finish'] = 1;
		$ad_params['status'] = 1;
		$ad_params['start_time'] = array('<=', $time);
		$ad_params['end_time'] = array('>=', $time);
		$ad_params['game_ids'] = array('LIKE', $game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($ad_params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
		}
		if($ids){
			$ad_params['id'] = array('IN',$ids);
		}else{
			$ad_params['id'] = 0;
		}
		$result = Sdk_Service_Ad::getBy($ad_params, array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC'));
		$data['versions']['noticeVersion'] = intval($result['version_time']);
		
		//活动的版本号
		$activity_params['show_type'] = array('LIKE', '2');
		$activity_params['is_finish'] = 1;
		$activity_params['status'] = 1;
		$activity_params['start_time'] = array('<=', $time);
		$activity_params['end_time'] = array('>=', $time);
		$activity_params['game_ids'] = array('LIKE', $game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($activity_params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
		}
		if($ids){
			$activity_params['id'] = array('IN',$ids);
		}else{
			$activity_params['id'] = 0;
		}
		
		$result = Sdk_Service_Ad::getBy($activity_params ,array('version_time'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC'));
		$data['versions']['activityVersion'] = intval($result['version_time']);
		$this->clientOutput($data);

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
		if ($page < 1) $page = 1;
		
	
		$package = trim($this->getInput('gamePackage'));
		if(!$package){
			$this->clientOutput(array());
		}
		
		$data['success'] = true;
		$data['sign'] = 'GameSDK';
		$game_params['package'] = $package;
		$game_info = Resource_Service_Games::getBy($game_params);
	 	if(!$game_info['id']){
			$data['activityList'] = array();
			$this->clientOutput($data);
		} 
	
		$time = Common::getTime();
		$params['show_type'] = array('LIKE',2);
		$params['start_time'] = array('<=',$time);
		$params['end_time'] = array('>=',$time);
		$params['is_finish'] = 1;
		$params['status'] = 1;
		$params['game_ids'] = array('LIKE',$game_info['id']);
		$ret = Sdk_Service_Ad::getsBy($params);
		$ids = array();
		foreach ($ret as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($game_info['id'], $temp)){
				$ids[$val['id']] = $val['id'];
			}
				
		}
		if($ids){
			$params['id'] = array('IN',$ids);
		}else{
			$params['id'] = 0;
		}
		list($total,$ad_list) = Sdk_Service_Ad::getList($page, $this->perpage, $params,  array('sort'=>'DESC', 'start_time'=>'DESC', 'id'=>'DESC'));
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$temp = array();
		$data['success'] = true;
		$data['sign'] = 'GameSDK';
		$attachroot = Common::getAttachPath();
		$webroot  =  Yaf_Application::app()->getConfig()->sdkroot;
		foreach ($ad_list as $val){
			if($val['ad_type'] == 1){
				$url = $val['ad_content'];
			}else{
				$url = $webroot.'/game/sdk/activityDetail/?id='.$val['id'];
			}
			$temp[]=array(
					'id'=>$val['id'],
					'name'=>html_entity_decode( $val['title'],ENT_QUOTES),
					'url'  => $url,
					'showPay'=> $val['is_payment']?true:false,
					'imageUrl'=>$val['img']?$attachroot.$val['img']:'',
			);
		}
		$data['activityList'] = $temp;
		$data['hasNext'] = $hasnext ;
		$data['curPage'] = intval($page) ;
		$data['totalCount'] = intval($total) ;
		$this->clientOutput($data);
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
