<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desGamePriceiption here ...
 * @author lichanghau
 *
 */
class Game_Service_GamePrice{

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllGamePrice() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getGamePrice($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter desGamePriceiption here ...
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
	 * @param unknown_type $data
	 */
	public static function addGamePrice($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateGamePrice($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteGamePrice($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desGamePriceiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];		
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_GamePrice
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_GamePrice");
	}
	
}
