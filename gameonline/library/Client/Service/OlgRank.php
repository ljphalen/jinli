<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 网游榜
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_OlgRank{
	
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('rank_rate'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getLastDayId() {
		$ret = self::_getDao()->getLastDayId();
		return $ret[0]['day_id'];
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
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['day_id'])) $tmp['day_id'] = intval($data['day_id']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['rank_rate'])) $tmp['rank_rate'] = $data['rank_rate'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_OlgRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_OlgRank");
	}
}
