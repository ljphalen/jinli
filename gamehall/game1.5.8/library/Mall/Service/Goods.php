<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Mall_Service_Goods{
	const MALL_GOODS_TYPE_ENTITY = 1;
	const MALL_GOODS_TYPE_ACOUPON = 2;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('start_time'=>'DESC', 'id' => 'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getBy($params = array(),$orderBy = array('create_time'=>'DESC')) {
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getsBy($params) {
		return self::_getDao()->getsBy($params, array('start_time'=>'DESC','id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
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
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['type'])) $tmp['type'] = intval($data['type']);
		if(isset($data['total_num'])) $tmp['total_num'] = intval($data['total_num']);
		if(isset($data['preson_limit_num'])) $tmp['preson_limit_num'] = $data['preson_limit_num'];
		if(isset($data['remaind_num'])) $tmp['remaind_num'] = $data['remaind_num'];
		if(isset($data['consume_point'])) $tmp['consume_point'] = $data['consume_point'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['effect_time'])) $tmp['effect_time'] = intval($data['effect_time']);
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
