<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiansh
 *
 */
class Gionee_Service_ElifeServerImages {

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
	 * @param unknown_type $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getsBy($elife_id) {
		$ret   = self::_getDao()->getsBy(array('elife_id' => $elife_id), array('id' => 'DESC'));
		$total = self::_getDao()->count(array('elife_id' => $elife_id));
		return array($total, $ret);
	}

	public static function getByName($name) {
		return self::_getDao()->getBy(array('name' => $name));
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
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
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentId($parent_id) {
		return self::_getDao()->getsBy(array('parent_id' => $parent_id), array('sort' => 'DESC', 'id' => 'DESC'));
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getCanUseListByTypeId($type_id) {
		return self::_getDao()->getsBy(array('status' => '1'), array('sort' => 'DESC', 'id' => 'DESC'));
	}


	/**
	 *
	 * get parentList
	 * @return multitype:
	 */
	public static function getAllType() {
		return self::_getDao()->getAllType();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addImg($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach ($data as $key => $value) {
			$temp[] = array(
				'id'       => '',
				'elife_id' => intval($value['elife_id']),
				'img'      => $value['img'],
			);
		}
		$ret = self::_getDao()->mutiInsert($temp);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
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
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete($id);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateImg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteImgs($elife_id) {
		return self::_getDao()->deleteImgs($elife_id);
	}

	/**
	 *
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['elife_id'])) $tmp['elife_id'] = $data['elife_id'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_ElifeServerImages
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ElifeServerImages");
	}
}
