<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_MyGiftLog{
    const GIFT_STATE_OPENED = 1;
    const GIFT_STATE_CLOSEED = 0;
    const GAME_STATE_ONLINE = 1;
    const GAME_STATE_OFFLINE = 0;
    const GRAB_GIFT_LOG = 1;
    const SEND_GIFT_LOG = 2;
    const GIFT_EXPIRE = 14400;
    const PPERPAGE = 10;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getBy($params,$orderBy = array('create_time'=>'DESC')) {
		return 	self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsBy($params,$orderBy = array('create_time'=>'DESC')) {
		return  self::_getDao()->getsBy($params,$orderBy);
	}
	
	public static function count($params) {
	    return  self::_getDao()->count($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function updateBy($data, $params) {
	    if (!is_array($data)) return false;
	    return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function mutiGiftLog($data) {
	    if (!is_array($data)) return false;
	    return self::_getDao()->mutiInsert($data);
	}
	
	public static function getSyncBy($params) {
	    return 	self::_getSyncDao()->getBy($params);
	}
	
	public static function addSync($data) {
	    if (!is_array($data)) return false;
	    return self::_getSyncDao()->insert($data);
	}
	
	private static function getMyGiftParmes($giftLogIinfo, $version){
	    $parames = array();
	    $uname = $giftLogIinfo['uname'];
	    $imeicrc = $giftLogIinfo['imeicrc'];
	    if(strnatcmp($version, '1.5.0') >= 0){
	        $parames['uname'] = $uname;
	    } else {
	        $parames['imeicrc'] = $imeicrc;
	    }
	    
	    return $parames;
	}
	
	
	private static function getByMygiftLogs($giftLogIinfo, $page, $version){
	    $params = self::getMyGiftParmes($giftLogIinfo, $version);
	     
	    if(strnatcmp($version, '1.5.8') < 0){
	        $params['log_type']  = self::GRAB_GIFT_LOG;
	    }
	     
	    if(strnatcmp($version, '1.5.6') < 0){
	        $myGiftLogs = self::getsBy($params);
	        $total = self::count($params);
	    } else {
	        list($total, $myGiftLogs) = self::getList($page, self::PPERPAGE, $params);
	    }
	     
	    return array($total, $myGiftLogs);
	}
	
	public static function getListLogs($uname, $imeicrc, $page = 1, $version){
	    $giftLogIinfo['uname'] = $uname;
	    $giftLogIinfo['imeicrc'] = $imeicrc;
	    
	    list($total, $myGiftLogs) = self::getByMygiftLogs($giftLogIinfo, $page, $version);
	    return array($total, $myGiftLogs);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['log_id'])) $tmp['log_id'] = intval($data['log_id']);
		if(isset($data['log_type'])) $tmp['log_type'] = intval($data['log_type']);
		if(isset($data['gift_id'])) $tmp['gift_id'] = $data['gift_id'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imeicrc'])) $tmp['imeicrc'] = $data['imeicrc'];
		if(isset($data['activation_code'])) $tmp['activation_code'] = $data['activation_code'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['send_order'])) $tmp['send_order'] = intval($data['send_order']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_MyGiftLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_MyGiftLog");
	}
	
	/**
	 *
	 * @return Client_Dao_MyGiftLogSync
	 */
	private static function _getSyncDao() {
	    return Common::getDao("Client_Dao_MyGiftLogSync");
	}
}
