<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Gc_Service_LocalGoods{

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
	public static function getCanUseLocalGoods($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getCanUseLocalGoods(intval($start), intval($limit), $params);
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
	public static function getAfterLocalGoods($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getAfterLocalGoods(intval($start), intval($limit), $params);
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
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['gold_coin'])) $tmp['gold_coin'] = intval($data['gold_coin']);
		if(isset($data['silver_coin'])) $tmp['silver_coin'] = intval($data['silver_coin']);
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['supplier'])) $tmp['supplier'] = $data['supplier'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['iscash'])) $tmp['iscash'] = $data['iscash'];
		if(isset($data['stock_num'])) $tmp['stock_num'] = $data['stock_num'];
		if(isset($data['limit_num'])) $tmp['limit_num'] = $data['limit_num'];
		if(isset($data['purchase_num'])) $tmp['purchase_num'] = $data['purchase_num'];
		if(isset($data['is_new_user'])) $tmp['is_new_user'] = $data['is_new_user'];
		if(isset($data['isrecommend'])) $tmp['isrecommend'] = $data['isrecommend'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gc_Dao_LocalGoods
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_LocalGoods");
	}
}
