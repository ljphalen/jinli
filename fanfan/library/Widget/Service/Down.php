<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Down {

	/**
	 * 获取机型列表
	 */
	public static function getAll($orderBy = array()) {
		static $list = null;

		if ($list == null) {
			$tmpList = self::_getDao()->getAll($orderBy);
			$list    = array(
				0 => '默认'
			);
			foreach ($tmpList as $val) {
				$list[$val['id']] = $val['name'];
			}
		}

		return $list;
	}

	/**
	 *
	 * 下载资源列表
	 * @param int $page
	 * @param int $limit
	 * @param int $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('created_at' => 'DESC', 'id' => 'DESC'));
		return $ret;
	}


	/**
	 *
	 * 下载资源列表
	 * @param int $page
	 * @param int $limit
	 * @param int $params
	 */
	public static function getAllCp() {
		$cps = Widget_Service_Cp::filterCpId();
		foreach ($cps as $cpId => $cpVal) {
			$list[$cpId] = self::_getDao()->getBy(array('cp_id' => $cpId));
		}
		return $list;
	}


	/**
	 * 下载资源数量
	 * @param array $params
	 * @return string
	 */
	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getByCpId($cpId) {
		if (empty($cpId)) {
			return array();
		}

		$cpId    = intval($cpId);
		$rcKey = Common::KN_WIDGET_CP_DOWN . $cpId;
		$info  = Common::getCache()->get($rcKey);
		if (empty($info)) {
			$info = Widget_Service_Down::getBy(array('cp_id' => intval($cpId)));
			Common::getCache()->set($rcKey, $info, Common::KT_CP_INFO);
		}
		return $info;
	}

	public static function cleanCache($cpId) {
		$rcKey = Common::KN_WIDGET_CP_DOWN . $cpId;
		Common::getCache()->delete($rcKey);
	}

	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params, $orderBy = array()) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	public static function set($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		self::cleanCache($data['cp_id']);
		return self::_getDao()->update($data, intval($id));
	}

	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	public static function all() {
		$list = self::_getDao()->getAll();
		return $list;
	}


	/**
	 *
	 * 字段过滤
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('name', 'cp_id', 'desc_s', 'desc', 'tip', 'url', 'size', 'icon', 'pic', 'company', 'created_at', 'mark');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}


	/**
	 * @return Widget_Dao_Down
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Down");
	}
}
