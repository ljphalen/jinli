<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gionee_Service_News{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getNews($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	public static function getListByType($type) {
		if (!$type) return false;
		$ret = self::_getDao()->getListByType(intval($type));
		$total = self::_getDao()->count(array('type_id'=>intval($type)));
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	public static function getCanUseNews() {
		$ret = self::_getDao()->getCanUseNews();
		$total = self::_getDao()->count(array('status'=>'1'));
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTopNews($type) {
		if(!type) return false;
		$ret = self::_getDao()->where(array('istop'=>1, 'type_id'=>intval($type)));
		$total = self::_getDao()->count(array('istop'=>1, 'type_id'=>intval($type)));
		return array($total, $ret);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page -1) * $limit;
		$params = self::_cookData($params);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, 'sort');
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addNews($data) {
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

	public static function updateNews($data, $id){
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteNews($id) {
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
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['content'])) $tmp['content'] = $data['content'];
		if (isset($data['ontime'])) $tmp['ontime'] = strtotime($data['ontime']);
		if (isset($data['start_time'])) $tmp['start_time'] = strtotime($data['start_time']);
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['istop'])) $tmp['istop'] = $data['istop'];
		if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		return $tmp;
	}

	/**
	 * 
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_News");
	}
}
