<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_Subject {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSubject() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseSubjects($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if (intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getCanUseSubjects(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseSubjectCount($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSubject($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSubject($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteSubject($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSubject($data) {
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
		if (isset($data['channel'])) $tmp['channel'] = $data['channel'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['content'])) $tmp['content'] = $data['content'];
		if (isset($data['hide_title'])) $tmp['hide_title'] = $data['hide_title'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Subject
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Subject");
	}
}
