<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljphalen@163.com
 *
 */
class User_Service_UserLoginLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateAdTJ($id) {
		if (!$id) return false;
		return self::getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getById($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params, $orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateById($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteById($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret = self::getDao()->insert($data);
		if(!$ret) return false;
		return self::getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['logid'])) $tmp['logid'] = $data['logid'];
		if(isset($data['userid'])) $tmp['userid'] = $data['userid'];
		if(isset($data['serverid'])) $tmp['serverid'] = $data['serverid'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['phoneinfo'])) $tmp['phoneinfo'] = $data['phoneinfo'];
		if(isset($data['marketid`'])) $tmp['marketid`'] = $data['marketid`'];
		if(isset($data['marketsubid'])) $tmp['marketsubid'] = $data['marketsubid'];
		if(isset($data['marketuserid'])) $tmp['marketuserid'] = $data['marketuserid'];
		if(isset($data['usernick'])) $tmp['usernick'] = $data['usernick'];
		if(isset($data['jinbi'])) $tmp['jinbi'] = $data['jinbi'];
		if(isset($data['lastplayip'])) $tmp['lastplayip'] = $data['lastplayip'];
		if(isset($data['lastplaydatetime'])) $tmp['lastplaydatetime'] = $data['lastplaydatetime'];
		if(isset($data['tianfu'])) $tmp['tianfu'] = $data['tianfu'];
		if(isset($data['jingli'])) $tmp['jingli'] = $data['jingli'];
		if(isset($data['heroinbound'])) $tmp['heroinbound'] = $data['heroinbound'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Payment_Dao_PayFlowLog
	 */
	private static function getDao() {
		return Common::getDao("User_Dao_UserLoginLog");
	}
}