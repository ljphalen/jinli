<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 用于APP调用本地数据的类
 */
class Gionee_Service_LocalInterface {
	
	/**
	 * 获得表中所有数据
	 * @return 数组
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
		$ret   = self::_getDao()->getList($start, $limit, $params, array('id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start    = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookParams($params);
	
		$ret   = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updates($ids, $data) {
		if (!is_array($data) || !is_array($ids)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updates('id', $ids, $data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)){
			return false;
		}
			 
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
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['info'])) $tmp['info'] = $data['info'];
		if (isset($data['v_type']))$tmp['v_type'] =$data['v_type'];

		return $tmp;
	}
	
	/**
	 *
	 * @return Gionee_Dao_HaiPuNews
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_LocalInterface");
	}
}