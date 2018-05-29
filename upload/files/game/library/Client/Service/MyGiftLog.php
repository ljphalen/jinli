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
	
	private static function getCacheKeyParmes($giftLogIinfo, $version){
	    $params = array();
	    $uname = $giftLogIinfo['uname'];
	    $imeicrc = $giftLogIinfo['imeicrc'];
	    if(strnatcmp($version, '1.5.0') >= 0){
	        $ckey = $uname.Util_CacheKey::MY_GIFT_LOGS;
	        $params['uname'] = $uname;
	        $syncUname = $uname;
	        $syncImeicrc ='';
	    } else {
	        $ckey = $imeicrc.Util_CacheKey::MY_GIFT_LOGS;
	        $params['imeicrc'] = $imeicrc;
	        $syncUname = '';
	        $syncImeicrc = $imeicrc;
	    }
	     
	    $syncData = array(
	            'uname' => $syncUname,
	            'imeicrc' => $syncImeicrc,
	            'create_time'=>Common::getTime(),
	    );
	     
	    return array($ckey, $params, $syncData);
	}
	
	public static function updateMygiftLogsCache($giftLogIinfo, $myGiftLogInfo, $version){
	    $cache = Cache_Factory::getCache();
	    list($cacheKey, $parmes, $syncData) = self::getCacheKeyParmes($giftLogIinfo, $version);
	    
	    $myGrabGiftLogs = array();
	    $ret = self::getSyncBy($parmes);
        if(!$ret){
            $myGrabGiftLogs = self::getsByGrabGiftLog($parmes);
            if($myGrabGiftLogs){
              self::batchAddGrabGiftLog($myGrabGiftLogs);
            }
            self::addSync($syncData);
        } 
        
        self::add($myGiftLogInfo);
        $myGiftLogs = self::getsBy($parmes);
        $myGiftLogsCache = self::convertMyGiftLogCache($myGiftLogs);
        if(count($myGiftLogsCache) && is_array($myGiftLogsCache)){
            $cache->set($cacheKey, $myGiftLogsCache, self::GIFT_EXPIRE);
        }
	}
	
	private static function getByMygiftLogsCache($giftLogIinfo, $version){
	    $cache = Cache_Factory::getCache();
	    list($cacheKey, $params, $syncData) = self::getCacheKeyParmes($giftLogIinfo, $version);
	    $myGiftLogsCache = $cache->get($cacheKey);
	    
	    if($myGiftLogsCache === false){
	        $myGrabGiftLogs = array();
	        $ret = self::getSyncBy($params);
	        if(!$ret){
                $myGrabGiftLogs = self::getsByGrabGiftLog($params);
                if($myGrabGiftLogs){
                    self::batchAddGrabGiftLog($myGrabGiftLogs);
                }
                self::addSync($syncData);
	        }
	        
	        $myGiftLogs = self::getsBy($params);
	        $myGiftLogsCache = self::convertMyGiftLogCache($myGiftLogs);
	        if(count($myGiftLogsCache) && is_array($myGiftLogsCache)){
	          $cache->set($cacheKey, $myGiftLogsCache, self::GIFT_EXPIRE);
	        }
	    }
	    
	    if(strnatcmp($version, '1.5.8') < 0){
	        $myGiftLogsCache = self::getGrabGifts($myGiftLogsCache);
	    }
	    
	    return $myGiftLogsCache;
	}
	
	public static function getGrabGifts($giftLogs){
	    $myGiftLogs = array();
	    if(!$giftLogs) {
	        return $myGiftLogs;
	    }
	    
	    foreach($giftLogs as $key=>$value){
	        if($value['log_type'] == self::GRAB_GIFT_LOG){
	            $myGiftLogs[] = $value;
	        }
	    }
	    return $myGiftLogs;
	}
	
	public static function getListLogs($uname, $imeicrc, $page = 1, $version){
	    $giftLogIinfo['uname'] = $uname;
	    $giftLogIinfo['imeicrc'] = $imeicrc;
	    
	    $myGiftLogs = self::getByMygiftLogsCache($giftLogIinfo, $version);
	    $total = count($myGiftLogs);
	    if(strnatcmp($version, '1.5.6') < 0){
	        return array($total, $myGiftLogs);
	    }
	    
	    $offset = ($page - 1) * self::PPERPAGE;
	    $myGiftLogs = array_slice($myGiftLogs, $offset, self::PPERPAGE);
	    return array($total, $myGiftLogs);
	}
	
	private static function batchAddGrabGiftLog($myGrabGiftLogInfo){
	    if (!is_array($myGrabGiftLogInfo)) return false;
        return self::mutiGiftLog($myGrabGiftLogInfo);
	    
	}
	
	public static function getsByGrabGiftLog($search){
	    $myGrabGiftLogs = array();
	    $grabLogs = Client_Service_Giftlog::getsBy($search);
	    if(!$grabLogs) {
	        return $myGrabGiftLogs;
	    }
	    
	    foreach($grabLogs as $key=>$value){
	        $myGrabGiftLogs[] =  array('id' => '',
                    	               'log_id' => $value['id'],
                    	               'log_type' => self::GRAB_GIFT_LOG,
                    	               'gift_id' => $value['gift_id'],
                    	               'game_id' => $value['game_id'],
                    	               'uname' => $value['uname'],
                    	               'imei'=>$value['imei'],
                    	               'imeicrc'=>$value['imeicrc'],
                    	               'activation_code'=>$value['activation_code'],
                    	               'status'=>$value['status'],
                    	               'create_time'=>$value['create_time'],
                    	               'send_order'=>$value['send_order'],
	        );
	    }
	    return $myGrabGiftLogs;
	}
	
	private static function convertMyGiftLogCache($myGiftLogs){
	    $myGiftLogsCache = array();
	    if (!is_array($myGiftLogs)) return array();
    	foreach($myGiftLogs as $key=>$value){
    	        $myGiftLogsCache[] =  array('log_type' => $value['log_type'],
                        	                'gift_id' => $value['gift_id'],
                        	                'game_id' => $value['game_id'],
                        	                'activation_code'=>$value['activation_code']
    	        );
    	}
    	return $myGiftLogsCache;
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
