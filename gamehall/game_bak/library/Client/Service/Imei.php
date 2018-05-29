<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Imei{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllImei() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getCanUseImeis($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseImeis(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseImeiCount($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
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
	public static function getImei($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getImeiByImei($m_id) {
		if (!intval($m_id)) return false;
		return self::_getDao()->getBy(array('m_id'=>$m_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateImei($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateImeiTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function replaceImei($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->replace($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteImei($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addImei($data) {
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
		if(isset($data['m_id'])) $tmp['m_id'] = intval($data['m_id']);
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Imei
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Imei");
	}
}
