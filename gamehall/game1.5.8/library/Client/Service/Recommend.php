<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Recommend{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRecommend() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('ID'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getRecommendGames($params) {
		return self::_getDao()->getsBy($params,array('ID'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRecommend($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRecommend($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRecommend($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function turncateRecommend() {
		return self::_getDao()->turncateRecommend();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRecommend($data) {
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
		if(isset($data['ID'])) $tmp['ID'] = intval($data['ID']);
		if(isset($data['GAMEC_RESOURCE_ID'])) $tmp['GAMEC_RESOURCE_ID'] = intval($data['GAMEC_RESOURCE_ID']);
		if(isset($data['GAMEC_RECOMEND_ID'])) $tmp['GAMEC_RECOMEND_ID'] = intval($data['GAMEC_RECOMEND_ID']);
		if(isset($data['CREATE_DATE'])) $tmp['CREATE_DATE'] = $data['CREATE_DATE'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Recommend
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Recommend");
	}
}
