<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_Goods{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGoods() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getNomalGoods($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getNomalGoods(intval($start), intval($limit), $params);
		$total = self::_getDao()->getNomalGoodsCount($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * get goods list for page
	 * @param array $params
	 * @param int $page
	 * @param int $limit
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
	 * get goods info by ids
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getGoodsByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getGoodsByIds($ids);
	}
	
	/**
	 * 
	 * get goods info by id
	 * @param int $id
	 */
	public static function getGoods($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update goods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function updateGoods($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateGoodsTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(7, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * update want set want+1
	 * @param int $id
	 * @return boolean
	 */
	public static function want(array $data) {
		if (!is_array($data)) return false;
		$ret = Gou_Service_User::want($data['uid']);
		if (!$ret) return false;
		$ret = Gou_Service_WantLog::addWantLog($data);
		if (!$ret) return false;
		return self::_getDao()->increment('want', array('id'=>intval($data['goods_id'])));
	}
	
	/**
	 * 
	 * delete goods
	 * @param int $id
	 */
	public static function deleteGoods($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add goods
	 * @param array $data
	 */
	public static function addGoods($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['subject_id'])) $tmp['subject_id'] = intval($data['subject_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['num_iid'])) $tmp['num_iid'] = $data['num_iid'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['commission'])) $tmp['commission'] = $data['commission'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['default_want'])) $tmp['default_want'] = intval($data['default_want']);
		if(isset($data['want'])) $tmp['want'] = intval($data['want']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Goods");
	}
}
