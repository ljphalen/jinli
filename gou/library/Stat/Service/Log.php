<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter desChannelCodeiption here ...
 * @author tiansh
 *
*/
class Stat_Service_Log{

	//版本
	const V_H5 = 1;
	const V_APK = 2;
	const V_CHANNEL = 3;
	const V_MARKET = 4;
	const V_APP = 5;
	const V_IOS = 6;
	
   static $V_ARRAY = array(
	            1=>'H5',
	            2=>'APK',
	            3=>'CHANNEL',
	            4=>'MARKET',
	            5=>'APP',
               6=>'IOS'
	        );
	
	//统计url
	const URL_CLICK = '/click/redirect';
	const URL_SEARCH = '/click/search';
	
	/**
	 *
	 * Enter getChannelCode
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

    /**
     * @param $params
     * @return array
     */
    public static function getsByParams($params) {
        $data = self::_cookData($params);
        return self::_getDao()->getsByParams($data);
    }

    /**
     * @param $params
     * @return array
     */
    public static function getsChannelCodeByParams($params) {
        $data = self::_cookData($params);
        return self::_getDao()->getsChannelCodeByParams($data);
    }

	/**
	 *
	 * Enter desChannelCodeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

    /*
     * 根据host查询
     */
    public static function getByHost($params) {
        if (empty($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getByHost($data);
    }

    /**
     * @param $params
     * @return array
     */
    public static function getByProvince($params) {
        if (empty($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getByProvince($data);
    }

    /**
     * @param $params
     * @return array|bool
     */
    public static function getsGroupByChannelCode($params) {
        if (empty($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getsGroupByChannelCode($data);
    }
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function update($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}


	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * get by
	 */
	public static function deleteBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->deleteBy($params);
	}

	/**
	 *
	 * Enter desChannelCodeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['version_id'])) $tmp['version_id'] = $data['version_id'];
		if(isset($data['hash'])) $tmp['hash'] = $data['hash'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
        if(isset($data['hour'])) $tmp['hour'] = intval($data['hour']);
        if(isset($data['module_id'])) $tmp['module_id'] = intval($data['module_id']);
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = intval($data['channel_code']);
		if(isset($data['item_id'])) $tmp['item_id'] = intval($data['item_id']);
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['host'])) $tmp['host'] = $data['host'];
		if(isset($data['host_id'])) $tmp['host_id'] = $data['host_id'];
        if(isset($data['province'])) $tmp['province'] = $data['province'];
        if(isset($data['province_id'])) $tmp['province_id'] = $data['province_id'];
        if(isset($data['city'])) $tmp['city'] = $data['city'];
        if(isset($data['city_id'])) $tmp['city_id'] = $data['city_id'];
		if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
		if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['ip'])) $tmp['ip'] = $data['ip'];
		return $tmp;
	}

	/**
	 *
	 * @return Stat_Dao_Log
	 */
	private static function _getDao() {
		return Common::getDao("Stat_Dao_Log");
	}
}
