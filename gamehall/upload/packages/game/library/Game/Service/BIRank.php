<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Game_Service_BIRank{

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
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('DL_TIMES'=>'DESC'));
		$total = self::_getDao()->count();
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
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
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
		if(isset($data['DAY_ID'])) $tmp['DAY_ID'] = $data['DAY_ID'];
		if(isset($data['GAME_ID'])) $tmp['GAME_ID'] = $data['GAME_ID'];
		if(isset($data['DL_SOURCE'])) $tmp['DL_SOURCE'] = $data['DL_SOURCE'];
		if(isset($data['DL_TIMES'])) $tmp['DL_TIMES'] = $data['DL_TIMES'];
		if(isset($data['CRT_TIME'])) $tmp['CRT_TIME'] = $data['CRT_TIME'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Game_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_BIRank");
	}
}
