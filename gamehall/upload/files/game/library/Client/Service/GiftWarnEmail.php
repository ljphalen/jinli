<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_GiftWarnEmail {
	
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
	
    public static function add($data) {
		return self::_getDao()->insert($data);
	}

	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['gift_id'])) $tmp['gift_id'] = $data['gift_id'];
		if(isset($data['is_lessfive'])) $tmp['is_lessfive'] = $data['is_lessfive'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_GiftWarnEmail
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_GiftWarnEmail");
	}
}
