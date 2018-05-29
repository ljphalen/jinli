<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_ForumImg{

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order_by) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order_by);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getForumImg($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getImagesByForumIds($forumids) {
		if (!is_array(($forumids))) return false;
		return self::_getDao()->getImagesByForumIds($forumids);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getImagesByForumId($forumid) {
		if (!($forumid)) return false;
		return self::_getDao()->getImagesByForumId($forumid);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateForumImg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteForumImg($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByForumId($forum_id) {
		if (!intval($forum_id)) return false;
		return self::_getDao()->deleteByForumId(intval($forum_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addForumImg($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		Common::log($data, 'test1.log');
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function batchAddForumImg($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach($data as $key=>$value) {
			$temp[] = array('id' => '',
					'forum_id' => intval($value['forum_id']),
					'img' => $value['img'],
					'sort' => $value['sort'],
					'status' => $value['status']
			);
		}
		return self::_getDao()->mutiInsert($temp);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['forum_id'])) $tmp['forum_id'] = $data['forum_id'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_ForumImg
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ForumImg");
	}
}
