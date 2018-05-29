<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Earn {
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function getsBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$sort);
	}
	 
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 得到当天登陆信息
	 */
	
	public static function getSignInfo($stime,$etime,$uid,$sync=false){
		$date = date("Ymd",time());
		$key = "User_Sign_Data_{$date}_{$uid}";
		$data = Common::getCache()->get($key);
		if(empty($data) || $sync== true){
			$params             = array();
			$params['group_id'] = 1;
			$params['add_time'] = array(array(">=",$stime),array("<=",$etime));
			$params['uid']      = $uid;
			$data                = User_Service_Earn::getBy($params, array('id' => 'DESC'));
			Common::getCache()->set($key,$data,600);
		}
			return $data;
	}
	
	/**
	 *得到当天获到积分的商品所有ID
	 * @param unknown $params
	 */
	public static function getDayEarnedGoodsIds($params = array()){
		if(!is_array($params)) return false;
		$dataList =  self::_getDao()->getsBy($params);
		$ids = array();
		foreach ($dataList as $k=>$v){
			$ids[$k] = $v['goods_id']; 
		}
		return $ids;
	}
	
	//得到所有获取过积分的用户ID
	public static function getAllActivateUserIds($params = array()){
		return self::_getDao()->getAllActivateUserIds($params);
	}
	/**
	 * 获取月签到统计
	 * @param unknown $params
	 */
	public static function statMonthSignin($params = array(),$order = array(),$limit = array()) {
		$sdate = empty($params['sdate']) ? strtotime(date('Y-m-1 0:0:0'))                       : $params['sdate'];
		$edate = empty($params['edate']) ? strtotime(date('Y-m-1 0:0:0'),strtotime('+1 month')) : $params['edate']; 	
		$order = empty($order) 		     ? array('cnt'=>'desc') : $order;
		$params2 = array();
		if(!empty($params['days'])) {
			$params2['cnt'] = $params['days'];
			unset($params['days']);
		}
		$params= array(
			'group_id' => 1,
			'add_time' => array(array('>=',$sdate),array('<',$edate))
		);
		$res  = self::_getDao()->statUserSignin($params,$params2,$order,$limit);		
		return $res; 
	}
	

	//获得用户每天获得积分数
	public static function getUserEarnData($where,$order,$page,$pageSize){
		if(!is_array($where)) return false;
		return self::_getDao()->getUserEarnData($where,$order,$page,$pageSize);
	}
	
	//每天完成任务的统计数据
	public static function getUserDoneTasksMsg($where=array(),$order=array(),$page=1,$pageSize=20){
		if(!is_array($where)) return false;
		$page  = $pageSize *( max($page,1)  - 1 );
		$dataList= self::_getDao()->getUserDoneTasksMsg($where,$order,$page,$pageSize);
	}
	
	public static function getActiviteUsersNumber($where){
		return self::_getDao()->getActiviteUsersNumber($where);
	}
	
	public static function getPerDayUserAmount($params){
		return self::_getDao()->getPerDayUserAmount($params);
	}
	
	public static function getDoneTasksData($params){
		return self::_getDao()->getDoneTasksData($params);
	}
	
	//当天用户赚取积分排名
	public static function getDayScoreRank($params,$groupBy,$page,$pageSize){
		if(!is_array($params)) return false;
		if($page<1){
			$page = 1;
		}
		$page = $pageSize *($page -1);
		$count = self::_getDao()->count($params);
		$scoreMsg =  self::_getDao()->getDayScoreRank($params,$groupBy,$page,$pageSize);
		$options = array_merge($params,array('cat_id'=>array('>',0)));
		$tasksMsg = self::_getDao()->getDayTaskNumber($options,$groupBy,$page,$pageSize);
		$data = array();
		foreach ($scoreMsg as $k=>$v){
			$data[$v['uid']]['total_scores'] = $v['total_scores'];
		}
		foreach ($tasksMsg as $key=>$val){
			$data[$val['uid']]['tasks_number'] = $val['tasks_number'];
		}
		return array($count,$data);
	}
	public static function getSumScoresByUserRank($params){
		if(!is_array($params)) return false;
		$deadlineScores =  self::_getDao()->getSumScoresByUserRank($params);
		$deadlineTasks =  self::_getDao()->getSumTasksByUserRank(array_merge($params,array('cat_id'=>array('>',0))));
		$scores = $tasks = 0;
		foreach($deadlineScores as $v){
			$scores += $v['deadline_scores'];
		}
		
		foreach ($deadlineTasks as $m){
			$tasks  += $m['deadline_tasks'];
		}
		return array($scores,$tasks);
	}

	public static function getDubiousIpData($date,$num){
		$dataList = self::_getDao()->getDubiousIpData($date,$num);
		return $dataList;
	}
	

	public static function resetUserDays(){
		return self::_getDao()->resetUserDays();
	}
	/**
	 * @return User_Dao_Earn
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_Earn");
	}

	static public function getTaskLogList($where) {
		return self::_getDao()->getTaskLogList($where);
	}

	static public function getTaskLogTotal($where) {
		return self::_getDao()->getTaskLogTotal($where);
	}
}