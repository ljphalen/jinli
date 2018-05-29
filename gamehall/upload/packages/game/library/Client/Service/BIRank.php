<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 下载最多(旧)
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_BIRank{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllBIRank() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getTopBIRank() {
		return self::_getDao()->getTopBIRank();
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('DAY_ID'=>'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBIRank($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params) {
		return  self::_getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBIRank($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBIRank($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBIRank($data) {
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
		if(isset($data['DAY_ID'])) $tmp['DAY_ID'] = intval($data['DAY_ID']);
		if(isset($data['GAME_ID'])) $tmp['GAME_ID'] = $data['GAME_ID'];
		if(isset($data['DL_SOURCE'])) $tmp['DL_SOURCE'] = $data['DL_SOURCE'];
		if(isset($data['DL_TIMES'])) $tmp['DL_TIMES'] = intval($data['DL_TIMES']);
		if(isset($data['CRT_TIME'])) $tmp['CRT_TIME'] = intval($data['CRT_TIME']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_BIRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_BIRank");
	}
}
