<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_DailyTaskConfig{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		return self::getDao()->getBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->insert($data);
	}
	
	/**
	 * 设置缓存
	 * @return boolean
	 */
	public static function setDailyTaskCache(){
		$rs = Client_Service_DailyTaskConfig::getsBy(array('id'=>array('>', 0)));
		$taskData = array();
		$dailyTaskPointTotal = 0;
		$dailyTaskTicketTotal = 0;
		if(!is_array($rs)){
			return false;
		} 
		$cache = Common::getCache();
		foreach ($rs as $val){
			if($val[status] == 1){
				$h = 'dailyTask'.$val['id'];
				if($val['send_object'] == 1){
					$dailyTaskPointTotal +=  $val['points']*$val['daily_limit'];
				}elseif($val['send_object'] == 2){
					$awardJSON = json_decode($val['award_json'], true);
					$tmpDenomination = 0;
					foreach ($awardJSON as $va){
						$dailyTaskTicketTotal +=  $va['denomination']*$val['daily_limit'];
						$tmpDenomination += $va['denomination'];
					}
					$cache->hSet($h, 'denomination', $tmpDenomination);
				}
				
				$cache->hSet($h, 'dailyLimit', $val['daily_limit']);
				$cache->hSet($h, 'sendObject', $val['send_object']);
				$cache->hSet($h, 'points', $val['points']);
				$cache->hSet($h, 'taskName', $val['task_name']);
			}else{
				$cache->delete('dailyTask'.$val['id']);
			}
		}
		//每日任务的总积分
		$cache->set('dailyTaskPointTotal', $dailyTaskPointTotal);
		//每日任务的总A券
		$cache->set('dailyTaskTicketTotal', $dailyTaskTicketTotal);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['task_name'])) $tmp['task_name'] = $data['task_name'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['daily_limit'])) $tmp['daily_limit'] = $data['daily_limit'];
		if(isset($data['send_object'])) $tmp['send_object'] = $data['send_object'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
		if(isset($data['award_json'])) $tmp['award_json'] = $data['award_json'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_DailyTaskConfig
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_DailyTaskConfig");
	}
}
