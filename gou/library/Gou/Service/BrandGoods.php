<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class Gou_Service_BrandGoods extends Common_Service_Base {
	/**
	 * 获取品牌商品数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取单条品牌商品数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getBrandgoods($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 根据淘宝num_iid检查商品是否已经存在
	 * @param unknown $num_iid
	 * @return boolean|string
	 */
	public static function checkBrandGoodsByNumiid($num_iid){
		if (!intval($num_iid)) return false;
		$rs = self::_getDao()->count(array('num_iid'=>$num_iid));
		return !empty($rs) ? true : false;
	}
	
	/**
	 * 添加品牌商品数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addBrandgoods($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['time_line'] = time();
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改品牌商品数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBrandgoods($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
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
	 * 更新商品访问统计信息
	 * @param unknown $brand_id
	 */
	public static function updateHits($goods_id){
		if (empty($goods_id)) return false;
		Gou_Service_ClickStat::increment(21, $goods_id);
		return self::_getDao()->increment('hits', array('id'=>intval($goods_id)));
	}
	
	/**
	 * 删除品牌商品数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteBrandgoods($id){
		return self::_getDao()->delete(intval($id));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['brand_id'])) $tmp['brand_id'] = intval($data['brand_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['num_iid'])) $tmp['num_iid'] = $data['num_iid'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Gou_Dao_BrandGoods
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_BrandGoods");
	}
}