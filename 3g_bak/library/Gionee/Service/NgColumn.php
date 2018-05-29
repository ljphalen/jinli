<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Gionee_Service_NgColumn
 */
class Gionee_Service_NgColumn {

	static $styles = array(
		'img1'       => '横幅图片',
		'baidu'      => '百度热词',
		'hot_nav'    => '热门导航',
		'link'       => '新闻链接',
		'hotlink'    => '热度推荐',
		'img5'       => '五列图片',
		'img4'       => '四列图片',
		'img3'       => '三列图片',
		'img2'       => '两列图片',
		'words3'     => '三列热词',
		'words4'     => '四列热词',
		'words5'     => '五列热词',
		'like'       => '猜你喜欢',
		'bread'      => '栏目条',
		'lottery'    => '彩票',
		'img3switch' => '三列图片切换',
		'news_list'  => '新闻列表',
		'ticket'     => '火车票',
		'topic'      => '专题',
	);

	public static function getAll() {
		return self::_getDao()->getAll(array('sort' => 'ASC', 'id' => 'ASC'));
	}

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * @param int $id
	 */
	public static function getColumn($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 * get by
	 */
	public static function getBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}

	/**
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
	}


	/**
	 * @param int   $page
	 * @param int   $limit
	 * @param array $params
	 * @param array $orderBy
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
	 * @param array $data
	 */
	public static function addColumn($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 批量插入
	 *
	 * @param array $data
	 */
	public static function batchAddNavType($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	public static function updateColumn($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * Enter description here ...
	 *
	 * @param unknown_type $uid
	 */
	public static function deleteColumn($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * @param array
	 *
	 * @return int Total column Number
	 */
	public static function count($param) {
		return self::_getDao()->count($param);
	}

	/**
	 *
	 *
	 * Enter desNavTypeiption here ...
	 *
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['style'])) $tmp['style'] = $data['style'];
		if (isset($data['more'])) $tmp['more'] = $data['more'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['status'])) $tmp['status'] = intval($data['status']);
		if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_NgColumn
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_NgColumn");
	}
}
