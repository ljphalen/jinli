<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Dhm_Service_Brand{
	
	/**
	 * 
	 * get getList list for page
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * get LocalGoods info by id
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update LocalGoods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 * 
	 * delete LocalGoods
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add LocalGoods
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

    /**
     * @param array $params
     * @param array $sort
     * @return array|bool
     */
	public static function getsBy($params, $sort = array()) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array(), $sort = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params, $sort);
	}
	
	/**
	 * @description 统计更新
	 * @param $id
	 * @return bool|int
	 */
	public static function updateTJ($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	

	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['logo'])) $tmp['logo'] = $data['logo'];
		if(isset($data['image'])) $tmp['image'] = $data['image'];
		if(isset($data['brand_desc'])) $tmp['brand_desc'] = $data['brand_desc'];
		if(isset($data['is_top'])) $tmp['is_top'] = $data['is_top'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
		if(isset($data['country_id'])) $tmp['country_id'] = intval($data['country_id']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Dhm_Dao_Brand
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Brand");
	}
}
