<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author huwei
 *
 */
class Gionee_Service_RecWebsite {

	static $groupNames = array(
		'新闻','阅读','视频','游戏','购物'
	);

	/**
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret = self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}


	public static function getListByType($val) {
		$rcKey = '3G_RECWEBSITE:'.$val;
		$list = Common::getCache()->get($rcKey);
		if (empty($list)) {
			$where = array('status' => 1, 'type' => $val);
			$order = array('sort' => 'ASC');
			$list  = Gionee_Service_RecWebsite::getsBy($where, $order);
			if (!empty($list)) {
				Common::getCache()->set($rcKey, $list, 3600);
			}
		}
		return $list;
	}
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret = self::_getDao()->getBy($params, $orderBy);
		return $ret;
	}

	public static function getTotal($params) {
		if (!is_array($params)) return false;
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		return $ret;
	}

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $data
	 * @param array $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * @param array $data
	 * @param array $id
	 */
	public static function updates($ids, $data) {
		if (!is_array($data) || !is_array($ids)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 *
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	public static function deleteBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}


	public static function deletes($ids, $data) {
		if (!is_array($data) || !is_array($ids)) return false;
		return self::_getDao()->deletes('id', $ids, $data);
	}

	/**
	 *
	 * @param array $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}


	/**
	 *
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['created_at'])) $tmp['created_at'] = $data['created_at'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['group_name'])) $tmp['group_name'] = $data['group_name'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_RecWebsite
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_RecWebsite");
	}

}
