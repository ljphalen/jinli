<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Jhtype {

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
	public static function getWidget($nowtime) {
		$ret = self::_getDao()->getWidget($nowtime);
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getPrevId($nowtime, $sub_time) {
		$ret = self::_getDao()->getPrevId($nowtime, $sub_time);
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getPrevWidget($prev_id) {
		$ret = self::_getDao()->getPrevWidget($prev_id);
		return $ret;
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
	public static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['source_id'])) $tmp['source_id'] = $data['source_id'];
		if (isset($data['position'])) $tmp['position'] = $data['position'];
		if (isset($data['tj_type'])) $tmp['tj_type'] = $data['tj_type'];
		if (isset($data['ad'])) $tmp['ad'] = $data['ad'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Jhtype
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Jhtype");
	}
}
