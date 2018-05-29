<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_SendTicketReason {
	
	public static function getList($page = 1, $limit = 10, $params = array(),$order = array('create_time'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getsBy($params) {
		return self::_getDao()->getsBy($params);
	}
	
	public static function count($params) {
		return self::_getDao()->count($params);
	}
	
	public static function getBy($params) {
		return self::_getDao()->getBy($params);
	}
	
	public static function mutiFieldInsert($data) {
	    if (!is_array($data)) return false;
	    return self::_getDao()->mutiFieldInsert($data);
	}

	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['aid'])) $tmp['aid'] = $data['aid'];
		if(isset($data['reason'])) $tmp['reason'] = intval($data['reason']);
		if(isset($data['operator_name'])) $tmp['operator_name'] = intval($data['operator_name']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Taste
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_SendTicketReason");
	}
}
