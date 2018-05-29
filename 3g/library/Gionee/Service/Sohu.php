<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiger
 *
 */
class Gionee_Service_Sohu {
	static $positions = array(
		'1' => '顶部站点',
		'2' => '轮播图片',
		'4' => '文字链1',
		'5' => '推荐广告',
		'6' => '文字链2',
		'7' => '文字链3',
		'8' => '文字链4',
		'9' => '底部站点',
	);

	static $BiColumnName = array(
		4 => array(1=>'要闻',2=>'社会',3=>'财经',4=>'独家'),
		6 => array(1=>'军事',2=>'历史',3=>'科技',4=>'汽车'),
		7 => array(1=>'体育',2=>'娱乐',3=>'女人',4=>'星座'),
		8 => array(1=>'购物',2=>'关注'),
	);
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseAds($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if (intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getCanUseAds(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseAdCount($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateAd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAd($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['position'])) $tmp['position'] = $data['position'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['pic'])) $tmp['pic'] = $data['pic'];
		if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if (isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['attribute'])) $tmp['attribute'] = $data['attribute'];
		if (isset($data['partner_id'])) $tmp['partner_id'] = $data['partner_id'];
		if (isset($data['color']))		$tmp['color'] = $data['color'];
		if (isset($data['cp_id']))		$tmp['cp_id'] = $data['cp_id'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Sohu
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Sohu");
	}
}