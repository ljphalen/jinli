<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅 -- 每个游戏下载量
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_WeekRank{

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
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
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
	public static function getMostGames($limit,$date) {
		$ret = self::_getDao()->getMostGames($limit,$date);
		$total = self::_getDao()->getMostGamescount($limit,$date);
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
	 * @param unknown_type $id
	 */
	public static function getRankGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::_getDao()->getBy(array('GAME_ID'=>$game_id), array('DAY_ID'=>'DESC'));
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['DAY_ID'])) $tmp['DAY_ID'] = intval($data['DAY_ID']);
		if(isset($data['GAME_ID'])) $tmp['GAME_ID'] = $data['GAME_ID'];
		if(isset($data['DL_TIMES'])) $tmp['DL_TIMES'] = intval($data['DL_TIMES']);
		if(isset($data['CRT_TIME'])) $tmp['CRT_TIME'] = intval($data['CRT_TIME']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_WeekRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_WeekRank");
	}
}
