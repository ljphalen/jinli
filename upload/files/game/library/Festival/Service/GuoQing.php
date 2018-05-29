<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Festival_Service_GuoQing{


	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC', 'id' => 'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getBy($params){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	
	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getsBy($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 添加答题日志
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function add($data){
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
	public static function upBydate($data, $params) {
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
		return $tmp;
	}
	
	/**
	 * 
	 * @return Festival_Dao_GuoQing
	 */
	private static function _getDao() {
		return Common::getDao("Festival_Dao_GuoQing");
	}
}
