<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Game_Service_RecommendList
 * @author wupeng
 */
class Game_Service_H5IndexAd {
	const INDEX_AD_STATUS_OPEN = 1;
	const INDEX_AD_STATUS_CLOSE = 0;
	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllList() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	public static function getById($id) {
	    if (!intval($id)) return false;
	    return self::getDao()->get(intval($id));
	}
	
	public static function getLast() {
		$params['start_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_MINUTE));
		$params['end_time'] = array('>=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_MINUTE));
		$params['status'] = self::INDEX_AD_STATUS_OPEN;
		return self::getDao()->getBy($params, array('sort'=>'DESC', 'id'=>"DESC"));
	}

	/**
	 * 查询记录
	 * @return array
	 */
	public static function getListsBy($searchParams = array(), $sortParams = array('sort'=>'DESC', 'id'=>"DESC")) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	/**
	 * 分页查询
	 * @param int $page
	 * @param int $limit
	 * @param array $searchParams
	 * @param array $sortParams
	 * @return array
	 */	 
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort'=>'DESC', 'id'=>"DESC")) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function add($data) {
	    if (!is_array($data)) return false;
	    return self::getDao()->insert($data);
	}
	
	public static function deleteById($id) {
	    $keyParams = array('id' => $id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function updateById($data, $id) {
	    if (!is_array($data)) return false;
	    return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * @return Game_Dao_RecommendList
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_H5IndexAd");
	}
	
}
