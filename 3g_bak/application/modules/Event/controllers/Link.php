<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 *用户中心首页
 */
class LinkController extends User_BaseController {
	
	public $prizeStatus = array(
		'1'	=>'已领取',
		'0'	=>'未领取',
		'-2'	=>'过期奖品',
	);
	
	public function indexAction(){
		$tipFlag = $this->getInput('pz_tip');
		$tip  = $tipFlag?$tipFlag:0;
		$uName = Common::getUName();
		if (empty($uName)) {
			$this->output('-1','',array('prize_code'=>101));
		}
		$from = $this->getInput('from');
		$from = 'index_' . $from;
		
		$info = Event_Service_Link::getInfoByName($uName);
		 if(empty($info['id'])){
			$this->output('-1','',array('prize_code'=>102));	
		} 
		
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $from . ':qixi');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $from . ':qixi');
		
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$info  =  Event_Service_Link::getInfo($info,$userInfo);
	
		$config = Event_Service_Link::getConfig();
		$params = array();
		$nowDate  = date("Ymd",time());
		if($nowDate != $info['cur_date']){
			$params['log_id'] = 0;
			$params['cur_date'] = $nowDate;
			$params['cur_num'] = 0;
		}
		if(!empty($params)){
			Event_Service_Link::getUserDao()->update($params, $info['id']);
		}
		$data = array(
				'prize_name'	=>'',
				'prize_url'		=> sprintf("%s/event/link/getScores",Common::getCurHost()),
				'prize_times'	=>'3',
				'prize_get'		=>0,
				'prize_level'	=>0,
				'prize_expired'=>0,
				'prize_val'		=>0,
				'prize_per_score'=>$config['user_link_per_scores'],
			);
		$now = time();
		if($config['user_link_status'] == 0 || strtotime($config['user_link_edate']) < $now){
			$data['prize_expired'] = 1;//活动已结束
		}
		$prizeInfo= array();
		if(!empty($info['log_id'])){
			$prizeInfo =Event_Service_Link::getPrizeInfo($info['log_id']);
			if($prizeInfo){
				$expireTime  = $prizeInfo['add_time'] + $config['user_link_expire_minus']*60; //已过期;
				if($prizeInfo['prize_status'] == 0 && ($expireTime < $now)) { 
					Event_Service_Link::getPrizeDao()->update(array('prize_status'=>'-2'), $prizeInfo['id']);
					$data['prize_level'] = 0;
					$data['prize_name'] = '';
					$tip = 1;
				} 
				if($prizeInfo['prize_status'] ==1){ //已领奖
					$data['prize_get'] = 1; 
				}elseif($prizeInfo['prize_status'] == 0 ) { //已中奖未领奖
					$data['prize_level']  = $prizeInfo['prize_level'];
					if($prizeInfo['prize_level'] == '6'){
						$data['prize_name'] = sprintf("%d金币",$config['user_link_takepart_scores']);
					}elseif ($prizeInfo['prize_level'] >0 ) {
						$data['prize_url'] = sprintf("%s/user/goods/detail?from=qx&goods_id=%d",Common::getCurHost(),$prizeInfo['prize_val']);
						$goodsInfo = User_Service_Commodities::get($prizeInfo['prize_val']);
						$data['prize_name'] = $goodsInfo['name'];
					}
				}
			}
		} 

		$prizeList = array();
		if(!empty($userInfo['id'])){
			$prizeList = $this->_getUserPrizeList($userInfo['id']);
		}
	
		$this->assign('userInfo', $userInfo);
		$this->assign('prizeList', $prizeList);
		$this->assign('data', $data);
		$this->assign('prizeStatus', $this->prizeStatus);
		$this->assign('tip', $tip);
	}
	
	private function _getUserPrizeList($uid){
		$ret = array();
		$webRoot = Common::getCurHost();
		$reason = array(
				'1'	=>"<a href='".$webRoot."/user/center/score'>查看</a>",
				'-2'	=>"(该奖品已过期)",
		);
		$where = array(
			'uid'=>$uid,
			'prize_status'=>array('IN',array('1','-2')),
		);
		$prizeList = Event_Service_Link::getPrizeDao()->getsBy($where,array('prize_status'=>'DESC'));
		foreach ($prizeList as $k=>$v){
			if(in_array($v['prize_level'],array('0','6'))){
				$v['prize_name'] = $v['prize_val']."金币";
			}else{
				$goods = User_Service_Commodities::get($v['prize_val']);
				$v['prize_name'] = $goods['name'];
				$reason['1'] = "<a href='".$webRoot."/user/center/msg'>查看</a>";
			}
			$v['reason'] = $reason[$v['prize_status']];
			$v['date'] =  date('m月d日',$v['add_time']);
			$ret[$v['prize_status']][]  = $v;
		}
		return $ret;
	}
	
	public function ajaxGetPrizeAction(){
		$info = $this->_getUserInfo();
		$config = Event_Service_Link::getConfig();
		
		$this->_checkStatus($config);
		$this->_check_request_times($info['uname'],'ajax');
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'prize:qixi');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  'prize:qixi');
		
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$info  =  Event_Service_Link::getInfo($info,$userInfo);
		
		//已领奖时
		if(!empty($info['log_id'])){
			$out = array();
			$prizeData  = Event_Service_Link::getPrizeInfo($info['log_id']);
			if(!empty($prizeData) && in_array($prizeData['prize_status'], array('0','1'))){ 
				$goods = User_Service_Commodities::get($prizeData['prize_val']);
				$out['prize_name'] = $goods['name'];
				$out['prize_level'] = $prizeData['prize_level'];
				$out['prize_url']		 = '/event/link/index';
				if($prizeData['prize_status'] == 1){
					$out['prize_url'] = '/event/link/index?pz_tip=1';
				}
				$this->output('0','已有中奖商品',$out);
			}
		}
		
		$num = Event_Service_Link::getPrizeDao()->count();
		$_num = $this->getInput('_num');
		if(!empty($_num)){
			$num = $_num;
		}
		$exist = false;
		$leastPrize  = Event_Service_Link::getPrizeDao()->getBy(array('num'=>$num));
		if(empty($leastPrize['id'])){
			$exist = true;
		}
		$rankData = Event_Service_Link::getRankData();
		if($exist && isset($rankData[$num])){
			$prizeLevel = $rankData[$num];
			$goodsList = json_decode($config['user_link_prize_level'],true);
			$goodsId = $goodsList[$rankData[$num]];
			$val = $goodsId;
			$goodsInfo = User_Service_Commodities::get($goodsId);
			$name = $goodsInfo['name'];
			$url = sprintf("%s/user/goods/detail?from=qixi&goods_id=%d",Common::getCurHost(),$goodsId);
		}else{
			$val = $config['user_link_takepart_scores'];
			$prizeLevel = 6;
			$name = sprintf("您获得%d金币",$val);
			$url = sprintf("%s/event/link/getScores?num=10",Common::getCurHost());
		}
		$params = array( 
			'uid'		=>$info['uid']?$info['uid']:0,
			'uname'=>$info['uname'],
			'prize_level'=>$prizeLevel,
			'prize_val'		=>$val,
			'add_time'		=>time(),
			'date'				=>date("Ymd",time()),
			'user_ip'			=>Util_Http::getClientIp(),
			'num'				=>$num,
		);
		$ret = Event_Service_Link::getPrizeDao()->insert($params);
		if($ret){
			$lastId = Event_Service_Link::getPrizeDao()->getLastInsertId();
			$arr = array("status"=>2,'log_id'=>$lastId);
			Event_Service_Link::getUserDao()->update($arr, $info['id']);
			Event_Service_Link::getInfoByName($info['uname'],true);
			$this->output('0','',array(
										'prize_err'=>'',
										'prize_level'=>$prizeLevel,
										'prize_val'	=>$val,
										'prize_name'=>$name,
										'prize_url'=>$url."&pid={$lastId}",
							)
					);
		}else{
			$this->output('-1','',array('prize_err'=>'获得奖品失败!'));
		}
	}
	
	public function getScoresAction(){
		$counts =$this->getInput('num');
		if($counts<0 ||$counts >10){
			$this->output('-1','',array('prize_msg'=>'数据有错!'));
		}
		$config = Event_Service_Link::getConfig();
		$this->_checkStatus($config);
		
		$info = $this->_getUserInfo(true,$counts); //用户信息
		$this->_check_request_times($info['uname']);
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,   'score:qixi');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,   'score:qixi');
	
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$info  =  Event_Service_Link::getInfo($info,$userInfo);
		
		$prizeLevel =  $totalScores = $lastId = 0;
		$prize = Event_Service_Link::getPrizeDao()->get($info['log_id']);
		if(!empty($prize)){
			if($prize['prize_status'] == 1){
				Common::redirect('/event/link/index?pz_tip=1');
				exit();
			}
			
		$now = time();
		$expireTime = $prize['add_time'] + $config['user_link_expire_minus']*60;
		if($now > $expireTime){
			Event_Service_Link::getPrizeDao()->update(array('prize_status'=>'-2','update_time'=>$now), $prize['id']);
			Common::redirect('/event/link/index?pz_tip=1');
		}
		
		$lastId = $prize['id'];
		$totalScores = $config['user_link_takepart_scores'];
		$prizeLevel = 6;
		}else{
			$perScores =$config['user_link_per_scores']?$config['user_link_per_scores']:2;
			$totalScores = $perScores*$counts;
			$num = Event_Service_Link::getPrizeDao()->count();
			$params = array(
				'uname'				=>$info['uname'],
				'uid'						=>$info['uid'],
				'prize_level'		=>$prizeLevel,
				'prize_status'		=>0,
				'prize_val'			=>$totalScores,
				'user_ip'				=>Util_Http::getClientIp(),
				'add_time'			=>time(),
				'date'					=>date("Ymd",time()),
				'num'					=>$num,	
			);
			$res = Event_Service_Link::getPrizeDao()->insert($params);
			if($res){
				$lastId = Event_Service_Link::getPrizeDao()->getLastInsertId();
				$userStatus = Event_Service_Link::getUserDao()->update(array('status'=>2,'log_id'=>$lastId,'cur_num'=>$info['cur_num']),$info['id']);
				Event_Service_Link::getUserInfoByUname($info['uname'],true);
			}
		}
		$prizeStatus = 	$sta  =  -1;
		$logData  =  User_Service_Gather::changeScoresAndWriteLog($info['uid'], $totalScores, '213','2');
		if($logData > -2 ){
				$prizeStatus = $sta = 1;
		}
		$ret = Event_Service_Link::getPrizeDao()->update(array('uid'=>$info['uid'], 'prize_status'=>$prizeStatus,'update_time'=>time(),'is_show'=>2), $lastId);
		Event_Service_Link::getPrizeInfo($lastId,true);
		Common::redirect(Common::getCurHost()."/user/index/index?act=qx&scores={$totalScores}&sta={$sta}");
		exit();
	}

	public function tagAction(){
		$var = $this->getInput('type');
		if(!empty($var)){
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,   "{$var}:qixi");
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,  "{$var}:qixi");
		}
		exit();
	}
	
	public function giveupAction(){
		$info = $this->_getUserInfo();
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV,  'giveup:qixi');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV,   'giveup:qixi');
		
		$userInfo = Gionee_Service_User::getCurUserInfo();
		$info  =  Event_Service_Link::getInfo($info,$userInfo);
		
		$where=  array();
		$where['uname']  = $info['uname'];
		$where['prize_status']  = 0;
		$where['date'] = date('Ymd',time());
		$where['uname'] = $info['uname'];
		$where['uid']	 = $info['uid'];
		$prizeInfo = Event_Service_Link::getPrizeDao()->getBy($where);
		if(!empty($prizeInfo)){
			$ret = Event_Service_Link::getPrizeDao()->update(array('prize_status'=>'-1','update_time'=>time()),$prizeInfo['id']);
			if($ret){
				Event_Service_Link::getPrizeInfo($prizeInfo['id'],true);
				$this->output('0','',array('prize_msg'=>'操作成功','prize_code'=>'001'));
			}
		}
		$this->output('0','',array('prize_msg'=>'操作成功!','prize_code'=>'002'));
	}
	
	public function loginAction(){
		$backurl = Common::getCurHost().'/event/link/index';
		Common_Service_User::checkLogin($backurl,true);
		exit();
	}
	
	private function _checkStatus($config){
		if(empty($config['user_link_status'])){
			$this->output('-1','',array('prize_err'=>'对不起,活动已关闭'));
		}
		$now = time();
		if(strtotime($config['user_link_sdate'])> $now){
			$this->output('-1','',array('prize_err'=>'对不起,活动未开始'));
		}
		if(strtotime($config['user_link_edate']<$now)){
			$this->output('-1','',array('prize_err'=>'对不起,活动已结束'));
		}
	}
	
	private function _check_request_times($uname,$type='score'){
		$rcKey = "3G:EVENT:LINK:USER:{$uname}:{$type}";
		$val = Common::getCache()->get($rcKey);
		if(!empty($val)){
			Common::getCache()->set($rcKey,1,3);
			$this->output('-1','',array("prize_msg"=>'请求次数太多!'));
		}
	}
	
	private  function _getUserInfo($needLogin = false,$num=0){
		$uName = Common::getUName();
		if (empty($uName)) {
			$this->output('0','',array('prize_code'=>'网络请求失败,请刷新重试001!'));
		}
		$info = Event_Service_Link::getInfoByName($uName);
		if(empty($info['id'])){
			$this->output('0','',array('prize_code'=>'网络请求失败,请刷新重002!'));
		}
		if($needLogin){
			$login = Common_Service_User::checkLogin("/event/link/index");
			if(empty($login['key'])){
				Common::redirect($login['keyMain']);
				exit();
			} 
		}
		return $info;
	}
	
}