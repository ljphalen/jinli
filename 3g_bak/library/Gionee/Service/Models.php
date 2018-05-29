<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Gionee_Service_Models {

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllModels() {
		return array(self::_getDao()->count(), self::_getDao()->getAllModels(array('sort'=>'DESC', 'id'=>'DESC')));
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('sort' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getModels($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getModelsByName($name) {
		if (!$name) return false;
		
		$rc       = Common::getCache();
		$rcKey    = 'GIONEE_MODELS:'.$name;
		$model = $rc->get($rcKey);
		if (empty($model)) {
			$model = self::_getDao()->getModelsByName($name);
			$rc->set($rcKey, $model, 3600);
		}
		
		return $model;
	}

	/**
	 *
	 * Enter description here ...
	 * @param int $series_id
	 */
	public static function getListBySeriesId($series_id) {
		if (!intval($series_id)) return false;
		$ret   = self::_getDao()->getListBySeriesId(intval($series_id));
		$total = self::_getDao()->count(array('series_id' => intval($series_id)));
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateModels($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteModels($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addModels($data) {
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
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['series_id'])) $tmp['series_id'] = $data['series_id'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Models
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Models");
	}
}
