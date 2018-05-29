<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Gou_Service_LocalGoods{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllLocalGoods() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getNomalLocalGoods($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getNomalLocalGoods(intval($start), intval($limit), $params);
		$total = self::_getDao()->getNomalLocalGoodsCount($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getCanUseLocalGoods($page, $limit, $params = array(),array $orderBy = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseLocalGoods(intval($start), intval($limit), $params, $orderBy);
		$total = self::_getDao()->getCanUseLocalGoodsCount($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return multitype:unknown
	 */
	public static function getAfterLocalGoods($page, $limit, $params = array(),array $orderBy = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getAfterLocalGoods(intval($start), intval($limit), $params, $orderBy);
		$total = self::_getDao()->getAfterLocalGoodsCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 更新商品库存
	 * @param int $goods_id
	 * @param int $num
	 * @return boolean
	 */
	public static function updatePurchaseNum($goods_id, $num) {
		if (!$goods_id || !$num) return false;
		return self::_getDao()->updatePurchaseNum($goods_id, $num);
	}
	
	/**
	 * 减商品库存
	 * @param int $goods_id
	 * @param int $num
	 * @return boolean
	 */
	public static function minusStock($goods_id, $num) {
		if (!$goods_id || !$num) return false;
		return self::_getDao()->minusStock($goods_id, $num);
	}
	
	/**
	 * 加商品库存
	 * @param int $goods_id
	 * @param int $num
	 * @return boolean
	 */
	public static function addStock($goods_id, $num) {
		if (!$goods_id || !$num) return false;
		return self::_getDao()->addStock($goods_id, $num);
	}
	
	/**
	 * 
	 * get LocalGoods list for page
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
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 *
	 * get LocalGoods info by ids
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getLocalGoodsByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getLocalGoodsByIds($ids);
	}
	
	/**
	 * 
	 * get LocalGoods info by id
	 * @param int $id
	 */
	public static function getLocalGoods($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update LocalGoods by id
	 * @param array $data
	 * @param int $id
	 */
	public static function updateLocalGoods($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 * 
	 * delete LocalGoods
	 * @param int $id
	 */
	public static function deleteLocalGoods($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add LocalGoods
	 * @param array $data
	 */
	public static function addLocalGoods($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function parise($id) {
	    if (!$id) return false;
	    return self::_getDao()->increment('praise', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function checkParise($id) {
	    if (!$id) return false;
	    $cookie = json_decode(Util_Cookie::get('GOU-PRAISE', true), true);
	    if(in_array('amigo_goods_'.$id, $cookie)) return true;
	    return false;
	}

	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['short_desc'])) $tmp['short_desc'] = $data['short_desc'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['gold_coin'])) $tmp['gold_coin'] = intval($data['gold_coin']);
		if(isset($data['silver_coin'])) $tmp['silver_coin'] = $data['silver_coin'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['supplier'])) $tmp['supplier'] = $data['supplier'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['iscash'])) $tmp['iscash'] = $data['iscash'];
		if(isset($data['is_sale'])) $tmp['is_sale'] = $data['is_sale'];
		if(isset($data['is_praise'])) $tmp['is_praise'] = $data['is_praise'];
		if(isset($data['stock_num'])) $tmp['stock_num'] = $data['stock_num'];
		if(isset($data['limit_num'])) $tmp['limit_num'] = $data['limit_num'];
		if(isset($data['purchase_num'])) $tmp['purchase_num'] = $data['purchase_num'];
		if(isset($data['is_new_user'])) $tmp['is_new_user'] = $data['is_new_user'];
		if(isset($data['isrecommend'])) $tmp['isrecommend'] = $data['isrecommend'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['goods_type'])) $tmp['goods_type'] = $data['goods_type'];
		if(isset($data['show_type'])) $tmp['show_type'] = $data['show_type'];
		if(isset($data['keywords'])) $tmp['keywords'] = $data['keywords'];
		if(isset($data['img_thumb'])) $tmp['img_thumb'] = $data['img_thumb'];
		
		if(isset($data['doing'])) $tmp['doing'] = $data['doing'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_LocalGoods
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_LocalGoods");
	}
}
