<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Sensitive{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllSensitive() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC','create_time'=>'DESC'), $offset) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getCount($params = array()) {
		return self::_getDao()->count($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSensitive($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateBySensitive($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBySensitive($params,$orderBy) {
		$params = self::_cookData($params);
		$total = self::_getDao()->count($params);
		$ret =  self::_getDao()->getsBy($params,$orderBy);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBySensitive($params) {
		$ret =  self::_getDao()->getBy($params);
		return $ret;
	}
	
	public static function getsBySensitives(array $params = array()) {
		return self::_getDao()->getsBy($params, array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSensitive($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteSensitive($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 批量删除评论记录
	 * @param unknown_type $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByComments($data) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::deleteSensitive($value);
		}
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSensitive($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function addMoreSensitive($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiInsert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['stype'])) $tmp['stype'] = $data['stype'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['num'])) $tmp['num'] = $data['num'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Sensitive
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Sensitive");
	}
}
