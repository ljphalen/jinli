<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_AdController extends Api_BaseController {
	
	public $perpage = 10;
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
	    $webroot  = Common::getWebRoot();
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
		$webroot  = Common::getWebRoot();
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
	
}
