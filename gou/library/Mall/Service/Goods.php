<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Mall_Service_Goods{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllMallGoods() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param array $guideTypes
	 * @return array
	 */
	public static function getByCategorys($page, $limit, $category) {
		if (!is_array($category)) return false;
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getByCategorys(intval($start), intval($limit), $category);
		$total = self::_getDao()->getByCategorysCount($category);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getNormalMallGoods($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getNormalMallGoods(intval($start), intval($limit), $params);
		$total = self::_getDao()->getNormalMallGoodsCount($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * get MallGoods list for page
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
	 * get MallGoods info by ids
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getMallGoodsByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getMallGoodsByIds($ids);
	}
	
	/**
	 * 
	 * get MallGoods info by id
	 * @param int $id
	 */
	public static function getMallGoods($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total, $ret);
    }

    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

	/**
	 * 
	 * update MallGoods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function updateMallGoods($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function updates($ids, $data) {
		if (!is_array($data) || !is_array($ids)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updates('id', $ids, $data);
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
	 * delete MallGoods
	 * @param int $id
	 */
	public static function deleteMallGoods($id) {
		return self::_getDao()->delete(intval($id));
	}
	public static function inc($field, $params, $step=1) {
		return self::_getDao()->increment($field, $params, $step);
	}

	/**
	 * 
	 * add MallGoods
	 * @param array $data
	 */
	public static function addMallGoods($data) {
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
        if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
//		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
        if (isset($data['title'])) $tmp['title'] = $data['title'];
        if (isset($data['img'])) $tmp['img'] = $data['img'];
        if (isset($data['num_iid'])) $tmp['num_iid'] = $data['num_iid'];
        if (isset($data['price'])) $tmp['price'] = $data['price'];
        if (isset($data['hits'])) $tmp['hits'] = $data['hits'];
        if (isset($data['fav_count'])) $tmp['fav_count'] = $data['fav_count'];
        if (isset($data['commission'])) $tmp['commission'] = $data['commission'];
        if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
        if (isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
        if (isset($data['status'])) $tmp['status'] = intval($data['status']);
        if (isset($data['default_want'])) $tmp['default_want'] = intval($data['default_want']);
        if (isset($data['want'])) $tmp['want'] = intval($data['want']);
        if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Mall_Dao_Goods
	 */
	private static function _getDao() {
		return Common::getDao("Mall_Dao_Goods");
	}
}
