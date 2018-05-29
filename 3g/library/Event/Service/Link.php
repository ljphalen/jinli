<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Event_Service_Link{
	
	public  static function getInfoByName($uname,$sync=false){
		$info = self::getUserInfoByUname($uname,$sync);
		if(empty($info['id'])){
			$info = array();
			$uaArr   = Util_Http::ua();
			$params = array(
				'uid'					=> 0,
				'uname'			=>$uname,
				'uuid'				=>!empty($uaArr['uuid'])?$uaArr['uuid']:'',
				'create_time'	=>time(),
				'status'				=>0,
				'cur_date'			=>date("Ymd",time()),
				'cur_num'			=>0,
				'update_time'	=>time(),
			);
			$ret = Event_Service_Link::getUserDao()->insert($params);
			if(!empty($ret)){
				$lastId = Event_Service_Link::getUserDao()->getLastInsertId();
				$info = $params;
				$info['id'] = $lastId;
				self::getUserInfoByUname($uname,true);
			}
		}
		return $info;
	}
	
	public static function getUserInfoByUname($uname,$sync=false){
		if (empty($uname)) {
			return false;
		}
		$rcKey = '3G:EVENT:LINK:USER:' . $uname;
		$info  = Common::getCache()->get($rcKey);
		if (empty($info['id']) || $sync) {
			$info = Event_Service_Link::getUserDao()->getBy(array('uname' => $uname));
			Common::getCache()->set($rcKey, $info, 600);
		}
		return $info;
	}
	
 	public static   function getConfig(){
		$rsKey  = "USER:LINK:CONFIG";
		$data = Common::getCache()->get($rsKey);
		$data = '';
		if(empty($data)){
			$keys = array(
					'user_link_per_scores',
					'user_link_status',
					'user_link_sdate',
					'user_link_edate',
					'user_link_prize_ratio',
					'user_link_prize_level',
					'user_link_init_value',
					'user_link_step',
					'user_link_prize_position',
					'user_link_takepart_scores',
					'user_link_expire_minus',
			);
			$tmp = Gionee_Service_Config::getsBy(array('3g_key'=>array("IN",$keys)));
			$data = array();
			foreach ($tmp as $k=>$v){
					$data[$v['3g_key']] = $v['3g_value'];
			}
			Common::getCache()->set($rsKey,$data,600);
		}
		return  $data;
	}
	
	public static function getRankData(){
		$config = self::getConfig();
		$arr = array();
		$init = $config['user_link_init_value'];
		$step = $config['user_link_step'];
		$list = explode("|",$config['user_link_prize_position']);
		$i = 0;
		foreach ($list as $k=>$v){
			$val = $init + $i*$step;
			$arr[$val] = $v;
			$i++; 
		}
		return $arr;
	}
	
	public static   function getPrizeInfo($pid=0,$sync=false){
		if(!intval($pid)) return false;
		$rcKey  = "EVENT:LINK:PRIZE:INFO:{$pid}";
		$data = Common::getCache()->get($rcKey);
		if(empty($data) || $sync == true){
			$data = Event_Service_Link::getPrizeDao()->get($pid);
			Common::getCache()->set($rcKey,$data,30);
		}
		return $data;
	}
	
	
	public  static  function getUserList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::getUserDao()->getList($start, $limit, $params, $sort);
		$total = self::getUserDao()->count($params);
		return array($total, $ret);
	}
	
	public  static  function getPrizeList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::getPrizeDao()->getList($start, $limit, $params, $sort);
		$total = self::getPrizeDao()->count($params);
		return array($total, $ret);
	}
	
	

	public static  function getInfo($vinfo,$userInfo){
		if (!empty($userInfo['id']) ) {
			$info = Event_Service_Link::getUserDao()->getBy(array('uid'=>$userInfo['id']));
			//$info = self::getUserInfo($userInfo['id']);
			if (empty($info['id']) && empty($vinfo['uid']) && !empty($vinfo['id'])) {
				Event_Service_Link::getUserDao()->update(array('uid' => $userInfo['id']), $vinfo['id']);
				$vinfo['uid'] = $userInfo['id'];
				self::getUserInfo($userInfo['id'],true);
			}
		}
		if (empty($info)) {
			$info = $vinfo;
		}
		return $info;
	}
	
	public static function getUserInfo($uid,$sync=false){
		$rcKey=  "3G:EVENT:LINK:{$uid}";
		$data = Common::getCache()->get($rcKey);
		if(empty($data) || $sync == true){
			$data = Event_Service_Link::getUserDao()->getBy(array('uid'=>$uid));
			Common::getCache()->set($rcKey, $data,60);
		}
		return $data;
	}
	
	public static function getTotalUser(){
		$data =  self::getUserDao()->getUserNumbers();
		$arr = array();
		foreach ($data as $v){
			$arr[$v['cur_date']] = $v['users'];
		}
		return $arr;
	}
	
	public static function getRealUsers($where,$group){
		$data =  self::getPrizeDao()->getPrizeUsers();
		$arr = array();
		foreach ($data as $v){
			$arr['total_users'][$v['prize_status']][$v['date']] += $v['total_users'];
			$arr['total_times'][$v['prize_status']][$v['date']] += $v['total_times'];
		}
		return $arr;
	}
	
	public static function getPrizeUsers($where,$group){
		$data = self::getPrizeDao()->getPrizeUsers($where,$group);
		$arr  = array();
		foreach($data as $v){
			$arr['prize_users'][$v['prize_status']] [$v['date']]= $v['prize_users'];
			$arr['prize_times'][$v['prize_status']][$v['date']] = $v['prize_times'];
		}
		return $arr;
	}
	
	public static function getScoreUsers($where ,$group){
		$data = self::getPrizeDao()->getScoreUsers($where,$group);
		$arr = $arrSub = $arrDetail = $arrReal  = $arrTimes =  array();
		$totalUsers = array();
		foreach($data as $v){
				$arr['total_prize_users'][$v['date']] += $v['total_users']; 
				$arr['total_prize_times'][$v['date']] += $v['total_times'];
				$arrSub['total_score_users'][$v['prize_status']][$v['date']]  += $v['total_users'];
				$arrSub['total_score_times'][$v['prize_status']][$v['date']]  += $v['total_times'];
				
				$arrTimes['total_users'][$v['prize_level']][$v['date']]  += $v['total_users'];
				$arrTimes['total_times'][$v['prize_level']][$v['date']] += $v['total_times'];
				
				$arrDetail['total_users'][$v['prize_level']][$v['prize_status']][$v['date']]  = $v['total_users'];
				$arrDetail['total_times'][$v['prize_level']][$v['prize_status']] [$v['date']]= $v['total_times'];
				
				if(! in_array($v['prize_level'],array('0','6'))){
					$arrReal['total_users'][$v['date']] += $v['total_users'];
					$arrReal['total_times'][$v['date']] += $v['total_times'];
					$arrReal['status_users'][$v['prize_status']][$v['date']] += $v['total_users'];
					$arrReal['status_times'][$v['prize_status']][$v['date']] += $v['total_times'];
				}
			}
		return array($arr,$arrSub,$arrReal, $arrTimes,$arrDetail);
	}
	
	
	public static function checkExpirePrize(){
		$config = self::getConfig();
		$expireTime = $config['user_link_expire_minus']*60;
		$now = time();
		$ids = array();
		$dataList  = Event_Service_Link::getPrizeDao()->getList(0,60,array('prize_status'=>0,'upate_time'=>time()),array('id'=>"ASC"));
		foreach($dataList as $k=>$v){
			if( ( $v['add_time'] + $expireTime) < $now){
				Event_Service_Link::getPrizeDao()->update(array('prize_status'=>'-2'), $v['id']);
				$ids[] = $v['id'];
				//error_log("Update_Time:{$now}  Prize:".json_encode($v)."New_Status:-2 \n",3,'/tmp/event_qixi.log');
			}
		}
		return $ids;
	}
	/**
	 * @return Event_Dao_LinkUser
	 */
	public static function getUserDao() {
		return Common::getDao("Event_Dao_LinkUser");
	}
	
	/**
	 * @return Event_Dao_LinkPrize
	 */
	public static function getPrizeDao() {
		return Common::getDao("Event_Dao_LinkPrize");
	}
}