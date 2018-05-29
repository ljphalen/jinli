<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 */
class Admin_Service_Group {

	public static function getGroup($groupid) {
		$result           = self::_getDao()->get(intval($groupid));
		$result['rvalue'] = json_decode($result['rvalue'], true);
		return $result;
	}

	public static function addGroup($data) {
		if (!is_array($data)) return false;
		$data['createtime'] = Common::getTime();
		$data               = self::_cookData($data);
		return self::_getDao()->addGroup($data);
	}


	public static function updateGroup($data, $groupid) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($groupid));
	}


	public static function getAllGroup() {
		return self::_getDao()->getGroups();
	}

	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param int $groupid
	 */
	public static function deleteGroup($groupid) {
		return self::_getDao()->deleteGroup(intval($groupid));
	}

	/**
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('name', 'descrip', 'rvalue', 'createtime');
		$tmp    = Common::cookData($data, $fields);
		if (isset($data['rvalue'])) {
			$tmp['rvalue'] = json_encode($data['rvalue']);
		}
		$tmp['ifdefault'] = 1;
		return $tmp;
	}

	/**
	 *
	 * @return Admin_Dao_Group
	 */
	private static function _getDao() {
		return Common::getDao("Admin_Dao_Group");
	}
}