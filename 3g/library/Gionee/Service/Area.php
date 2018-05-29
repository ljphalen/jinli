<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter desAreaiption here ...
 * @author tiansh
 *
 */
class Gionee_Service_Area {


	/**
	 *
	 * Enter desAreaiption here ...
	 */
	public static function getAllArea() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getArea($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $name
	 */

	public static function getByName($name) {
		return self::_getDao()->getByName(trim($name));
	}

	public static function count($params){
		return self::_getDao()->count($params);
	}
	
	/**
	 *
	 * Enter desAreaiption here ...
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
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentId($parent_id) {
		return self::_getDao()->getListByParentID($parent_id);
	}

	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getAllCity() {
		return self::_getDao()->getAllCity();
	}

	public static function getsBy($params =array(),$order=array()){
		return self::_getDao()->getsBy($params,$order);
	}
	/**
	 *
	 * @param unknown_type $Date
	 * @return multitype:
	 */
	public static function getProvinceList() {
		return self::_getDao()->getProvinceList();
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addArea($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAddArea($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updateArea($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteArea($id) {
		return self::_getDao()->delete(intval($id));
	}

	
	/**
	 * 根据ID获得当前城市信息
	 *
	 */
	public static  function   getCityNameByIds($params){
		if(!is_array($params)) return false;
		$area = array();
		$data = Gionee_Service_Area::getsBy($params,array('id'=>"ASC"));
		foreach ($data as $v){
			$temp = $v['parent_id']>0?'city':'province';
			$area[$temp]  = array('id'=>$v['id'],'name'=>$v['name'],'parent_id'=>$v['parent_id']);
		}
		return $area;
	}
	/**
	 *
	 * Enter desAreaiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		return $tmp;
	}


	/**
	 *
	 * @return Gionee_Dao_Area
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Area");
	}
}
