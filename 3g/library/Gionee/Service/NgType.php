<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author tiger       
 */
class Gionee_Service_NgType {
	/**
	 * Enter desNavTypeiption here ...
	 */
	public static function getAll() {
		return self::_getDao()->getAll(array('page_id'=>'ASC', 'sort'=>'ASC', 'id'=>'DESC'));
	}
	
	/**
	 * 
	 * @param unknown $pageId
	 * @return boolean
	 */
	static public function getListByPageId($pageId) {
		$list = self::_getDao()->getsBy(array('page_id' => $pageId, 'status'=>1), array('sort' => 'ASC', 'id' => 'DESC'));
		return $list;
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $id        	
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params, $orderBy) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * Enter description here ...
	 * @param string $name
	 */
	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $limit        	
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData ( $params );
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao ()->getList ( $start, $limit, $params, $orderBy );
		$total = self::_getDao ()->count ( $params );
		return array($total, $ret );
	}
	
	/**
	 *
	 * @param unknown_type $parent_id        	
	 * @return multitype:
	 */
	public static function getListByModelId($model_id) {
		return self::_getDao ()->getsBy ( array (
				'model_id' => $model_id 
		), array (
				'sort' => 'DESC',
				'id' => 'DESC' 
		) );
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao ()->insert($data);
		if (!$ret) {
			return false;
		}
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 *
	 * 批量插入
	 * 
	 * @param array $data        	
	 */
	public static function batchAddNavType($data) {
		if (!is_array($data)) return false;
		self::_getDao ()->mutiInsert ( $data );
		return true;
	}
	
	/**
	 *
	 *
	 * Enter description here ...
	 * 
	 * @param unknown_type $data        	
	 */
	public static function update($data, $id) {
		if(empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao ()->update($data, intval($id));
	}
	
	/**
	 *
	 *
	 * Enter description here ...
	 * 
	 * @param unknown_type $uid        	
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * Enter desNavTypeiption here ...
	 * @param unknown_type $data        	
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['description'])) $tmp['description'] = $data['description'];
		if (isset($data['desc_color'])) $tmp['desc_color'] = $data['desc_color'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['status'])) $tmp['status'] = intval($data['status']);
		if (isset($data['page_id'])) $tmp['page_id'] = intval($data['page_id']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Gionee_Dao_NgType
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_NgType");
	}
}