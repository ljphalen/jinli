<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desChanneliption here ...
 * @author tiansh
 *
 */
class Gou_Service_Channel{
	

	/**
	 *
	 * Enter desChanneliption here ...
	 */
	public static function getAllChannel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * cookie a channel
	 */
	public static function cookieChannel($source) {
		$channel_id = Common::encrypt($source, 'DECODE');
		$ret = self::getChannel($channel_id);
		if ($source && $channel_id && $ret) {
			Util_Cookie::set("GouSource", $channel_id, true, Common::getTime() + 86400, '/');
		} else {
			Util_Cookie::set("GouSource", 0, true, Common::getTime() + 86400, '/');
		} 
	}
	
	/**
	 * 获取channel_id
	 * @return Ambigous <mixed, boolean, string>
	 */
	public static function getChannelId() {
		return Util_Cookie::get("GouSource", true);
	}
	
	/**
	 *
	 * Enter desChanneliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseChanels($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseChanels(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseChanelCount($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addChannel($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getChannel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateChannel($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateChannelTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(3, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteChannel($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * @param $version_id
	 * @param $value
	 * @return 获取统计的短链接
	 */
	public static function getShortUrl($version_id, $value) {
		list($model_id, $channel_id) = explode('_', $value['module_channel']);
		return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $value['link'], $value['name'], $value['channel_code']);
	}
	
	/**
	 * sort
	 * @param array $sort
	 * @return boolean
	 */
	public static function sort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::_getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 *
	 * Enter desChanneliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		if(isset($data['is_open'])) $tmp['is_open'] = intval($data['is_open']);
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		if(isset($data['module_id']) && isset($data['cid'])) $tmp['module_channel'] = intval($data['module_id']).'_'.intval($data['cid']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_Channel
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Channel");
	}
}
