<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Service_Column
 * @author luojiapeng
 *
 */
class Client_Service_ColumnNew {

	
	/**
	 * 获取所有列表
	 */
	public static function getAllColumn() {
		return self::_getDao()->getAll(array('pid' => 'ASC'));
	}	
	
	/**
	 *
	 * 获取所有栏目.
	 */
	public function getColumnList($page, $limit, $params = array(),$orderby = array('position'=>'ASC', 'id' => 'DESC')) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, $orderby);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 通过父id获取列表
	 */
	public static function getListByParentId($parent_id) {
		return self::_getDao()->getsBy(array('pid'=>$parent_id), array('position'=>'ASC', 'id'=>'DESC'));
	}
	
	
	/**
	 * 获取一级栏目和二级频道
	 */
	public static function getParenList() {
		return self::_getDao()->getsBy(array('pid'=>array('NOT IN',array('6'))), array('position'=>'ASC', 'id'=>'DESC'));
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
	 * @param unknown_type $params
	 * @return boolean
	 */
	public static function updateColumnBywhere($data,$params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	

	
	/**
	 * 通过条件获取
	 */
	public static function getListBywhere($params) {
		return self::_getDao()->getsBy($params,array('pid' => 'ASC','position'=>'ASC'));
	}
	
	/**
	 * 获取数量
	 * @param unknown_type $params
	 * @return string
	 */
	public static function getColumnCount($params) {
		return 	 self::_getDao()->count($params);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['position'])) $tmp['position'] = $data['position'];
		if (isset($data['channel_type'])) $tmp['channel_type'] = $data['channel_type'];
		if (isset($data['pid'])) $tmp['pid'] = $data['pid'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['icon_path'])) $tmp['icon_path'] = $data['icon_path'];
		if (isset($data['icon_default'])) $tmp['icon_default'] = $data['icon_default'];
		if (isset($data['default_open'])) $tmp['default_open'] = $data['default_open'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if (isset($data['relevance'])) $tmp['relevance'] = $data['relevance'];
		if (isset($data['show_type'])) $tmp['show_type'] = $data['show_type'];
		if (isset($data['level'])) $tmp['level'] = $data['level'];
		return $tmp;
	}
	

	/**
	 *
	 * @return Client_Dao_ColumnNew
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_ColumnNew");
	}
}