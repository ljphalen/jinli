<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Score{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllScore() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	public static function getByScore($params, $orderBy=array()){
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getScoreList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getLogsList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getLogsDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getLogsDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLog($data) {
		if (!is_array($data)) return false;
		return self::_getLogsDao()->insert($data);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getByLog($params = array(),$orderBy = array('id'=>'DESC')) {
		return self::_getLogsDao()->getBy($params, $orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateScore($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameScore($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 * 添加评分
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addScore($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 添加评分日志
	 * 
	 */
	public static function addScoreLog($data){
		if (!is_array($data)) return false;
		$data = self::_cookLogData($data);
		return self::_getLogsDao()->insert($data);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameScoreLog($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookLogData($data);
		return self::_getLogsDao()->updateBy($data, $params);
	}

	/**
	 *
	 * 10分制平均分计算
	 *
	 * @param 总分数 $total
	 * @param 总人数 $num
	 * @return int
	 * 20 3
	 */
	public static function avgScore($total, $num){
		$avg = 0;
		if($total==0 ||$num==0) return $avg;
		$score = number_format($total/$num/2, 1);
		list($i, $f) = explode('.', $score);
		$avg = $i+(($f > 0 ? ($f <= 5 ? 0.5 : 1) : 0));
		$avg = $avg * 2;
		return $avg;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['score'])) $tmp['score'] = $data['score'];
		if(isset($data['total'])) $tmp['total'] = $data['total'];
		if(isset($data['number'])) $tmp['number'] = $data['number'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 * 添加评分日志
	 * @param unknown $data
	 * @return multitype:unknown
	 */
	private static function _cookLogData($data) {
		$tmp = array();
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['score'])) $tmp['score'] = $data['score'];
		if(isset($data['user'])) $tmp['user'] = $data['user'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['stype'])) $tmp['stype'] = $data['stype'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['android'])) $tmp['android'] = $data['android'];
		if(isset($data['sp'])) $tmp['sp'] = $data['sp'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Score
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Score");
	}
	
	/**
	 *
	 * @return Resource_Dao_ScoreLogs
	 */
	private static function _getLogsDao() {
		return Common::getDao("Resource_Dao_ScoreLogs");
	}
}
