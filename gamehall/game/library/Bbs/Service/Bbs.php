<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Bbs_Service_Bbs{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy=array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getBy($params = array() , $orderBy=array('id'=>'DESC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getsBy($params = array(), $orderBy=array('id'=>'DESC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByID($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateSortByID($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByID($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
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
		$ret = self::_getDao()->insert($data);
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
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Sdk_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Bbs_Dao_Bbs");
	}
}
