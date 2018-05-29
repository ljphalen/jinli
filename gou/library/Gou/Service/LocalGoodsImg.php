<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_LocalGoodsImg{

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order_by) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order_by);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGoodsImg($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getImagesByGoodsIds($goodsids) {
		if (!is_array(($goodsids))) return false;
		return self::_getDao()->getImagesByPids($goodsids);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getImagesByGoodsId($goods_id) {
		if (!($goods_id)) return false;
		return self::_getDao()->getImagesByGoodsId($goods_id);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGoodsImg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGoodsImg($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByGoodsId($goods_id) {
		if (!intval($goods_id)) return false;
		return self::_getDao()->deleteByGoodsId(intval($goods_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGoodsImg($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach($data as $key=>$value) {
			$temp[] = array('id' => '',
						'goods_id' => intval($value['goods_id']),
						'img' => $value['img']
					);
		}
		$ret = self::_getDao()->mutiInsert($temp);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId(); 
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gionee_Dao_ProductImg
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_LocalGoodsImg");
	}
}
