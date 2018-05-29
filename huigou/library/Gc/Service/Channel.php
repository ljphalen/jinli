<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desChanneliption here ...
 * @author tiansh
 *
 */
class Gc_Service_Channel{

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllChannel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getChannel($id) {
		return self::_getDao()->get(intval($id));
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
	public function getChannelId() {
		return Util_Cookie::get("GouSource", true);
	}
	
	/**
	 *
	 * @param array $params
	 * @return multitype:
	 */
	public function getListBy($params = array()) {
		return self::_getDao()->getListBy($params);
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getRootList() {
		return self::_getDao()->getRootList();
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getParentList() {
		return self::_getDao()->getParentList();
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentIds($pids) {
		if (!count($pids)) return false;
		return self::_getDao()->getListByParentIds($pids);
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
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($ret);
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
	 * @param unknown_type $uid
	 */
	public static function deleteChannel($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desChanneliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		if(isset($data['root_id'])) $tmp['root_id'] = intval($data['root_id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['secret'])) $tmp['secret'] = $data['secret'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_Channel
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_Channel");
	}
}
