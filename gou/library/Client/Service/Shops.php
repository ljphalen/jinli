<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Client_Service_Shops{

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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
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
	 * sort
	 * @param array $sort
	 * @return boolean
	 */
	public static function sort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::_getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['nick'])) $tmp['nick'] = $data['nick'];
		if(isset($data['city'])) $tmp['city'] = $data['city'];
		if(isset($data['shop_title'])) $tmp['shop_title'] = $data['shop_title'];
		if(isset($data['shop_url'])) $tmp['shop_url'] = $data['shop_url'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['description'])) $tmp['description'] = $data['description'];
		if(isset($data['goods_img'])) $tmp['goods_img'] = $data['goods_img'];
		if(isset($data['logo'])) $tmp['logo'] = $data['logo'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['shop_type'])) $tmp['shop_type'] = intval($data['shop_type']);
		if(isset($data['shop_id'])) $tmp['shop_id'] = intval($data['shop_id']);
		if(isset($data['tag_id'])) $tmp['tag_id'] = $data['tag_id'];
		if(isset($data['favorite_count'])) $tmp['favorite_count'] = intval($data['favorite_count']);
		if(isset($data['channel_id'])) $tmp['channel_id'] = intval($data['channel_id']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Shops
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Shops");
	}
}
