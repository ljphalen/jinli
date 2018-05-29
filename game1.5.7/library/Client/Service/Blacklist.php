<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Blacklist{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllBlacklist() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * 后台根据账号搜索
	 *
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsearchList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
	
		$sqlWhere = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBlacklist($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateBlacklistStatus($data,$statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::_getDao()->update(array('status'=>$statu), $value);
		}
		return $ret;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsByBlacklist($params,$orderBy) {
		$ret =  self::_getDao()->getsBy($params,$orderBy);
		return $ret;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByBlacklist($params) {
		$ret =  self::_getDao()->getBy($params);
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBlacklist($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBlacklist($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBlacklist($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['utype'])) $tmp['utype'] = $data['utype'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = $data['imcrc'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Blacklist
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Blacklist");
	}
}
