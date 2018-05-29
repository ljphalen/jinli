<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Dhm_Service_Ad{

	/**
	 * 获取广告列表
	 * @param int $page
	 * @param int $limit
	 * @param array $params
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
	 * 获取广告记录
	 * @param int $id
	 */
	public static function getAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 更新广告记录
	 * @param array $data
	 * @param int $id
	 */
	public static function updateAd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 删除广告记录
	 * @param int $id
	 */
	public static function deleteAd($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 添加广告记录
	 * @param array $data
	 */
	public static function addAd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 获取广告记录
	 * @param $params
	 * @param array $orderBy
	 * @return bool|mixed
	 */
	public static function getBy($params, $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getBy($params, $orderBy);
	}

	/**
	 * 增加广告点击量
	 * @param $params
	 * @return bool|int
	 */
	public static function clickIncrement($params){
		return self::_getDao()->increment('hits', $params);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['ad_type'])) $tmp['ad_type'] = intval($data['ad_type']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['start_time'])){
			if(is_array($data['start_time']) && isset($data['start_time'][1])){
				$data['start_time'][1] = intval($data['start_time'][1]);
			}
			$tmp['start_time'] = $data['start_time'];
		}
		if(isset($data['end_time'])){
			if(is_array($data['end_time']) && isset($data['end_time'][1])){
				$data['end_time'][1] = intval($data['end_time'][1]);
			}
			$tmp['end_time'] = $data['end_time'];
		}
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Dhm_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Ad");
	}
}
