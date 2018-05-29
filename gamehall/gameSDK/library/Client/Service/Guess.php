<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅 -- 猜你喜欢
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Guess{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGuess() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('imcrc'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getGuessGames($params) {
		$params = self::_cookData($params);
		return self::_getDao()->getsBy($params,array('imcrc'=>'DESC'));
	}
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getGamesByImCrc($imei) {
		return self::_getDao()->getBy(array('imcrc'=>$imei));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGuess($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGuess($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGuess($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function turncateGuess() {
		return self::_getDao()->turncateGuess();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGuess($data) {
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
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = intval($data['imcrc']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Guess
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Guess");
	}
}
