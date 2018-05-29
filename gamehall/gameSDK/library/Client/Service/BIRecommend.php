<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅 -- 游戏推荐
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_BIRecommend{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllBIRecommend() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBIRecommend($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBIRecommend($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBIRecommend($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBIRecommend($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateBIRecommendTJ($id) {
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
		if(isset($data['GAMEC_RESOURCE_ID'])) $tmp['GAMEC_RESOURCE_ID'] = intval($data['GAMEC_RESOURCE_ID']);
		if(isset($data['GAMEC_RECOMEND_ID'])) $tmp['GAMEC_RECOMEND_ID'] = intval($data['GAMEC_RECOMEND_ID']);
		if(isset($data['CREATE_DATE'])) $tmp['CREATE_DATE'] = $data['CREATE_DATE'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_BIRecommend
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_BIRecommend");
	}
}
