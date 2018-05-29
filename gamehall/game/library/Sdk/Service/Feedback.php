<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Sdk_Service_Feedback{

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
	public static function getList($page = 1, $limit = 10, $params = array(), $orderby=array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderby);
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
	
	public static function getBy($params = array(),$orderBy = array('id'=>'DESC')){
		$ret = self::_getDao()->getBy($params, $orderBy);
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
		$ret = self::_getDao()->getsBy($params, $orderBy);
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

	public static function updateBy($data, $params){
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortAd($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
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
			self::_getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAd($id) {
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
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = $data['imcrc'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['reply_time'])) $tmp['reply_time'] = $data['reply_time'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['label_name'])) $tmp['label_name'] = $data['label_name'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['reply_name'])) $tmp['reply_name'] = $data['reply_name'];
		if(isset($data['reply_content'])) $tmp['reply_content'] = $data['reply_content'];
		if(isset($data['version_time'])) $tmp['version_time'] = $data['version_time'];
		if(isset($data['tel'])) $tmp['tel'] = $data['tel'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		if(isset($data['tag'])) $tmp['tag'] = $data['tag'];
		if(isset($data['attach'])) $tmp['attach'] = $data['attach'];
		if(isset($data['qq'])) $tmp['qq'] = $data['qq'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Sdk_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Sdk_Dao_Feedback");
	}
}
