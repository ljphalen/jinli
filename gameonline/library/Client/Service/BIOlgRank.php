<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 网游榜
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_BIOlgRank{

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
	 * @return Client_Dao_BIOlgRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_BIOlgRank");
	}
}
