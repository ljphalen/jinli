<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Gc_Service_Sms{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllSms() {
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
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSms($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSms($data) {
		if (!is_array($data)) return false;
		$tmp = array();
		foreach ($data as $key=>$value) {
			$value = self::_cookData($value);
			array_push($tmp, array(
				'id'=>'',
				'tel'=>$value['tel'],
				'content'=>$value['content'],
				'status'=>$value['status'],
			));
		}
		return self::_getDao()->mutiInsert($tmp);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['tel'])) $tmp['tel'] = $data['tel'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gc_Dao_Sms
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_Sms");
	}
}
