<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Festival_Service_Log{


	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params,array('create_time'=>'DESC', 'id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function updateStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('status'=>$status), $value);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getByLog($params){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	
	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getsByLog($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 添加抽奖日志
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addLog($data){
		if (!is_array($data)) return false;
		//$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateLog($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['activity_id'])) $tmp['activity_id'] = intval($data['activity_id']);
		if(isset($data['user_id'])) $tmp['user_id'] = $data['user_id'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['tel'])) $tmp['tel'] = intval($data['tel']);
		if(isset($data['prize'])) $tmp['prize'] = intval($data['prize']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Game_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Festival_Dao_Log");
	}
}
