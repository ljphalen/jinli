<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//用户积分表相关
class User_Service_ScoreLog  {
	
	public static function add($keys = array(),$params= array()){
		if(!is_array($params)) return false;
		return self::_getDao()->batchInsert($keys,$params);
	}
	
	public static function insert($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function getsBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
	}
	
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static function getList($page=1,$pageSize=20,$where=array(),$orderBy=array()){
		$page = max($page,1);
		$count = self::count($where);
		return array(self::count($where),self::_getDao()->getList($pageSize *($page-1),$pageSize,$where,$orderBy));
	}
	
	public static function count($params){
		return self::_getDao()->count($params);
	}

	
	public static function countBy($params=array(),$group){
		$data =  self::_getDao()->getCountByParams($params,$group);
		$ret = array();
		foreach($data as $k=>$v){
			$ret[$v['score_type']] = $v['amount'];
		}
		return $ret;
	}


    public  static  function  totalRemindScores($where=array(),$group=array(),$orderBy =array()){

        $data = self::_getDao()->totalRemindScores($where,$group,$orderBy);
        $ret = array();
        foreach($data as $k=>$v){
            $ret[$v['date']] = $v['total_scores'];
        }
        return $ret;
    }

	public static function getDayEarnScoresInfo($page,$pageSize,$params,$groupBy,$orderBy){
		$page = $pageSize *($page -1);
		$data = self::_getDao()->getDayEarnScoresInfo($page, $pageSize, $params, $groupBy, $orderBy);
		$count  = self::_getDao()->getCountByParams($params,$groupBy);
		return array(count($count),$data);
	}
	//获得当天用户所得到的积分数
	public static function getDayIncreScores($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getDayIncreScores($params);
	}
	
	//检测用户当天完成任务情况
	public static function checkTasks($params,$groupBy){
		if(!is_array($params)) return false;
		return self::_getDao()->checkTasks($params,$groupBy);
	}
	
	public static function getDoneTasksList($page,$pageSize,$where=array(),$group=array(),$order=array()){
		if(!is_array($where)) return false;
		$total = self::_getDao()->getCountByParams($where,$group);
		$data = self::_getDao()->getDoneTasksList($pageSize *($page-1),$pageSize,$where,$group,$order);
		return array(count($total),$data);
	}
	
	public static function getTotalDoneTasksInfo($where=array()){
		if(!is_array($where)) return false;
		return self::_getDao()->getTotalDoneTasksInfo($where);
	}
	
	
	public static function getLotteryDayData($where=array(),$group=array(),$sort=array()){
		if(!is_array($where)) return false;
		$data =  self::_getDao()->getLotteryDayData($where,$group,$sort);
		$res = array();
		foreach ($data as $k=>$v){
			$res[date('Y-m-d',strtotime($v['date']))]['total_users'] = $v['total_users'];
			$res[date('Y-m-d',strtotime($v['date']))]['total_scores'] = $v['total_scores'];
		}
		return $res;
	}
	
	public static function getQuizScoreData($where=array(),$groupBy=array(),$orderBy=array()){
		if(!is_array($where)) return false;
		$data = self::_getDao()->getLotteryDayData($where,$groupBy,$orderBy);
		$ret = array();
		foreach ($data as $k=>$v){
			$ret[$v['date']][$v['score_type']]['total_users']  = $v['total_users'];
			$ret[$v['date']][$v['score_type']]['total_scores'] = $v['total_scores'];
		}
		return $ret;
	}
	
	public static function getDrawingTimesList($params,$group,$sort){
		if(!is_array($params)) return false;
		$data = self::_getDao()->getDrawingTimesList($params,$group,$sort);
		$result = array();
		foreach ($data as $v){
			$result[date('Y-m-d',strtotime($v['date']))]['drawing_times'] = $v['drawing_times'];
		}
		return $result;
	}
	
	public static function getVoipExchangeAmount($where=array(),$group=array()){
		return self::_getDao()->getVoipExchangeAmount($where,$group);
	}
	
	public  static function getVoipExchangeDetailData($where= array(),$group=array()){
		return self::_getDao()->getVoipExchangeDetailData($where,$group);
	}
	
	public static function getPerDayVoipExchangeData(){
		$params =array();
		$params['date']  = date('Ymd',strtotime('-1 day'));
		$params['group_id'] = 3;
		$params['score_type'] = 309;
		return  self::getExchangeChatSumData($params);

	}
	
	private static function getExchangeChatSumData($params){
		return self::_getDao()->getExchangeChatSumData($params);
	}
	
	/**
	 * 夺宝
	 * @param unknown $params
	 * @return boolean
	 */
	public static function snatchCalucateData($params,$groupBy){
		$res = array();
		$data =  self::_getDao()->snatchCalucateData($params,$groupBy);
		foreach ($data as $k=>$v){
			$res[date('Y-m-d',strtotime($v['date']))][$v['score_type']]['snatch_users'] = $v['total_users'];
			$res[date('Y-m-d',strtotime($v['date']))][$v['score_type']]['snatch_times'] = $v['total_times'];
			$res[date('Y-m-d',strtotime($v['date']))][$v['score_type']]['snatch_cost_scores'] = $v['total_cost_scores'] ?$v['total_cost_scores']:0;
		}
		return $res;
	}
	
	public static function getEverydayScoreInfo($params,$group){
		$data = self::_getDao()->getEverydayScoreInfo($params,$group);
		$ret = array();
		foreach ($data as $k=>$v){
			$ret[$v['date']]['quiz_scores'] = $v['quiz_scores'];
		}
		return $ret;
	}
	
	public static function sum($var = '',$params=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->sum($var,$params);
	}
	
	private static function _checkData($params=array()){
		if(!is_array($params)) return false;
		$temp = array();
		if (isset($data['uid'])) $tmp['uid'] = $params['uid'];
		if (isset($data['group_id'])) $tmp['group_id'] = $params['group_id'];
		if (isset($data['score_type'])) $tmp['score_type'] = $params['score_type'];
		if (isset($data['before_score'])) $tmp['before_score'] = $params['before_score'];
		if (isset($data['after_score'])) $tmp['after_score'] = $params['after_score'];
		if (isset($data['affected_score'])) $tmp['affected_score'] = $params['affected_score'];
		if (isset($data['add_time'])) $tmp['add_time'] = $params['add_time'];
		if (isset($data['date'])) $tmp['date'] = $params['date'];
		if (isset($data['fk_earn_id'])) $tmp['fk_earn_id'] = $params['fk_earn_id'];
	}


    public static function getTaskBrowserOnlineTotalCoin($sdate,$edate){
        $datas = self::_getDao()->getTaskBrowserOnlineTotalCoin($sdate,$edate);
        return $datas;
    }
	/**
	 * 
	 * @return User_Dao_Scorelog
	 */
	private  static function _getDao(){
		return Common::getDao("User_Dao_Scorelog");
	}
}