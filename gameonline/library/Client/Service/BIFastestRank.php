<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 最快榜
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_BIFastestRank{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllBIFastestRank() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
	 * @return Client_Dao_BIFastestRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_BIFastestRank");
	}
}
