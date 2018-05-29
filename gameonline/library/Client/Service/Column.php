<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Service_Column
 * @author fanch
 *
 */
class Client_Service_Column {

	
	/**
	 * 获取所有列表
	 */
	public static function getAllColumn() {
		return self::_getDao()->getAll(array('sort' => 'DESC', 'id' => 'DESC'));
	}	
	
	/**
	 *
	 * 获取所有栏目.
	 */
	public function getColumnList($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;

		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC', 'id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 通过父id获取列表
	 */
	public static function getListByParentId($parent_id) {
		return self::_getDao()->getsBy(array('pid'=>$parent_id), array('sort'=>'DESC', 'id'=>'DESC'));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getColumn($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addColumn($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 * 批量添加栏目
	 * @param unknown $data
	 * @return 
	 */
	public static function batchAddColumn($data) {
		if (!is_array($data)) return false;

		return self::_getDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteColumn($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateColumn($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateColumnSort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $statu
	 * @return boolean
	 */
	public static function updateColumnStatus($data,$status) {
		if (!is_array($data)) return false;
		foreach($data as $value) {
			self::_getDao()->update(array('status' => $status), $value);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['pid'])) $tmp['pid'] = $data['pid'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}

	/**
	 *
	 * @return Client_Dao_Column
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Column");
	}
}