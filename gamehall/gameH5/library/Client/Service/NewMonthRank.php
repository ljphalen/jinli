<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 月榜(搜索使用)
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_NewMonthRank{

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
		$ret = self::_getDao()->getList($start, $limit, $params, array('DAY_ID'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getLastDayId() {
		$ret = self::_getDao()->getLastDayId();
		return $ret[0]['DAY_ID'];
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
		if(isset($data['DAY_ID'])) $tmp['DAY_ID'] = intval($data['DAY_ID']);
		if(isset($data['GAME_ID'])) $tmp['GAME_ID'] = $data['GAME_ID'];
		if(isset($data['DL_TIMES'])) $tmp['DL_TIMES'] = intval($data['DL_TIMES']);
		if(isset($data['CRT_TIME'])) $tmp['CRT_TIME'] = $data['CRT_TIME'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_NewMonthRank
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_NewMonthRank");
	}
}
