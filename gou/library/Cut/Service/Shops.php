<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cut_Service_Shops{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllShops() {
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
        $params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getShops($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateShops($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteShops($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addShops($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$data = self::_cookData($params);
		return self::_getDao()->getBy($data);
	}

    /**
     * @param $params
     * @param array $sort
     * @return bool|mixed
     */
    public static function getsBy($params,$sort=array()) {
		if (!is_array($params)) return false;
		$total = self::_getDao()->count($params);
		$ret = self::_getDao()->getsBy($params,$sort);
		return array($total,$ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateShopTJ($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(15, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}

    /**
     * @description 收藏量统计
     * @param $id
     * @return bool|int
     */
    public static function updateFavCount($item_id,$step=1) {
		if (!$item_id||!in_array($step,array(1,-1))) return false;
		return self::_getDao()->increment('favorite_count', array('shop_id'=>intval($item_id),'channel_id'=>2),$step);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['shop_title'])) $tmp['shop_title'] = $data['shop_title'];
		if(isset($data['shop_url']))   $tmp['shop_url'] = $data['shop_url'];
		if(isset($data['shop_type']))  $tmp['shop_type'] = $data['shop_type'];
		if(isset($data['goods_imgs'])) $tmp['goods_imgs'] = $data['goods_imgs'];
        if(isset($data['goods_ids'])) $tmp['goods_ids'] = $data['goods_ids'];
        if(isset($data['logo'])) $tmp['logo'] = $data['logo'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['shop_id'])) $tmp['shop_id'] = intval($data['shop_id']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Cut_Dao_Shops
	 */
	private static function _getDao() {
		return Common::getDao("Cut_Dao_Shops");
	}
}
