<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_ResourceAssign {
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getResourceAssign($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getByModelId($model_id) {
		//if(!$model_id) return false;
		return self::_getDao()->getByModelId(intval($model_id));
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, $orderBy);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getListGroupByModelId($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getListGroupByModelId(intval($start), intval($limit), $params, 'sort');
		$total  = count(self::_getDao()->countGroupByModelId($params));
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceAssign($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach ($data as $key => $value) {
			$temp[] = array('id'          => '',
			                'series_id'   => $value['series_id'],
			                'model_id'    => $value['model_id'],
			                'resource_id' => intval($value['rid']),
			                'sort'        => $value['sort']
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
	public static function updateResourceAssign($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteResourceAssign($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteByModel($model_id) {
		//if (!$model_id) return false;
		return self::_getDao()->deleteByModel(intval($model_id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['series_id'])) $tmp['series_id'] = $data['series_id'];
		if (isset($data['model_id'])) $tmp['model_id'] = $data['model_id'];
		if (isset($data['resource_id'])) $tmp['resource_id'] = $data['resource_id'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		return $tmp;
	}


	/**
	 *
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ResourceAssign");
	}
}
