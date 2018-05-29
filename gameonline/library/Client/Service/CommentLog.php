<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_CommentLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params) {
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getsByLog($params) {
		return self::_getDao()->getsBy($params,array('check_time'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
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
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['comment_id'])) $tmp['comment_id'] = intval($data['comment_id']);
		if(isset($data['check_name'])) $tmp['check_name'] = $data['check_name'];
		if(isset($data['check_time'])) $tmp['check_time'] = $data['check_time'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['badwords'])) $tmp['badwords'] = $data['badwords'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = intval($data['imcrc']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['is_sensitive'])) $tmp['is_sensitive'] = $data['is_sensitive'];
		if(isset($data['is_filter'])) $tmp['is_filter'] = intval($data['is_filter']);
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['is_top'])) $tmp['is_top'] = $data['is_top'];
		if(isset($data['top_time'])) $tmp['top_time'] = $data['top_time'];
		if(isset($data['utype'])) $tmp['utype'] = $data['utype'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['is_del'])) $tmp['is_del'] = $data['is_del'];
		if(isset($data['is_blacklist'])) $tmp['is_blacklist'] = $data['is_blacklist'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_CommentLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_CommentLog");
	}
	
	/**
	 *
	 * @return Client_Dao_CommentOperatLog
	 */
	private static function _getLogDao() {
		return Common::getDao("Client_Dao_CommentOperatLog");
	}
}
