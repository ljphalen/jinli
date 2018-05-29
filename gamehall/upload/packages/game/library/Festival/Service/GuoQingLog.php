<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Festival_Service_GuoQingLog{


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
	
	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getByLog($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
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
	 * 添加答题日志
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addLog($data){
		if (!is_array($data)) return false;
		//$data = self::_cookData($data);
		return  self::_getDao()->insert($data);
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
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['day_id'])) $tmp['day_id'] = $data['day_id'];
		if(isset($data['answer_id'])) $tmp['answer_id'] = $data['answer_id'];
		if(isset($data['score'])) $tmp['score'] = intval($data['score']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['daan'])) $tmp['daan'] = $data['daan'];
		if(isset($data['answer_id'])) $tmp['answer_id'] = intval($data[answer_id]);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Festival_Dao_GuoQingLog
	 */
	private static function _getDao() {
		return Common::getDao("Festival_Dao_GuoQingLog");
	}
}
