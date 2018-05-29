<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_JhColumn {
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	public static function getListByType($type) {
		if (!$type) return false;
		$ret   = self::_getDao()->getListByType(intval($type));
		$total = self::_getDao()->count(array('type_id' => intval($type)));
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	public static function getCanUseNews() {
		$ret   = self::_getDao()->getCanUseNews();
		$total = self::_getDao()->count(array('status' => '1'));
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTopNews($type) {
		if (!type) return false;
		$ret   = self::_getDao()->getBy(array('istop' => 1, 'type_id' => intval($type)));
		$total = self::_getDao()->count(array('istop' => 1, 'type_id' => intval($type)));
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNewsList($types, $limit, $params = array()) {
		if (!is_array($types)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getNewsList($types, $limit, $params);

	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array(), $orderby = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, $orderby);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
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
	public static function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}


	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAddNews($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 批量修改显示状态
	 * @param array $ids
	 * @return boolean
	 */
	public static function updateStatusByIds($ids, $status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateStatusByIds($ids, $status);
	}

	/**
	 * 批量修改置顶状态
	 * @param array $ids
	 * @return boolean
	 */
	public static function updateTopById($id, $status) {
		if (!intval($id)) return false;
		return self::_getDao()->updateTopById($id, $status);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteColumn($id) {
		return self::_getDao()->deleteColumn(intval($id));
	}


	/**
	 *
	 * Enter description here ...
	 */
	public static function deleteByType($type) {
		if (!$type) return false;
		return self::_getDao()->deleteByType(intval($type));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['parent_id'])) $tmp['parent_id'] = $data['parent_id'];
		if (isset($data['source_id'])) $tmp['source_id'] = $data['source_id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['tj_type'])) $tmp['tj_type'] = $data['tj_type'];
		if (isset($data['ad'])) $tmp['ad'] = $data['ad'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['is_recommend'])) $tmp['is_recommend'] = $data['is_recommend'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_JhColumn");
	}
}
