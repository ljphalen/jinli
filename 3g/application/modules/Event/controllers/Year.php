<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class YearController extends User_BaseController {
	
	public function indexAction() {
		$data = array(
				'login'								=>0,  //是否已登陆
				'chance'							=>1,//是否有领奖机会
				'prizeStatus'					=>-2, //是否有未领奖品
				'activity'							=>0, //活动状态
				'loginUrl'							=>'', //登陆URL
				'prizeUrl'						=>'', //领奖URL
				'expireSeconds'			=>0,//默认过期时间
				'remainedSeconds'		=>0, //剩余领奖时间
				'prizeImageUrl'				=>'', //奖品图片
				'prizeName'					=>'',
		);
		$from = $this->getInput('from');
		if(empty($from)){
			$from = 'index';
		}
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $from . ':year');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $from . ':year');
		
		$loginInfo = Common_Service_User::checkLogin('/event/year/index');
		$data['login']  = $loginInfo['key'];
		if(!intval($loginInfo['key'])){
			$data['loginUrl'] = $loginInfo['keyMain'];
		}
		//活动状态
		$config = Event_Service_Activity::getConfigData();
		$data['expireSeconds'] = $config['national_day_expires']*60;
		$now = time();
		if(!intval($config['national_day_status'])){
			$data['activity'] = 1;
		}
		if( strtotime($config['national_day_start_time']) >$now|| $now > strtotime($config['national_day_end_time'])){
			$data['activity'] = 1;
		}
		//奖品状态
		$prize = array();
		$userInfo = Gionee_Service_User::getCurUserInfo();
		if(!empty($userInfo)){
			$prize = Event_Service_Activity::getUserPrizeInfo($userInfo['id']);
			if(!empty($prize)){
				$data['prizeStatus'] = $prize['prize_status'] ;
				$prizeGoods =Event_Service_Activity::getPrizeGoodsInfo($prize['prize_id']);
				$data['prizeImageUrl'] = $prizeGoods['image'];
				$data['prizeName'] = $prizeGoods['name'];
				$data['chance']  = 0;
				$host = Common::getCurHost();
				$data['prizeUrl'] = sprintf("%s%s",$host,'/event/year/getPrize'); //默认为金币
				if($prize['prize_type'] == 1){ //实物奖品时
					$data['prizeUrl']  = sprintf("%s%s%d",$host,'/user/goods/detail?goods_id=',$prize['prize_val']);
				}
				if($prize['prize_status'] == 0){ //中奖但为领取时
					$expiredTime = $prize['add_time']+$data['expireSeconds'];
					$remainedSeconds  = $expiredTime - $now;
					if($remainedSeconds > 0 ){
						$data['remainedSeconds']  = $remainedSeconds;
					}else{
						Event_Service_Activity::getResultDao()->update(array('prize_status'=>'-1', 'expire_time'=>$now),$prize['id']);
						$prizeGoods = Event_Service_Activity::getPrizeGoodsInfo($prize['prize_id']);
						if($prizeGoods['prize_level'] <=5){ //实物奖品过期,返回奖品池
							Event_Service_Activity::changePrizeGoodsNumber($prizeGoods,'+');
						}
						Event_Service_Activity::getUserPrizeInfo($userInfo['id'],true);
						$data['prizeStatus'] = '-1';
					}
				}
			} 
		}
		
		$sdate = $config['national_day_start_time'];
		$edate = $config['national_day_end_time'];
		$this->assign('sdate',date("m月d日",strtotime($sdate)));
		$this->assign('edate', date("m月d日",strtotime($edate)));
		$this->assign('config', $config);
		$this->assign('data', $data);
		$this->assign('prize', $prize);
	}

	//抽奖
	public  function ajaxDrawingAction(){
		$loginInfo = Common_Service_User::checkLogin('/event/year/index',true);
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$this->_checkActivityStatus();

		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'drawing:year');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'drawing:year');

		$this->_checkRequestTimes($userInfo['id'], 'year');
		$config = Event_Service_Activity::getConfigData();
		$prizeInfo = Event_Service_Activity::getUserPrizeInfo($userInfo['id']);
		if(!empty($prizeInfo)){
			$this->output('0','',array("err_msg"=>'今天已经抢过啦!'));
		}
		$prizeGoodsList = Event_Service_Activity::getPrizeGoodsList();
		$data = $prizeMsg = array();
		foreach ($prizeGoodsList as $k=>$val){
				if($val['number']> 0){
					$data[$val['id']] = $val['ratio'];
                	$prizeMsg[$val['id']]  = $val;
				}
		}
		Event_Service_Activity::addClicksData($userInfo['id'],'year_drawing');
		$prizeGoods = self::_getPrizeGoods($data);
		if(!empty($prizeGoods)){
			$params=  array(
				'uid'						=>$userInfo['id'],
				'prize_id'				=>$prizeGoods['id'],
				'prize_status'		=>0,
				'add_time'			=>time(),
				'add_date'			=>date('Ymd',time()),
				'user_ip'				=>Util_Http::getClientIp(),
			);
			$ret = Event_Service_Activity::getResultDao()->insert($params);
			Event_Service_Activity::getUserPrizeInfo($userInfo['id']);
			$imgPath = Common::getImgPath();
			$host  	 = Common::getCurHost();
			$prizeUrl = $host."/event/year/getPrize";
			if($prizeGoods['prize_type'] == 1){
				$prizeUrl = sprintf("%s%s%d",$host,'/user/goods/detail?goods_id=',$prizeGoods['prize_val']);
			}
			if($prizeGoods['prize_level']<=5){
				Event_Service_Activity::changePrizeGoodsNumber($prizeGoods);
			}
			$this->output('0','',array(
					'err_msg'=>"恭喜您,获得{$prizeGoods['name']},可用户个人<br/>中心的话费和新款手机兑换!",
					'prize_id'=>$prizeGoods['id'],
					'prize_image'=>$imgPath.$prizeGoods['image'],
					'prize_url'		=>$prizeUrl,
			));
		}
		$this->output('-1','操作失败',array('err_msg'=>'操作失败!'));
	}

	//获得奖品
	public function getPrizeAction(){
		$loginInfo = Common_Service_User::checkLogin('/event/year/index',true);
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$config = Event_Service_Activity::getConfigData();
		if(!intval($config['national_day_status'])) {
			Common::redirect('/event/year/index');
		}
		$now = time();
		if(strtotime($config['national_day_start_time']) >$now|| strtotime($config['national_day_end_time'])< $now){
			Common::redirect('/event/year/index');
		}
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'get:year');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'get:year');

		$redirectUrl = sprintf("%s%s",Common::getCurHost(),'/user/index/index');
		//$this->_checkRequestTimes($userInfo['id'], 'get');
		$has = $this->_hasPrize($userInfo['id']);
		if(intval($has)){
			Common::redirect('/event/year/index');
		}
		$prize = Event_Service_Activity::getUserPrizeInfo($userInfo['id']);
		if(empty($prize)){
			Common::redirect($redirectUrl);
		}
		if($prize['prize_status'] == '-1'){
			Common::redirect($redirectUrl);
		}
		if($prize['prize_status'] == '1'){
			Common::redirect($redirectUrl);
		}  
		$expiredTime = $config['national_day_expires']*60 + $prize['add_time'];
		if($prize['prize_status'] == '0'  && $now >$expiredTime){
			Event_Service_Activity::getResultDao()->update(array('prize_status'=>'-1','expire_time'=>$now),$prize['id']);
			Event_Service_Activity::getUserPrizeInfo($userInfo['id'],true);
			Common::redirect($redirectUrl);
		}
		Event_Service_Activity::addClicksData($userInfo['id'],'year_getprize');
		$prizeGoods =Event_Service_Activity::getPrizeGoodsInfo($prize['prize_id']);
		if($prizeGoods['prize_type'] == 1){ //实物奖品
			$redirectUrl = Common::getCurHost()."/user/goods/detail?goods_id={$prizeGoods['prize_val']}";
			Common::redirect($redirectUrl);
		}else{ 
			$update = Event_Service_Activity::getResultDao()->update(array('prize_status'=>1,'get_time'=>time()),$prize['id']);
			$ret = User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $prizeGoods['prize_val'], 215);
			if($ret && $update ){
				$data  = array(
						'uid'=>$userInfo['id'],
						'scores'=>$prize['prize_val'],
						'status'=>1,
						'activity'=>'周年庆活动奖励',
						'prize_name'=>sprintf("%d金币",$prizeGoods['prize_val']),
						'classify'=>17
				);
				$this->_setPrize($userInfo['id']);
				Event_Service_Activity::getUserPrizeInfo($userInfo['id'],true);
				Common_Service_User::sendInnerMsg($data,'national_day_score_tpl');
				$ret2= Common_Service_User::unReadMsgNumber($userInfo['id'],true);
				Common::redirect($redirectUrl);
			}
		}
		$this->output('-1','',array('err_msg'=>'操作失败!'));
	}

	private function _hasPrize($uid){
		$date = date("Ymd",time());
		$key = "User_Year_Date_{$date}_Uid_{$uid}";
		$has = Common::getCache()->get($key);
		return $has;
	}
	private function _setPrize($uid){
		$date = date("Ymd",time());
		$key = "User_Year_Date_{$date}_Uid_{$uid}";
		Common::getCache()->set($key,1,3);
	}
	
	public function clearAction(){
			if(in_array(ENV,array('test','develop'))){
				$userInfo =  Gionee_Service_User::getCurUserInfo();
				$mobile = $this->getInput('mobile');
				if(!empty($mobile)){
					$userInfo =  Gionee_Service_User::getUserByName($mobile);
				}
				Event_Service_Activity::getResultDao()->deleteBy(array('uid'=>$userInfo['id']));
				Event_Service_Activity::getUserPrizeInfo($userInfo['id'],true);
				exit($userInfo['username'].'信息清除成功!');
			}
	}
	
	//抽奖品
	private function _getPrizeGoods($data){
		$prizeGoodsId     = Common_Service_User::getRangeData($data);
		$goodsInfo =Event_Service_Activity::getPrizeGoodsInfo($prizeGoodsId);
		if(empty($goodsInfo)){
			$this->output('-1','',array('err_msg'=>'奖品不存在!'));
		}
		return $goodsInfo;
	}
	

	private function _checkActivityStatus(){
		$config = Event_Service_Activity::getConfigData();
		if(!intval($config['national_day_status'])) {
			$this->output('-1','',array('err_msg'=>'活动已结束!'));
		}
		if(strtotime($config['national_day_start_time']) > time() || strtotime($config['national_day_end_time'])< time()){
			$this->output('-1','' ,array('err_msg'=>'活动已结束!'));
		}
	}
	
	
	private function _checkRequestTimes($uid,$type){
		$key = "EVENT:REQUEST:TIMES:{$uid}:{$type}:";
		$data = Common::getCache()->get($key);
		if(!empty($data)){
			Common::getCache()->set($key,1,3);
			$this->output('-1','',array('err_msg'=>'请求太频繁,请稍后再试!'));
		}
		
	}
}