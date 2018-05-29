<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_RankResult{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRank() {
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
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('DL_TIMES'=>'DESC','GAME_ID'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRank($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRank($data, $id) {
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
	public static function updateBy($data = array(), $params = array()) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRank($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	public static function deleteBy($params) {
	    if (!is_array($params)) return false;
	    return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRank($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getLastDayId() {
		$ret = self::_getDao()->getLastDayId();
		return $ret[0]['DAY_ID'];
	}
	
	public static function getBy($params,$orderBy = array()) {
	    if (!is_array($params)) return false;
	    return self::_getDao()->getBy($params,$orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['DAY_ID'])) $tmp['DAY_ID'] = intval($data['DAY_ID']);
		if(isset($data['GAME_ID'])) $tmp['GAME_ID'] = $data['GAME_ID'];
		if(isset($data['DL_TIMES'])) $tmp['DL_TIMES'] = intval($data['DL_TIMES']);
		if(isset($data['CRT_TIME'])) $tmp['CRT_TIME'] = $data['CRT_TIME'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_RankResult
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_RankResult");
	}
}
