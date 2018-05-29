<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Feedback_Service_Feedback{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderby=array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderby);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getBy($params = array(),$orderBy = array('id'=>'DESC')){
		$ret = self::getDao()->getBy($params, $orderBy);
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
	
	public static function getsBy($params = array(),$orderBy = array('id'=>'DESC')){
		$ret = self::getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByID($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}

	public static function updateBy($data, $params){
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updataSort($sorts) {
		foreach($sorts as $key=>$value) {
			self::getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGameAd($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteById($id) {
		return self::getDao()->delete(intval($id));
	}
	
	
	public static function deleteBy($params) {
		return self::getDao()->deleteBy($params);
	}
	
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret = self::getDao()->insert($data);
		if (!$ret) return $ret;
		return self::getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = $data['imcrc'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['client_version'])) $tmp['client_version'] = $data['client_version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['label_name'])) $tmp['label_name'] = $data['label_name'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['contact'])) $tmp['contact'] = $data['contact'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['version_time'])) $tmp['version_time'] = $data['version_time'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Feedback_Dao_Feedback
	 */
	private static function getDao() {
		return Common::getDao("Feedback_Dao_Feedback");
	}
}
