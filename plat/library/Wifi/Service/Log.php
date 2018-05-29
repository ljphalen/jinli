<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Wifi_Service_Log{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return self::_getDao()->getAll();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    /**
     *
     * Enter description here ...
     * @param unknown_type $uid
     */
    public static function getBy($params) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['gwmac'])) $tmp['gwmac'] = $data['gwmac'];
		if(isset($data['gwmaccrc'])) $tmp['gwmaccrc'] = $data['gwmaccrc'];
		if(isset($data['mac'])) $tmp['mac'] = $data['mac'];
		if(isset($data['maccrc'])) $tmp['maccrc'] = $data['maccrc'];
		if(isset($data['ip'])) $tmp['ip'] = $data['ip'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Wifi_Dao_Device
	 */
	private static function _getDao() {
		return Common::getDao("Wifi_Dao_Log");
	}
}
