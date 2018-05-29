<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Rank{

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
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getMostGames($limit,$date, $filter=array()) {
		$ret = self::_getDao()->getMostGames($limit,$date, $filter);
		$total = self::_getDao()->getMostGamescount($limit,$date, $filter);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getsBy($params) {
		return self::_getDao()->getsBy($params, array('DL_TIMES'=>'DESC','CRT_TIME'=>'DESC'));
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
	 * @param unknown_type $id
	 */
	public static function deleteRank($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRank($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateRankTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 新游排行
	 */
	public static function newRank($page = 1, $limit = 10){
		
	}
	
	/**
	 * 上升排行
	 */
	public static function upRank($page = 1, $limit = 10){
	
	}
	
	/**
	 * 周排行
	 */
	public static function weekRank($page = 1, $limit = 10){
	
	}
	
	/**
	 * 月排行
	 */
	public static function monthRank($page = 1, $limit = 10){
	
	}
	
	/**
	 * 网游排行
	 */
	public static function onlineRank($page = 1, $limit = 10){
	
	}
	
	/**
	 * 单机排行
	 */
	public static function pcRank($page = 1, $limit = 10){
	
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
	 * @return Client_Dao_Rank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Rank");
	}
}
